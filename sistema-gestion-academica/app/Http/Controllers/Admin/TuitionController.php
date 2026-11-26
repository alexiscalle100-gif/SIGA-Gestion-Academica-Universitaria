<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Models\Enrollment;
use App\Models\Semester;
use App\Models\Tuition;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\SemesterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TuitionController extends Controller
{
    public function __construct(
        private readonly SemesterService $semesters,
        private readonly NotificationService $notifications,
    ) {}

    public function index(Request $request): View
    {
        $semesters = $this->semesters->allOrdered();
        $currentSemester = $this->semesters->current();
        $selectedSemesterId = $request->integer('semester_id') ?: $currentSemester->id;
        $selectedSemester = $semesters->firstWhere('id', $selectedSemesterId) ?? $currentSemester;
        $search = $request->string('search')->trim()->toString() ?: null;

        $students = User::query()
            ->where('role', User::ROLE_STUDENT)
            ->with(['career.faculty', 'careers.faculty'])
            ->when($search, fn ($q) => $q->where(fn ($i) => $i->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")))
            ->orderBy('name')
            ->get();

        $tuitionsMap = Tuition::where('semester_id', $selectedSemester->id)
            ->get()
            ->keyBy(fn (Tuition $t) => "{$t->student_id}-{$t->career_id}");

        $usedSlotsMap = Enrollment::query()
            ->where('semester_id', $selectedSemester->id)
            ->where('status', '!=', Enrollment::STATUS_ANNULLED)
            ->get()
            ->groupBy(fn (Enrollment $e) => "{$e->student_id}-".($e->career_id ?? $e->student?->career_id))
            ->map->count();

        $rows = [];
        foreach ($students as $st) {
            foreach ($st->activeCareers() as $idx => $car) {
                $key = "{$st->id}-{$car->id}";
                $rows[] = [
                    'student' => $st,
                    'career' => $car,
                    'is_secondary' => $idx > 0,
                    'tuition' => $tuitionsMap->get($key),
                    'used' => (int) ($usedSlotsMap->get($key) ?? 0),
                ];
            }
        }

        $careers = Career::where('is_active', true)->orderBy('name')->get();

        return view('admin.tuitions.index', compact(
            'semesters',
            'selectedSemester',
            'rows',
            'careers',
            'search'
        ));
    }

    public function toggle(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:users,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
            'career_id' => ['nullable', 'exists:careers,id'],
            'is_active' => ['required', 'boolean'],
        ]);

        $student = User::where('role', User::ROLE_STUDENT)->findOrFail($validated['student_id']);
        $semester = Semester::findOrFail($validated['semester_id']);
        $careerId = $validated['career_id'] ?? $student->career_id;

        if (! $careerId) {
            return back()->with('error', "El estudiante {$student->name} no tiene una carrera asignada.");
        }

        $active = (bool) $validated['is_active'];

        $tuition = Tuition::updateOrCreate(
            [
                'student_id' => $student->id,
                'career_id' => $careerId,
                'semester_id' => $semester->id,
            ],
            [
                'is_active' => $active,
                'max_subjects' => Tuition::DEFAULT_MAX_SUBJECTS,
                'activated_by' => $request->user()->id,
                'activated_at' => $active ? now() : null,
            ]
        );

        $this->notifications->notify(
            $student,
            $active ? 'Matrícula semestral activada' : 'Matrícula semestral desactivada',
            $active
            ? "Tu matrícula para el semestre {$semester->code} ha sido activada (cupo de 6 materias)."
            : "Tu matrícula para el semestre {$semester->code} ha sido desactivada por administración.",
            'sistema',
            route('student.enrollments.index')
        );

        $this->notifications->logActivity(
            $request->user()->id,
            $active ? 'matricula_activada' : 'matricula_desactivada',
            ($active ? 'Matrícula activada' : 'Matrícula desactivada')." para {$student->name} ({$semester->code}).",
            Tuition::class,
            $tuition->id
        );

        return back()->with('success', 'Matrícula de '.$student->name.' '.($active ? 'activada' : 'desactivada')." para {$semester->code}.");
    }
}
