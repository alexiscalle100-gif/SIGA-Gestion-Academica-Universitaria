<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Models\CurriculumItem;
use App\Models\Enrollment;
use App\Models\Faculty;
use App\Models\Subject;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CareerController extends Controller
{
    public function __construct(private readonly NotificationService $notifications) {}

    public function index(): View
    {
        $faculties = Faculty::where('is_active', true)->orderBy('name')->get();
        $careers = Career::with('faculty')
            ->withCount(['curriculumItems', 'students'])
            ->orderByDesc('is_active')
            ->orderBy('faculty_id')
            ->orderBy('name')
            ->get();

        return view('admin.careers.index', compact('careers', 'faculties'));
    }

    public function show(Career $career): View
    {
        $career->load('faculty');
        $allItems = $career->curriculumItems()
            ->with(['subject', 'prerequisites'])
            ->orderBy('level')
            ->orderBy('month_in_level')
            ->orderBy('order_in_curriculum')
            ->get();

        $items = $allItems->groupBy('level');
        $usedSubjectIds = $allItems->pluck('subject_id')->all();

        $availableSubjects = Subject::where('faculty_id', $career->faculty_id)
            ->where('is_active', true)
            ->whereNotIn('id', $usedSubjectIds)
            ->orderBy('code')
            ->get();

        $allFacultySubjects = Subject::where('faculty_id', $career->faculty_id)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('admin.careers.show', compact('career', 'items', 'allItems', 'availableSubjects', 'allFacultySubjects'));
    }

    /**
     * Toda carrera nueva nace INHABILITADA (is_active = false) hasta que el administrador
     * arme su pensum y apruebe su habilitación.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'faculty_id' => ['required', 'exists:faculties,id'],
            'code' => ['required', 'string', 'max:20', 'unique:careers,code'],
            'name' => ['required', 'string', 'max:160'],
            'shifts' => ['required', 'array', 'min:1'],
            'shifts.*' => [Rule::in(array_keys(Career::SHIFTS))],
            'description' => ['nullable', 'string', 'max:500'],
        ], ['code.unique' => 'Ya existe una carrera con ese código.']);

        $career = Career::create([
            'faculty_id' => $validated['faculty_id'],
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'shifts' => array_values(array_unique($validated['shifts'])),
            'description' => $validated['description'] ?? null,
            'is_active' => false,
        ]);

        $this->notifications->logActivity($request->user()->id, 'carrera_creada', "Carrera creada en estado Inhabilitada (pendiente de aprobación): {$career->code} - {$career->name}.");

        return redirect()->route('admin.careers.show', $career)
            ->with('success', "Carrera \"{$career->name}\" creada en estado Inhabilitada. Agrega sus materias en el pensum y aprueba su habilitación cuando esté lista.");
    }

    public function update(Request $request, Career $career): RedirectResponse
    {
        $validated = $request->validate([
            'faculty_id' => ['required', 'exists:faculties,id'],
            'code' => ['required', 'string', 'max:20', Rule::unique('careers', 'code')->ignore($career->id)],
            'name' => ['required', 'string', 'max:160'],
            'shifts' => ['required', 'array', 'min:1'],
            'shifts.*' => [Rule::in(array_keys(Career::SHIFTS))],
            'description' => ['nullable', 'string', 'max:500'],
        ], ['code.unique' => 'Ya existe otra carrera con ese código.']);

        $career->update([
            'faculty_id' => $validated['faculty_id'],
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'shifts' => array_values(array_unique($validated['shifts'])),
            'description' => $validated['description'] ?? null,
        ]);

        $this->notifications->logActivity($request->user()->id, 'carrera_actualizada', "Carrera actualizada: {$career->code} - {$career->name}.");

        return back()->with('success', "Carrera \"{$career->name}\" actualizada correctamente.");
    }

    /**
     * Permite aprobar/habilitar o reactivar una carrera inhabilitada, o deshabilitar una activa.
     */
    public function toggleStatus(Request $request, Career $career): RedirectResponse
    {
        $newState = ! $career->is_active;
        $career->update(['is_active' => $newState]);

        $action = $newState ? 'carrera_habilitada' : 'carrera_desactivada';
        $label = $newState ? 'habilitada / aprobada' : 'deshabilitada';
        $this->notifications->logActivity($request->user()->id, $action, "Carrera {$label}: {$career->code} - {$career->name}.");

        return back()->with('success', "Carrera \"{$career->name}\" {$label} correctamente.");
    }

    /**
     * Agrega una materia existente de la facultad o crea una nueva materia y la incorpora al pensum de la carrera.
     */
    public function addSubject(Request $request, Career $career): RedirectResponse
    {
        $mode = $request->input('mode', 'existing');

        if ($mode === 'new') {
            $validated = $request->validate([
                'new_code' => ['required', 'string', 'max:25', 'unique:subjects,code'],
                'new_area_prefix' => ['required', 'string', 'max:10'],
                'new_name' => ['required', 'string', 'max:160'],
                'new_credits' => ['required', 'integer', 'min:1', 'max:12'],
                'level' => ['required', 'integer', 'min:1', 'max:8'],
                'month_in_level' => ['required', 'integer', 'min:1', 'max:6'],
                'is_elective' => ['nullable', 'boolean'],
                'elective_number' => ['nullable', 'integer', 'min:1', 'max:4'],
                'requires_full_level' => ['nullable', 'integer', 'min:1', 'max:7'],
                'prerequisite_ids' => ['nullable', 'array'],
                'prerequisite_ids.*' => ['integer', 'exists:subjects,id'],
            ], [
                'new_code.unique' => 'Ya existe una materia en el catálogo con ese código.',
            ]);
        } else {
            $validated = $request->validate([
                'subject_id' => ['required', 'exists:subjects,id'],
                'level' => ['required', 'integer', 'min:1', 'max:8'],
                'month_in_level' => ['required', 'integer', 'min:1', 'max:6'],
                'is_elective' => ['nullable', 'boolean'],
                'elective_number' => ['nullable', 'integer', 'min:1', 'max:4'],
                'requires_full_level' => ['nullable', 'integer', 'min:1', 'max:7'],
                'prerequisite_ids' => ['nullable', 'array'],
                'prerequisite_ids.*' => ['integer', 'exists:subjects,id'],
            ]);
        }

        $subject = DB::transaction(function () use ($mode, $validated, $career, $request) {
            if ($mode === 'new') {
                $subject = Subject::create([
                    'faculty_id' => $career->faculty_id,
                    'code' => strtoupper(trim($validated['new_code'])),
                    'area_prefix' => strtoupper(trim($validated['new_area_prefix'])),
                    'name' => trim($validated['new_name']),
                    'credits' => (int) $validated['new_credits'],
                    'is_active' => true,
                ]);
            } else {
                $subject = Subject::findOrFail($validated['subject_id']);
                if ((int) $subject->faculty_id !== (int) $career->faculty_id) {
                    abort(422, 'La materia debe pertenecer a la misma facultad que la carrera.');
                }
            }

            if ($career->curriculumItems()->where('subject_id', $subject->id)->exists()) {
                return null;
            }

            $level = (int) $validated['level'];
            $month = (int) $validated['month_in_level'];
            $order = (($level - 1) * 6) + $month;

            $item = CurriculumItem::create([
                'career_id' => $career->id,
                'subject_id' => $subject->id,
                'level' => $level,
                'month_in_level' => $month,
                'order_in_curriculum' => $order,
                'is_elective' => $request->boolean('is_elective'),
                'elective_number' => $request->boolean('is_elective') ? ($validated['elective_number'] ?? 1) : null,
                'requires_full_level' => ! empty($validated['requires_full_level']) ? (int) $validated['requires_full_level'] : null,
            ]);

            $prereqs = collect($validated['prerequisite_ids'] ?? [])
                ->map(fn ($id) => (int) $id)
                ->reject(fn ($id) => $id === (int) $subject->id)
                ->unique()
                ->values()
                ->all();

            if (! empty($prereqs)) {
                $item->prerequisites()->sync($prereqs);
            }

            return $subject;
        });

        if (! $subject) {
            return back()->with('error', 'Esa materia ya se encuentra registrada en el pensum de esta carrera.');
        }

        $this->notifications->logActivity($request->user()->id, 'pensum_materia_agregada', "Se agregó {$subject->code} - {$subject->name} al pensum de {$career->code}.");

        return back()->with('success', "Materia \"{$subject->code} - {$subject->name}\" agregada al Nivel {$validated['level']} (Mes {$validated['month_in_level']}).");
    }

    public function removeSubject(Request $request, Career $career, CurriculumItem $curriculumItem): RedirectResponse
    {
        if ((int) $curriculumItem->career_id !== (int) $career->id) {
            abort(404);
        }

        $hasEnrollments = Enrollment::where('career_id', $career->id)
            ->where('subject_id', $curriculumItem->subject_id)
            ->exists();

        if ($hasEnrollments) {
            return back()->with('error', 'No se puede retirar esta materia del pensum porque ya existen inscripciones registradas.');
        }

        $code = $curriculumItem->subject->code;
        $curriculumItem->delete();

        $this->notifications->logActivity($request->user()->id, 'pensum_materia_retirada', "Se retiró {$code} del pensum de {$career->code}.");

        return back()->with('success', "Materia {$code} retirada del pensum.");
    }

    public function destroy(Request $request, Career $career): RedirectResponse
    {
        if ($career->hasAssociatedData()) {
            $career->update(['is_active' => false]);
            $this->notifications->logActivity($request->user()->id, 'carrera_desactivada', "Carrera desactivada (posee datos asociados): {$career->name}.");

            return back()->with('success', "La carrera \"{$career->name}\" tiene pensum o estudiantes asociados, por lo que fue desactivada en lugar de eliminarse. Puedes reactivarla cuando desees.");
        }

        $name = $career->name;
        $career->delete();

        return back()->with('success', "Carrera \"{$name}\" eliminada permanentemente.");
    }
}
