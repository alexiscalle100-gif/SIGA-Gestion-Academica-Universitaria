<?php

namespace App\Http\Controllers;

use App\Models\Career;
use App\Models\Offering;
use App\Services\Reports\AcademicReportBuilder;
use App\Services\Reports\ReportExportContext;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function __construct(
        private readonly AcademicReportBuilder $builder,
        private readonly ReportExportContext $exportContext,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $careers = Career::orderBy('name')->get();
        $teacherOfferings = $user->isTeacher()
            ? Offering::where('teacher_id', $user->id)->with(['subject', 'semester'])->withCount('activeEnrollments')->orderByDesc('semester_id')->get()
            : collect();

        return view('reports.index', compact('user', 'careers', 'teacherOfferings'));
    }

    public function download(Request $request, string $type, string $format): Response
    {
        $user = $request->user();
        $format = strtolower($format);
        if (! in_array($format, ['pdf', 'excel'], true)) {
            abort(404);
        }

        $reportData = match ($type) {
            'subjects-by-career' => $this->subjectsByCareer($request, $user),
            'offering-enrolled-students' => $this->offeringEnrolledStudents($request, $user),
            'offering-grades' => $this->offeringGrades($request, $user),
            'teacher-history' => $this->teacherHistory($user),
            'student-history' => $this->studentHistory($user),
            default => abort(404),
        };

        return $this->exportContext->resolveFormat($format)->export($reportData);
    }

    private function subjectsByCareer(Request $request, $user): array
    {
        if (! $user->isAdmin()) {
            abort(403);
        }
        $career = $request->filled('career_id') ? Career::findOrFail($request->integer('career_id')) : null;

        return $this->builder->subjectsByCareer($career);
    }

    private function offeringEnrolledStudents(Request $request, $user): array
    {
        if (! $user->isTeacher()) {
            abort(403);
        }
        $offering = Offering::findOrFail($request->integer('offering_id'));
        if ((int) $offering->teacher_id !== (int) $user->id) {
            abort(403);
        }

        return $this->builder->offeringEnrolledStudents($offering);
    }

    private function offeringGrades(Request $request, $user): array
    {
        // Regla Sección 8: Administrador NO puede ver ni modificar notas de aula
        if (! $user->isTeacher()) {
            abort(403);
        }
        $offering = Offering::findOrFail($request->integer('offering_id'));
        if ((int) $offering->teacher_id !== (int) $user->id) {
            abort(403);
        }

        return $this->builder->offeringGrades($offering);
    }

    private function teacherHistory($user): array
    {
        if (! $user->isTeacher()) {
            abort(403);
        }

        return $this->builder->teacherOfferingsHistory($user);
    }

    private function studentHistory($user): array
    {
        if (! $user->isStudent()) {
            abort(403);
        }

        return $this->builder->studentAcademicHistory($user);
    }
}
