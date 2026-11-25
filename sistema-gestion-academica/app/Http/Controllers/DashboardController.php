<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Career;
use App\Models\Enrollment;
use App\Models\Faculty;
use App\Models\Offering;
use App\Models\Subject;
use App\Models\Tuition;
use App\Models\User;
use App\Repositories\Contracts\OfferingRepositoryInterface;
use App\Services\CurriculumProgressService;
use App\Services\EnrollmentService;
use App\Services\SemesterService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly SemesterService $semesters,
        private readonly OfferingRepositoryInterface $offerings,
        private readonly EnrollmentService $enrollmentService,
        private readonly CurriculumProgressService $progressService,
    ) {
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $currentSemester = $this->semesters->current();

        if ($user->isAdmin()) {
            $stats = [
                'faculties' => Faculty::where('is_active', true)->count(),
                'careers' => Career::where('is_active', true)->count(),
                'subjects' => Subject::where('is_active', true)->count(),
                'active_offerings' => Offering::where('semester_id', $currentSemester->id)->where('is_disabled', false)->count(),
                'active_tuitions' => Tuition::where('semester_id', $currentSemester->id)->where('is_active', true)->count(),
                'students' => User::where('role', User::ROLE_STUDENT)->count(),
                'teachers' => User::where('role', User::ROLE_TEACHER)->count(),
                'enrollments' => Enrollment::where('semester_id', $currentSemester->id)
                    ->where('status', '!=', Enrollment::STATUS_ANNULLED)
                    ->count(),
            ];

            $recentOfferings = Offering::with(['subject.faculty', 'teacher', 'semester'])
                ->withCount('activeEnrollments')
                ->where('semester_id', $currentSemester->id)
                ->latest()
                ->limit(6)
                ->get();

            $recentActivities = ActivityLog::with('user')->latest()->limit(8)->get();

            return view('dashboard.admin', compact('user', 'currentSemester', 'stats', 'recentOfferings', 'recentActivities'));
        }

        if ($user->isTeacher()) {
            $user->loadMissing(['faculty', 'faculties']);
            $myOfferings = $this->offerings->getForTeacher($user);
            $activeOfferings = $myOfferings->filter(fn(Offering $o) => !$o->is_disabled && !$o->isManuallyClosed());

            $stats = [
                'total_offerings' => $myOfferings->count(),
                'active_offerings' => $activeOfferings->count(),
                'total_students' => $activeOfferings->sum('active_enrollments_count'),
                'closed_offerings' => $myOfferings->filter(fn(Offering $o) => $o->isManuallyClosed())->count(),
            ];

            return view('dashboard.teacher', compact('user', 'currentSemester', 'myOfferings', 'stats'));
        }

        // Estudiante (corrige orden de parámetros: $referenceDate = null, $careerId = $selectedCareerId)
        $user->loadMissing(['career.faculty', 'careers.faculty']);
        $studentCareers = $user->activeCareers();
        $selectedCareerId = $request->integer('career_id') ?: null;
        $portal = $this->enrollmentService->getStudentPortalState($user, null, $selectedCareerId);
        $selectedCareer = $portal['selected_career'] ?? $studentCareers->first();

        $inProgress = Enrollment::query()
            ->where('student_id', $user->id)
            ->where('status', Enrollment::STATUS_IN_PROGRESS)
            ->with(['career.faculty', 'offering.subject', 'offering.teacher', 'offering.tasks', 'taskGrades'])
            ->latest('enrolled_at')
            ->get();

        $careerProgress = $selectedCareer
            ? $this->progressService->buildCareerProgress($user, $selectedCareer)
            : null;

        $recentNotifications = $user->appNotifications()->latest()->limit(5)->get();

        return view('dashboard.student', compact(
            'user',
            'currentSemester',
            'portal',
            'studentCareers',
            'selectedCareer',
            'inProgress',
            'careerProgress',
            'recentNotifications'
        ));
    }
}
