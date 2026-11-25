<?php

namespace App\Services;

use App\Models\Career;
use App\Models\Enrollment;
use App\Models\Offering;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\User;
use App\Support\AcademicConfigSingleton;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OfferingService
{
    public function __construct(private readonly NotificationService $notifications)
    {
    }

    /**
     * Crea una oferta académica. Docente y aula son opcionales al habilitar y pueden asignarse/modificarse luego.
     */
    public function createOffering(array $data, User $admin): Offering
    {
        $config = AcademicConfigSingleton::getInstance();
        $subject = Subject::with('faculty')->findOrFail($data['subject_id']);
        $semester = Semester::findOrFail($data['semester_id']);

        $teacherId = ! empty($data['teacher_id']) ? (int) $data['teacher_id'] : null;
        $teacher = $teacherId ? User::where('role', User::ROLE_TEACHER)->findOrFail($teacherId) : null;

        $classroom = isset($data['classroom']) && $data['classroom'] !== '' && $data['classroom'] !== null
            ? (int) $data['classroom']
            : null;

        $month = (int) $data['month'];
        $shift = $data['shift'];
        $maxCapacity = (int) ($data['max_capacity'] ?? $config->maxRoomCapacity());

        $schedules = $config->shiftSchedules();
        $defaultSchedule = $schedules[$shift] ?? ['start' => '07:30', 'end' => '11:30'];
        $startTime = ! empty($data['start_time']) ? $data['start_time'] : $defaultSchedule['start'];
        $endTime = ! empty($data['end_time']) ? $data['end_time'] : $defaultSchedule['end'];
        $daysLabel = ! empty($data['days_label']) ? $data['days_label'] : 'Lunes a Viernes';

        if ($startTime >= $endTime) {
            throw ValidationException::withMessages([
                'end_time' => 'La hora de fin del turno debe ser posterior a la hora de inicio.',
            ]);
        }

        $this->validateCommonRules($subject, $semester, $teacher, $month, $shift, $classroom, $maxCapacity, null, $startTime, $endTime);
        $this->validateMonthBelongsToSemester($semester, $month);

        if ($data['start_date'] > $data['end_date']) {
            throw ValidationException::withMessages([
                'end_date' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
            ]);
        }

        $offering = Offering::create([
            'subject_id' => $subject->id,
            'semester_id' => $semester->id,
            'month' => $month,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'shift' => $shift,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'days_label' => $daysLabel,
            'max_capacity' => $maxCapacity,
            'teacher_id' => $teacher?->id,
            'classroom' => $classroom,
            'is_disabled' => false,
        ]);

        if ($teacher) {
            $this->notifications->notify(
                $teacher,
                'Nueva materia asignada',
                "Se te asignó la oferta de {$subject->code} - {$subject->name} ({$offering->monthLabel()}, turno {$offering->shiftLabel()}, {$offering->classroomLabel()}).",
                'oferta',
                route('teacher.offerings.show', $offering)
            );
        }

        $teacherName = $teacher ? $teacher->name : 'Por asignar';
        $this->notifications->logActivity(
            $admin->id,
            'oferta_creada',
            "Oferta habilitada: {$subject->code} ({$offering->monthLabel()} - {$offering->shiftLabel()}, {$offering->classroomLabel()}, docente {$teacherName}).",
            Offering::class,
            $offering->id
        );

        return $offering;
    }

    /**
     * Modifica SOLO docente, aula y cupo máximo de una oferta creada (permite asignar aula/docente después).
     * Fecha de inicio, fecha de fin y turno permanecen bloqueados.
     */
    public function updateOffering(Offering $offering, array $data, User $admin): Offering
    {
        if ($offering->is_disabled || $offering->isManuallyClosed()) {
            throw ValidationException::withMessages([
                'offering' => 'No se puede modificar una oferta deshabilitada o ya cerrada.',
            ]);
        }

        $offering->loadMissing(['subject.faculty', 'semester']);

        $teacherId = ! empty($data['teacher_id']) ? (int) $data['teacher_id'] : null;
        $teacher = $teacherId ? User::where('role', User::ROLE_TEACHER)->findOrFail($teacherId) : null;

        $classroom = isset($data['classroom']) && $data['classroom'] !== '' && $data['classroom'] !== null
            ? (int) $data['classroom']
            : null;

        $maxCapacity = (int) $data['max_capacity'];

        $enrolledCount = $offering->activeEnrollments()->count();
        if ($maxCapacity < $enrolledCount) {
            throw ValidationException::withMessages([
                'max_capacity' => "El cupo máximo no puede ser menor al número actual de inscritos ({$enrolledCount}).",
            ]);
        }

        $this->validateCommonRules(
            $offering->subject,
            $offering->semester,
            $teacher,
            $offering->month,
            $offering->shift,
            $classroom,
            $maxCapacity,
            $offering->id,
            $offering->start_time,
            $offering->end_time
        );

        $oldTeacherId = $offering->teacher_id;

        $offering->update([
            'teacher_id' => $teacher?->id,
            'classroom' => $classroom,
            'max_capacity' => $maxCapacity,
        ]);

        if ($teacher && (int) $oldTeacherId !== (int) $teacher->id) {
            $this->notifications->notify(
                $teacher,
                'Reasignación de oferta académica',
                "Has sido asignado a {$offering->subject->code} - {$offering->subject->name} ({$offering->monthLabel()}). Heredas las tareas y estudiantes existentes.",
                'oferta',
                route('teacher.offerings.show', $offering)
            );
        }

        $teacherName = $teacher ? $teacher->name : 'Por asignar';
        $this->notifications->logActivity(
            $admin->id,
            'oferta_actualizada',
            "Oferta {$offering->subject->code} actualizada: docente {$teacherName}, {$offering->fresh()->classroomLabel()}, cupo {$maxCapacity}.",
            Offering::class,
            $offering->id
        );

        return $offering->fresh();
    }

    /**
     * Deshabilita una oferta: las inscripciones activas pasan a estado "anulada"
     * y dejan de consumir el cupo de 6 materias del estudiante.
     */
    public function disableOffering(Offering $offering, User $admin): void
    {
        if ($offering->is_disabled) {
            return;
        }

        DB::transaction(function () use ($offering, $admin) {
            $offering->update(['is_disabled' => true]);

            $affectedEnrollments = $offering->enrollments()
                ->where('status', '!=', Enrollment::STATUS_ANNULLED)
                ->with('student')
                ->get();

            foreach ($affectedEnrollments as $enrollment) {
                $enrollment->update(['status' => Enrollment::STATUS_ANNULLED]);
                if ($enrollment->student) {
                    $this->notifications->notify(
                        $enrollment->student,
                        'Oferta deshabilitada',
                        "La oferta de {$offering->subject->code} - {$offering->subject->name} fue deshabilitada. Tu inscripción quedó anulada y no consume tu cupo de 6 materias.",
                        'oferta',
                        route('student.enrollments.index')
                    );
                }
            }

            $this->notifications->logActivity(
                $admin->id,
                'oferta_deshabilitada',
                "Se deshabilitó la oferta {$offering->subject->code} ({$offering->monthLabel()} - {$offering->shiftLabel()}). Inscripciones anuladas: {$affectedEnrollments->count()}.",
                Offering::class,
                $offering->id
            );
        });
    }

    /**
     * Eliminar solo se permite si la oferta NO tiene inscritos.
     */
    public function deleteOffering(Offering $offering, User $admin): void
    {
        if ($offering->enrollments()->exists()) {
            throw ValidationException::withMessages([
                'offering' => 'No se puede eliminar una oferta que tiene estudiantes inscritos. Utilice la opción "Deshabilitar".',
            ]);
        }

        $desc = "{$offering->subject->code} ({$offering->monthLabel()} - {$offering->shiftLabel()})";
        $offering->delete();

        $this->notifications->logActivity(
            $admin->id,
            'oferta_eliminada',
            "Se eliminó la oferta sin inscritos: {$desc}."
        );
    }

    /**
     * Devuelve docentes habilitados para esa facultad (soportando docentes multi-facultad)
     * indicando disponibilidad y conflicto exacto por mes y turno en TODAS las facultades.
     */
    public function teachersWithAvailability(
        int $facultyId,
        int $semesterId,
        int $month,
        string $shift,
        ?int $ignoreOfferingId = null
    ): Collection {
        $teachers = User::query()
            ->where('role', User::ROLE_TEACHER)
            ->where('is_active', true)
            ->where(function ($q) use ($facultyId) {
                $q->where('faculty_id', $facultyId)
                    ->orWhereHas('faculties', fn ($fq) => $fq->where('faculties.id', $facultyId));
            })
            ->with('faculties')
            ->orderBy('name')
            ->get();

        $busyOfferings = Offering::query()
            ->whereNotNull('teacher_id')
            ->where('semester_id', $semesterId)
            ->where('month', $month)
            ->where('shift', $shift)
            ->where('is_disabled', false)
            ->when($ignoreOfferingId, fn ($q) => $q->where('id', '!=', $ignoreOfferingId))
            ->with('subject')
            ->get()
            ->keyBy('teacher_id');

        return $teachers->map(function (User $teacher) use ($busyOfferings) {
            $conflict = $busyOfferings->get($teacher->id);

            return [
                'teacher' => $teacher,
                'available' => $conflict === null,
                'conflict_label' => $conflict
                    ? "Ocupado en {$conflict->subject->code} ({$conflict->subject->name}, {$conflict->classroomLabel()})"
                    : null,
            ];
        });
    }

    private function validateCommonRules(
        Subject $subject,
        Semester $semester,
        ?User $teacher,
        int $month,
        string $shift,
        ?int $classroom,
        int $maxCapacity,
        ?int $ignoreOfferingId = null,
        ?string $startTime = null,
        ?string $endTime = null
    ): void {
        $config = AcademicConfigSingleton::getInstance();
        $maxCap = $config->maxRoomCapacity();
        $schedules = $config->shiftSchedules();
        $defaultSchedule = $schedules[$shift] ?? ['start' => '07:30', 'end' => '11:30'];
        $startTime = $startTime ?: $defaultSchedule['start'];
        $endTime = $endTime ?: $defaultSchedule['end'];

        if ($maxCapacity < 1 || $maxCapacity > $maxCap) {
            throw ValidationException::withMessages([
                'max_capacity' => "El cupo máximo permitido por aula es entre 1 y {$maxCap} estudiantes.",
            ]);
        }

        // 1. El turno debe estar habilitado para las carreras que usan la materia
        $allowedShifts = $subject->allowedShifts();
        if (! in_array($shift, $allowedShifts, true)) {
            $allowedNames = array_map(fn ($s) => Career::SHIFTS[$s] ?? $s, $allowedShifts);
            throw ValidationException::withMessages([
                'shift' => 'El turno "' . (Career::SHIFTS[$shift] ?? $shift) . '" no está habilitado para las carreras que cursan esta materia. Turnos permitidos: ' . implode(', ', $allowedNames) . '.',
            ]);
        }

        // 2. Si se asignó docente, debe pertenecer a la facultad de la materia y no tener choque en ninguna facultad
        if ($teacher !== null) {
            if (! $teacher->belongsToFaculty((int) $subject->faculty_id)) {
                throw ValidationException::withMessages([
                    'teacher_id' => 'El docente seleccionado no está habilitado en la facultad dueña de esta materia (' . $subject->faculty->name . ').',
                ]);
            }

            $teacherOfferings = Offering::query()
                ->where('teacher_id', $teacher->id)
                ->where('semester_id', $semester->id)
                ->where('month', $month)
                ->where('is_disabled', false)
                ->when($ignoreOfferingId, fn ($q) => $q->where('id', '!=', $ignoreOfferingId))
                ->with('subject')
                ->get();

            $teacherClash = $teacherOfferings->first(
                fn (Offering $o) => $o->shift === $shift || $o->overlapsTimeWith($startTime, $endTime)
            );

            if ($teacherClash) {
                throw ValidationException::withMessages([
                    'teacher_id' => "Choque de docente: {$teacher->name} ya dicta {$teacherClash->subject->code} - {$teacherClash->subject->name} en el mismo mes ({$teacherClash->scheduleText()}).",
                ]);
            }
        }

        // 3. Si se asignó aula, debe ser positiva y no estar ocupada en el mismo mes y turno/horario
        if ($classroom !== null) {
            if ($classroom < 1) {
                throw ValidationException::withMessages([
                    'classroom' => 'El aula debe ser un número entero positivo.',
                ]);
            }

            $roomOfferings = Offering::query()
                ->where('classroom', $classroom)
                ->where('semester_id', $semester->id)
                ->where('month', $month)
                ->where('is_disabled', false)
                ->when($ignoreOfferingId, fn ($q) => $q->where('id', '!=', $ignoreOfferingId))
                ->with('subject')
                ->get();

            $roomClash = $roomOfferings->first(
                fn (Offering $o) => $o->shift === $shift || $o->overlapsTimeWith($startTime, $endTime)
            );

            if ($roomClash) {
                throw ValidationException::withMessages([
                    'classroom' => "Choque de aula: el Aula {$classroom} ya está ocupada por {$roomClash->subject->code} en ese mismo mes ({$roomClash->scheduleText()}).",
                ]);
            }
        }
    }

    private function validateMonthBelongsToSemester(Semester $semester, int $month): void
    {
        $validMonths = array_keys($semester->months());
        if (! in_array($month, $validMonths, true)) {
            throw ValidationException::withMessages([
                'month' => "El mes seleccionado no corresponde al semestre {$semester->code} ({$semester->periodLabel()}).",
            ]);
        }
    }
}
