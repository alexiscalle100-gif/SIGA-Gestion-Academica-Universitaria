<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Offering;
use App\Services\CurriculumProgressService;
use App\Services\EnrollmentService;
use App\Services\SemesterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentPortalController extends Controller
{
    public function __construct(
        private readonly EnrollmentService $enrollmentService,
        private readonly CurriculumProgressService $progressService,
        private readonly SemesterService $semesters,
    ) {}

    public function enrollments(Request $request): View
    {
        $student = $request->user()->load(['career.faculty', 'careers.faculty']);
        $careerId = $request->integer('career_id') ?: null;
        $portal = $this->enrollmentService->getStudentPortalState($student, null, $careerId);

        $currentSemesterEnrollments = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('semester_id', $portal['semester']->id)
            ->with(['offering.subject', 'offering.teacher', 'offering.tasks', 'taskGrades', 'career.faculty'])
            ->orderByDesc('enrolled_at')
            ->get();

        return view('student.enrollments.index', compact('student', 'portal', 'currentSemesterEnrollments'));
    }

    public function storeEnrollment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'offering_id' => ['required', 'exists:offerings,id'],
            'career_id' => ['nullable', 'exists:careers,id'],
        ]);

        $offering = Offering::with('subject')->findOrFail($validated['offering_id']);
        $careerId = isset($validated['career_id']) ? (int) $validated['career_id'] : null;
        $enrollment = $this->enrollmentService->enroll($request->user(), $offering, null, $careerId);

        return redirect()->route('student.enrollments.index', ['career_id' => $enrollment->career_id])
            ->with('success', "Inscripción confirmada en {$offering->subject->code} - {$offering->subject->name} (Intento #{$enrollment->attempt_number}).");
    }

    public function showEnrollment(Request $request, Enrollment $enrollment): View
    {
        if ((int) $enrollment->student_id !== (int) $request->user()->id) {
            abort(403, 'No puedes consultar inscripciones de otros estudiantes.');
        }

        $enrollment->load([
            'career.faculty',
            'offering.subject.faculty',
            'offering.teacher',
            'offering.semester',
            'offering.tasks',
            'taskGrades',
            'gradeAdjustments.adjuster',
        ]);

        $gradesByTask = $enrollment->taskGrades->keyBy('offering_task_id');
        $accumulatedScore = $enrollment->accumulatedScore();
        $totalAssignedPoints = (int) $enrollment->offering->tasks->sum('max_points');

        return view('student.enrollments.show', compact(
            'enrollment',
            'gradesByTask',
            'accumulatedScore',
            'totalAssignedPoints'
        ));
    }

    public function progress(Request $request): View
    {
        $student = $request->user()->load(['career.faculty', 'careers.faculty']);
        $studentCareers = $student->activeCareers();
        $selectedCareerId = $request->integer('career_id') ?: $studentCareers->first()?->id;
        $selectedCareer = $studentCareers->firstWhere('id', $selectedCareerId) ?? $studentCareers->first();

        $progress = $selectedCareer
            ? $this->progressService->buildCareerProgress($student, $selectedCareer)
            : null;

        $history = Enrollment::query()
            ->where('student_id', $student->id)
            ->with(['subject', 'semester', 'offering.teacher', 'career.faculty'])
            ->orderByDesc('semester_id')
            ->orderByDesc('enrolled_at')
            ->get();

        $passedScores = $history->where('status', '!=', Enrollment::STATUS_ANNULLED)
            ->whereNotNull('final_score')
            ->pluck('final_score');

        $averageScore = $passedScores->isNotEmpty() ? round((float) $passedScores->avg(), 1) : 0;

        return view('student.progress.index', compact('student', 'studentCareers', 'selectedCareer', 'progress', 'history', 'averageScore'));
    }
}
