<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Models\Faculty;
use App\Models\Offering;
use App\Models\Subject;
use App\Repositories\Contracts\OfferingRepositoryInterface;
use App\Services\OfferingService;
use App\Services\SemesterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OfferingController extends Controller
{
    public function __construct(
        private readonly OfferingService $offeringService,
        private readonly OfferingRepositoryInterface $offerings,
        private readonly SemesterService $semesters,
    ) {}

    /**
     * Interfaz con DOS pestañas exactas (Sección 5):
     * - Pestaña 1 ("habilitacion"): Habilitación de materias (Botones de facultades -> catálogo de esa facultad -> formulario + docentes con choques -> confirmación).
     * - Pestaña 2 ("ofertas"): Ofertas habilitadas (gestión de ofertas ya creadas).
     */
    public function index(Request $request): View
    {
        $tab = $request->string('tab')->toString() ?: 'habilitacion';
        $faculties = Faculty::where('is_active', true)->orderBy('name')->get();
        $semesters = $this->semesters->allOrdered();
        $currentSemester = $this->semesters->current();

        $selectedFacultyId = $request->integer('faculty_id') ?: $faculties->first()?->id;
        $selectedSubjectId = $request->integer('subject_id') ?: null;
        $selectedSemesterId = $request->integer('semester_id') ?: $currentSemester->id;
        $selectedSemester = $semesters->firstWhere('id', $selectedSemesterId) ?? $currentSemester;
        $defaultMonth = array_key_first($selectedSemester->months()) ?? now()->month;
        $selectedMonth = $request->integer('month') ?: (in_array(now()->month, array_keys($selectedSemester->months()), true) ? now()->month : $defaultMonth);
        $selectedShift = $request->string('shift')->toString() ?: 'manana';

        $facultySubjects = $selectedFacultyId
            ? Subject::where('faculty_id', $selectedFacultyId)
                ->where('is_active', true)
                ->with('curriculumItems.career')
                ->orderBy('code')
                ->get()
            : collect();

        $selectedSubject = $selectedSubjectId
            ? $facultySubjects->firstWhere('id', $selectedSubjectId)
            : null;

        $allowedShifts = $selectedSubject ? $selectedSubject->allowedShifts() : array_keys(Career::SHIFTS);
        if (! in_array($selectedShift, $allowedShifts, true)) {
            $selectedShift = $allowedShifts[0] ?? 'manana';
        }

        $teachersAvailability = $selectedFacultyId
            ? $this->offeringService->teachersWithAvailability($selectedFacultyId, $selectedSemester->id, $selectedMonth, $selectedShift)
            : collect();

        // Datos para Pestaña 2: Ofertas habilitadas
        $filterFaculty = $request->integer('f_faculty') ?: null;
        $filterSemester = $request->integer('f_semester') ?: $currentSemester->id;
        $filterMonth = $request->integer('f_month') ?: null;
        $filterShift = $request->string('f_shift')->toString() ?: null;

        $allOfferings = $this->offerings->getFiltered($filterFaculty, $filterSemester, $filterMonth, $filterShift);

        return view('admin.offerings.index', compact(
            'tab',
            'faculties',
            'semesters',
            'currentSemester',
            'selectedFacultyId',
            'selectedSubject',
            'selectedSemester',
            'selectedMonth',
            'selectedShift',
            'allowedShifts',
            'facultySubjects',
            'teachersAvailability',
            'allOfferings',
            'filterFaculty',
            'filterSemester',
            'filterMonth',
            'filterShift'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'shift' => ['required', Rule::in(array_keys(Career::SHIFTS))],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'days_label' => ['nullable', 'string', 'max:40'],
            'max_capacity' => ['required', 'integer', 'min:1', 'max:'.Offering::MAX_ROOM_CAPACITY],
            'teacher_id' => ['nullable', 'exists:users,id'],
            'classroom' => ['nullable', 'integer', 'min:1', 'max:9999'],
        ]);

        $offering = $this->offeringService->createOffering($validated, $request->user());

        return redirect()->route('admin.offerings.index', ['tab' => 'ofertas', 'f_semester' => $offering->semester_id])
            ->with('success', "Oferta habilitada correctamente: {$offering->subject->code} ({$offering->monthLabel()} · {$offering->shiftLabel()} · {$offering->classroomLabel()} · {$offering->teacherLabel()}).");
    }

    public function edit(Offering $offering): View
    {
        $offering->load(['subject.faculty', 'semester', 'teacher'])->loadCount('activeEnrollments');
        $teachersAvailability = $this->offeringService->teachersWithAvailability(
            $offering->subject->faculty_id,
            $offering->semester_id,
            $offering->month,
            $offering->shift,
            $offering->id
        );

        return view('admin.offerings.edit', compact('offering', 'teachersAvailability'));
    }

    /**
     * Regla Sección 5: Solo permite modificar docente, aula y cupo máximo.
     * Fechas y turno quedan bloqueados.
     */
    public function update(Request $request, Offering $offering): RedirectResponse
    {
        $validated = $request->validate([
            'teacher_id' => ['nullable', 'exists:users,id'],
            'classroom' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'max_capacity' => ['required', 'integer', 'min:1', 'max:'.Offering::MAX_ROOM_CAPACITY],
        ]);

        $this->offeringService->updateOffering($offering, $validated, $request->user());

        return redirect()->route('admin.offerings.index', ['tab' => 'ofertas', 'f_semester' => $offering->semester_id])
            ->with('success', "Oferta {$offering->subject->code} actualizada correctamente.");
    }

    public function disable(Request $request, Offering $offering): RedirectResponse
    {
        $this->offeringService->disableOffering($offering, $request->user());

        return redirect()->route('admin.offerings.index', ['tab' => 'ofertas', 'f_semester' => $offering->semester_id])
            ->with('success', "Oferta {$offering->subject->code} deshabilitada. Las inscripciones asociadas quedaron anuladas y liberaron el cupo semestral.");
    }

    public function destroy(Request $request, Offering $offering): RedirectResponse
    {
        $semesterId = $offering->semester_id;
        $this->offeringService->deleteOffering($offering, $request->user());

        return redirect()->route('admin.offerings.index', ['tab' => 'ofertas', 'f_semester' => $semesterId])
            ->with('success', 'Oferta eliminada correctamente.');
    }
}
