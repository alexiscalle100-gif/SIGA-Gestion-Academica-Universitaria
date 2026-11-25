<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FacultyController extends Controller
{
    public function __construct(private readonly NotificationService $notifications) {}

    public function index(): View
    {
        $faculties = Faculty::withCount(['careers', 'subjects', 'teachers'])
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        return view('admin.faculties.index', compact('faculties'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:faculties,code'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
        ], ['code.unique' => 'Ya existe una facultad con ese código.']);

        $faculty = Faculty::create([
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        $this->notifications->logActivity($request->user()->id, 'facultad_creada', "Facultad creada: {$faculty->code} - {$faculty->name}.");

        return back()->with('success', "Facultad \"{$faculty->name}\" registrada correctamente.");
    }

    public function update(Request $request, Faculty $faculty): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('faculties', 'code')->ignore($faculty->id)],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $faculty->update([
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Facultad \"{$faculty->name}\" actualizada.");
    }

    public function destroy(Request $request, Faculty $faculty): RedirectResponse
    {
        // Regla Sección 2: Se desactiva en lugar de eliminar si hay datos asociados
        if ($faculty->hasAssociatedData()) {
            $faculty->update(['is_active' => false]);
            $this->notifications->logActivity($request->user()->id, 'facultad_desactivada', "Facultad desactivada (posee datos asociados): {$faculty->name}.");

            return back()->with('success', "La facultad \"{$faculty->name}\" tiene carreras, docentes o materias asociadas, por lo que fue desactivada en lugar de eliminarse.");
        }

        $name = $faculty->name;
        $faculty->delete();

        return back()->with('success', "Facultad \"{$name}\" eliminada permanentemente.");
    }
}
