<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Models\Faculty;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $items = $career->curriculumItems()
            ->with(['subject', 'prerequisites'])
            ->get()
            ->groupBy('level');

        return view('admin.careers.show', compact('career', 'items'));
    }

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
            'is_active' => true,
        ]);

        $this->notifications->logActivity($request->user()->id, 'carrera_creada', "Carrera creada: {$career->code} - {$career->name}.");

        return back()->with('success', "Carrera \"{$career->name}\" registrada correctamente.");
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
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $career->update([
            'faculty_id' => $validated['faculty_id'],
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'shifts' => array_values(array_unique($validated['shifts'])),
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Carrera \"{$career->name}\" actualizada.");
    }

    public function destroy(Request $request, Career $career): RedirectResponse
    {
        if ($career->hasAssociatedData()) {
            $career->update(['is_active' => false]);
            $this->notifications->logActivity($request->user()->id, 'carrera_desactivada', "Carrera desactivada (posee datos asociados): {$career->name}.");

            return back()->with('success', "La carrera \"{$career->name}\" tiene pensum o estudiantes asociados, por lo que fue desactivada en lugar de eliminarse.");
        }

        $name = $career->name;
        $career->delete();

        return back()->with('success', "Carrera \"{$name}\" eliminada permanentemente.");
    }
}
