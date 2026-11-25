<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\GradeAdjustment;
use App\Models\Offering;
use App\Models\OfferingTask;
use App\Models\TaskGrade;
use App\Models\User;
use App\Support\AcademicConfigSingleton;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TaskGradingService
{
    public function __construct(private readonly NotificationService $notifications) {}

    public function assertTeacherOwnsOffering(User $teacher, Offering $offering): void
    {
        if (! $teacher->isTeacher() || (int) $offering->teacher_id !== (int) $teacher->id) {
            abort(403, 'Solo el docente titular asignado a esta oferta puede gestionar sus tareas y calificaciones.');
        }
    }

    public function createTask(Offering $offering, User $teacher, array $data): OfferingTask
    {
        $this->assertTeacherOwnsOffering($teacher, $offering);

        if ($offering->isManuallyClosed() || $offering->is_disabled) {
            throw ValidationException::withMessages([
                'title' => 'No se pueden crear tareas en una oferta cerrada o deshabilitada.',
            ]);
        }

        $maxPoints = (int) $data['max_points'];
        $currentTotal = (int) $offering->tasks()->sum('max_points');

        if ($currentTotal + $maxPoints > 100) {
            $remaining = max(0, 100 - $currentTotal);
            throw ValidationException::withMessages([
                'max_points' => "La suma de puntajes de las tareas no puede superar 100 puntos. Puntos disponibles: {$remaining}.",
            ]);
        }

        return OfferingTask::create([
            'offering_id' => $offering->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'max_points' => $maxPoints,
            'due_date' => $data['due_date'],
            'created_by' => $teacher->id,
        ]);
    }

    public function updateTask(OfferingTask $task, User $teacher, array $data): OfferingTask
    {
        $offering = $task->offering;
        $this->assertTeacherOwnsOffering($teacher, $offering);

        if ($offering->isManuallyClosed() || $offering->is_disabled) {
            throw ValidationException::withMessages([
                'title' => 'No se pueden editar tareas de una oferta cerrada o deshabilitada.',
            ]);
        }

        $maxPoints = (int) $data['max_points'];
        $otherTotal = (int) $offering->tasks()->where('id', '!=', $task->id)->sum('max_points');

        if ($otherTotal + $maxPoints > 100) {
            $remaining = max(0, 100 - $otherTotal);
            throw ValidationException::withMessages([
                'max_points' => "La suma total de puntajes superaría 100. Máximo permitido para esta tarea: {$remaining}.",
            ]);
        }

        // Si ya tiene notas mayores al nuevo max_points, proteger integridad
        $highestGiven = $task->grades()->whereNotNull('score')->max('score');
        if ($highestGiven !== null && $maxPoints < (int) $highestGiven) {
            throw ValidationException::withMessages([
                'max_points' => "Ya existen estudiantes calificados con {$highestGiven} puntos en esta tarea. No puede reducir el puntaje máximo por debajo de ese valor.",
            ]);
        }

        $task->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'max_points' => $maxPoints,
            'due_date' => $data['due_date'],
        ]);

        return $task->fresh();
    }

    public function deleteTask(OfferingTask $task, User $teacher): void
    {
        $offering = $task->offering;
        $this->assertTeacherOwnsOffering($teacher, $offering);

        if ($offering->isManuallyClosed() || $offering->is_disabled) {
            throw ValidationException::withMessages([
                'task' => 'No se pueden eliminar tareas de una oferta cerrada o deshabilitada.',
            ]);
        }

        $task->delete();
    }

    /**
     * Registra o actualiza la nota entera, el comentario y el estado de presentación de una actividad.
     * Únicamente el docente titular de la oferta puede modificar notas, comentarios o estado de entrega.
     * Distingue estrictamente "Sin calificar" (score = null) de "Calificada con 0" (score = 0).
     */
    public function gradeTask(
        OfferingTask $task,
        Enrollment $enrollment,
        User $teacher,
        ?int $score,
        ?string $comment,
        ?bool $isSubmitted = null
    ): TaskGrade {
        $offering = $task->offering;
        $this->assertTeacherOwnsOffering($teacher, $offering);

        if ($offering->isManuallyClosed() || $offering->is_disabled) {
            throw ValidationException::withMessages([
                'score' => 'La materia ya fue cerrada y sus notas están congeladas. Si requiere una corrección posterior, use "Corregir Nota Cerrada".',
            ]);
        }

        if ((int) $enrollment->offering_id !== (int) $offering->id || $enrollment->status === Enrollment::STATUS_ANNULLED) {
            abort(403, 'La inscripción no pertenece a esta oferta.');
        }

        if ($score !== null && ($score < 0 || $score > $task->max_points)) {
            throw ValidationException::withMessages([
                'score' => "La calificación debe ser un número entero entre 0 y {$task->max_points}.",
            ]);
        }

        $existing = TaskGrade::where('offering_task_id', $task->id)
            ->where('enrollment_id', $enrollment->id)
            ->first();

        $submittedAt = $existing?->submitted_at;
        if ($isSubmitted === true) {
            $submittedAt = $submittedAt ?? now();
        } elseif ($isSubmitted === false) {
            $submittedAt = null;
        } elseif ($score !== null && $submittedAt === null) {
            $submittedAt = now();
        }

        $taskGrade = TaskGrade::updateOrCreate(
            [
                'offering_task_id' => $task->id,
                'enrollment_id' => $enrollment->id,
            ],
            [
                'submitted_at' => $submittedAt,
                'score' => $score,
                'comment' => $comment !== null && trim($comment) !== '' ? trim($comment) : null,
                'graded_by' => $score !== null ? $teacher->id : null,
                'graded_at' => $score !== null ? now() : null,
            ]
        );

        if ($score !== null) {
            $this->notifications->notify(
                $enrollment->student_id,
                'Tarea calificada',
                "Tu tarea \"{$task->title}\" en {$offering->subject->code} fue calificada con {$score}/{$task->max_points} puntos.",
                'calificacion',
                route('student.enrollments.show', $enrollment)
            );
        }

        return $taskGrade;
    }

    /**
     * Cuenta cuántas celdas (inscripción × tarea) siguen "Sin calificar" (score = null).
     */
    public function countUngradedEntries(Offering $offering): int
    {
        $taskIds = $offering->tasks()->pluck('id');
        $enrollmentIds = $offering->activeEnrollments()->pluck('id');

        $totalCells = $taskIds->count() * $enrollmentIds->count();
        if ($totalCells === 0) {
            return 0;
        }

        $gradedCount = TaskGrade::query()
            ->whereIn('offering_task_id', $taskIds)
            ->whereIn('enrollment_id', $enrollmentIds)
            ->whereNotNull('score')
            ->count();

        return max(0, $totalCells - $gradedCount);
    }

    /**
     * Cierra la materia:
     * - Exige que las tareas sumen exactamente 100 puntos.
     * - Lo "Sin calificar" cuenta como 0 al cerrar.
     * - Calcula la nota final entera (0..100), marca cada inscripción como aprobada (>=51) o reprobada (<51) y congela.
     */
    public function closeOffering(Offering $offering, User $teacher): array
    {
        $this->assertTeacherOwnsOffering($teacher, $offering);

        if ($offering->is_disabled) {
            throw ValidationException::withMessages([
                'close' => 'No se puede cerrar una oferta deshabilitada.',
            ]);
        }

        if ($offering->isManuallyClosed()) {
            throw ValidationException::withMessages([
                'close' => 'Esta oferta ya fue cerrada anteriormente.',
            ]);
        }

        $totalPoints = (int) $offering->tasks()->sum('max_points');
        if ($totalPoints !== 100) {
            throw ValidationException::withMessages([
                'close' => "Para cerrar la materia, las tareas deben sumar exactamente 100 puntos (actualmente suman {$totalPoints}).",
            ]);
        }

        $stats = ['passed' => 0, 'failed' => 0];

        DB::transaction(function () use ($offering, $teacher, &$stats) {
            $now = now();
            $offering->load(['tasks', 'subject']);
            $taskIds = $offering->tasks->pluck('id');

            $enrollments = $offering->activeEnrollments()
                ->with(['student', 'taskGrades'])
                ->get();

            foreach ($enrollments as $enrollment) {
                $score = (int) $enrollment->taskGrades
                    ->whereIn('offering_task_id', $taskIds)
                    ->whereNotNull('score')
                    ->sum('score');

                $passingScore = AcademicConfigSingleton::getInstance()->passingScore();
                $score = max(0, min(100, $score));
                $status = $score >= $passingScore
                    ? Enrollment::STATUS_PASSED
                    : Enrollment::STATUS_FAILED;

                $enrollment->update([
                    'final_score' => $score,
                    'status' => $status,
                    'closed_at' => $now,
                ]);

                if ($status === Enrollment::STATUS_PASSED) {
                    $stats['passed']++;
                } else {
                    $stats['failed']++;
                }

                $this->notifications->notify(
                    $enrollment->student_id,
                    'Cierre de materia y nota final',
                    "La materia {$offering->subject->code} - {$offering->subject->name} ha sido cerrada. Tu nota final es {$score}/100 ({$enrollment->statusLabel()}).",
                    'calificacion',
                    route('student.progress')
                );
            }

            $offering->update([
                'closed_at' => $now,
                'closed_by' => $teacher->id,
            ]);

            $this->notifications->logActivity(
                $teacher->id,
                'materia_cerrada',
                "Docente {$teacher->name} cerró {$offering->subject->code} ({$offering->monthLabel()}): {$stats['passed']} aprobados, {$stats['failed']} reprobados.",
                Offering::class,
                $offering->id
            );
        });

        return $stats;
    }

    /**
     * Corrección auditada de una nota ya cerrada por el docente titular de esa oferta.
     */
    public function adjustClosedGrade(Enrollment $enrollment, User $teacher, int $newScore, string $reason): GradeAdjustment
    {
        $offering = $enrollment->offering;
        $this->assertTeacherOwnsOffering($teacher, $offering);

        if (! $offering->isManuallyClosed() || $enrollment->closed_at === null) {
            throw ValidationException::withMessages([
                'new_score' => 'Solo se puede auditar corrección en inscripciones de una materia ya cerrada.',
            ]);
        }

        if ($newScore < 0 || $newScore > 100) {
            throw ValidationException::withMessages([
                'new_score' => 'La nota final corregida debe ser un entero entre 0 y 100.',
            ]);
        }

        return DB::transaction(function () use ($enrollment, $teacher, $newScore, $reason, $offering) {
            $prevScore = (int) ($enrollment->final_score ?? 0);
            $prevStatus = $enrollment->status;
            $newStatus = $newScore >= Enrollment::PASSING_SCORE
                ? Enrollment::STATUS_PASSED
                : Enrollment::STATUS_FAILED;

            $adjustment = GradeAdjustment::create([
                'enrollment_id' => $enrollment->id,
                'adjusted_by' => $teacher->id,
                'previous_score' => $prevScore,
                'new_score' => $newScore,
                'previous_status' => $prevStatus,
                'new_status' => $newStatus,
                'reason' => trim($reason),
            ]);

            $enrollment->update([
                'final_score' => $newScore,
                'status' => $newStatus,
            ]);

            $this->notifications->notify(
                $enrollment->student_id,
                'Corrección de nota final registrada',
                "Tu nota final en {$offering->subject->code} fue corregida de {$prevScore} a {$newScore}/100 ({$enrollment->statusLabel()}). Motivo: {$reason}",
                'calificacion',
                route('student.progress')
            );

            $this->notifications->logActivity(
                $teacher->id,
                'nota_cerrada_corregida',
                "Corrección auditada en {$offering->subject->code} para {$enrollment->student->name}: {$prevScore} -> {$newScore}.",
                GradeAdjustment::class,
                $adjustment->id
            );

            return $adjustment;
        });
    }
}
