<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Offering;
use App\Models\OfferingTask;
use App\Repositories\Contracts\OfferingRepositoryInterface;
use App\Services\TaskGradingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherOfferingController extends Controller
{
    public function __construct(
        private readonly OfferingRepositoryInterface $offerings,
        private readonly TaskGradingService $gradingService,
    ) {}

    public function index(Request $request): View
    {
        $myOfferings = $this->offerings->getForTeacher($request->user());

        return view('teacher.offerings.index', compact('myOfferings'));
    }

    public function show(Request $request, Offering $offering): View
    {
        $this->gradingService->assertTeacherOwnsOffering($request->user(), $offering);
        $offering = $this->offerings->findDetailed($offering->id);

        $totalTaskPoints = (int) $offering->tasks->sum('max_points');
        $ungradedCount = $this->gradingService->countUngradedEntries($offering);
        $tab = $request->string('tab')->toString() ?: 'inscritos';

        // Mapa rápido [enrollment_id][task_id] => TaskGrade
        $gradesMap = [];
        foreach ($offering->activeEnrollments as $enrollment) {
            foreach ($enrollment->taskGrades as $tg) {
                $gradesMap[$enrollment->id][$tg->offering_task_id] = $tg;
            }
        }

        // Distribución de compañeros por Carrera y Facultad (para materias compartidas de niveles 1-3)
        $careerBreakdown = $offering->activeEnrollments
            ->groupBy(fn (Enrollment $e) => ($e->career?->name ?? $e->student->career?->name ?? 'Sin carrera') . ' · ' . ($e->career?->faculty?->code ?? $e->student->career?->faculty?->code ?? '—'))
            ->map->count()
            ->sortDesc();

        return view('teacher.offerings.show', compact(
            'offering',
            'totalTaskPoints',
            'ungradedCount',
            'gradesMap',
            'careerBreakdown',
            'tab'
        ));
    }

    public function storeTask(Request $request, Offering $offering): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:2', 'max:160'],
            'description' => ['nullable', 'string', 'max:1000'],
            'max_points' => ['required', 'integer', 'min:1', 'max:100'],
            'due_date' => ['required', 'date'],
        ]);

        $this->gradingService->createTask($offering, $request->user(), $validated);

        return redirect()->route('teacher.offerings.show', [$offering, 'tab' => 'tareas'])
            ->with('success', "Tarea \"{$validated['title']}\" creada correctamente.");
    }

    public function updateTask(Request $request, OfferingTask $task): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:2', 'max:160'],
            'description' => ['nullable', 'string', 'max:1000'],
            'max_points' => ['required', 'integer', 'min:1', 'max:100'],
            'due_date' => ['required', 'date'],
        ]);

        $this->gradingService->updateTask($task, $request->user(), $validated);

        return redirect()->route('teacher.offerings.show', [$task->offering_id, 'tab' => 'tareas'])
            ->with('success', "Tarea \"{$validated['title']}\" actualizada.");
    }

    public function destroyTask(Request $request, OfferingTask $task): RedirectResponse
    {
        $offeringId = $task->offering_id;
        $this->gradingService->deleteTask($task, $request->user());

        return redirect()->route('teacher.offerings.show', [$offeringId, 'tab' => 'tareas'])
            ->with('success', 'Tarea eliminada correctamente.');
    }

    public function gradeTask(Request $request, OfferingTask $task, Enrollment $enrollment): RedirectResponse
    {
        $rawScore = $request->input('score');
        $score = ($rawScore === null || $rawScore === '') ? null : (int) $rawScore;

        $request->validate([
            'score' => ['nullable', 'integer', 'min:0', 'max:' . $task->max_points],
            'comment' => ['nullable', 'string', 'max:500'],
            'is_submitted' => ['nullable', 'in:0,1'],
        ], [
            'score.integer' => 'La nota debe ser un número entero.',
            'score.max' => "La nota no puede exceder el puntaje máximo de la tarea ({$task->max_points}).",
        ]);

        $isSubmitted = $request->has('is_submitted') ? $request->boolean('is_submitted') : null;
        $this->gradingService->gradeTask($task, $enrollment, $request->user(), $score, $request->input('comment'), $isSubmitted);

        return redirect()->route('teacher.offerings.show', [$task->offering_id, 'tab' => 'calificaciones'])
            ->with('success', 'Calificación guardada para ' . $enrollment->student->name . '.');
    }

    public function close(Request $request, Offering $offering): RedirectResponse
    {
        $stats = $this->gradingService->closeOffering($offering, $request->user());

        return redirect()->route('teacher.offerings.show', [$offering, 'tab' => 'inscritos'])
            ->with('success', "Materia cerrada definitivamente: {$stats['passed']} aprobada(s), {$stats['failed']} reprobada(s). Las notas quedaron congeladas.");
    }

    public function adjustGrade(Request $request, Enrollment $enrollment): RedirectResponse
    {
        $validated = $request->validate([
            'new_score' => ['required', 'integer', 'min:0', 'max:100'],
            'reason' => ['required', 'string', 'min:5', 'max:255'],
        ]);

        $this->gradingService->adjustClosedGrade(
            $enrollment,
            $request->user(),
            (int) $validated['new_score'],
            $validated['reason']
        );

        return redirect()->route('teacher.offerings.show', [$enrollment->offering_id, 'tab' => 'inscritos'])
            ->with('success', "Nota final de {$enrollment->student->name} corregida con registro de auditoría.");
    }
}
