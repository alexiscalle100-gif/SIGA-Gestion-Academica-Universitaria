<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Models\Enrollment;
use App\Models\Faculty;
use App\Models\Tuition;
use App\Models\User;
use App\Repositories\Contracts\OfferingRepositoryInterface;
use App\Services\CurriculumProgressService;
use App\Services\NotificationService;
use App\Services\SemesterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserProfileController extends Controller
{
    public function __construct(
        private readonly NotificationService $notifications,
        private readonly SemesterService $semesters,
        private readonly CurriculumProgressService $progressService,
        private readonly OfferingRepositoryInterface $offerings,
    ) {}

    public function index(Request $request): View
    {
        $role = $request->string('role')->trim()->toString() ?: null;
        $search = $request->string('search')->trim()->toString() ?: null;

        $users = User::with(['faculty', 'faculties', 'career.faculty', 'careers'])
            ->when($role, fn ($q) => $q->where('role', $role))
            ->when($search, fn ($q) => $q->where(fn ($i) => $i->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")))
            ->orderByRaw("CASE role WHEN 'administrador' THEN 1 WHEN 'docente' THEN 2 ELSE 3 END")
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $faculties = Faculty::where('is_active', true)->orderBy('name')->get();
        $careers = Career::where('is_active', true)->with('faculty')->orderBy('name')->get();

        return view('admin.users.index', compact('users', 'faculties', 'careers', 'role', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'document_number' => ['nullable', 'string', 'max:30'],
            'phone' => ['nullable', 'string', 'max:30'],
            'faculty_id' => ['nullable', 'exists:faculties,id'],
            'career_id' => ['nullable', 'exists:careers,id'],
            'password' => ['required', Password::min(8)],
        ]);

        if ($validated['role'] === User::ROLE_TEACHER && empty($validated['faculty_id'])) {
            return back()->withInput()->withErrors(['faculty_id' => 'Todo docente debe pertenecer a una facultad.']);
        }
        if ($validated['role'] === User::ROLE_STUDENT && empty($validated['career_id'])) {
            return back()->withInput()->withErrors(['career_id' => 'Todo estudiante debe estar asignado a una carrera.']);
        }

        $prefix = match ($validated['role']) {
            User::ROLE_ADMIN => 'ADM',
            User::ROLE_TEACHER => 'DOC',
            default => 'EST',
        };
        $code = $prefix . '-' . now()->format('Y') . str_pad((string) (User::max('id') + 1), 3, '0', STR_PAD_LEFT);

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'role' => $validated['role'],
            'code' => $code,
            'document_number' => $validated['document_number'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'faculty_id' => $validated['role'] === User::ROLE_TEACHER ? $validated['faculty_id'] : null,
            'career_id' => $validated['role'] === User::ROLE_STUDENT ? $validated['career_id'] : null,
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        if ($user->isStudent() && $user->career_id) {
            $user->careers()->syncWithoutDetaching([$user->career_id => ['is_primary' => true, 'is_active' => true]]);
        }
        if ($user->isTeacher() && $user->faculty_id) {
            $user->faculties()->syncWithoutDetaching([$user->faculty_id => ['is_primary' => true]]);
        }

        $this->notifications->logActivity($request->user()->id, 'usuario_creado', "Usuario creado: {$user->name} ({$user->roleLabel()}).");

        return back()->with('success', "Usuario \"{$user->name}\" creado correctamente.");
    }

    /**
     * Ver perfil de usuario (Sección 8):
     * - Estudiante: pestañas "Perfil" (datos, carrera, matrícula) y "Récord académico" (materias, estado y nota final — SIN ver tareas).
     * - Docente: pestañas "Perfil" (datos y facultad) e "Historial" (materias dictadas — SIN ver tareas ni notas).
     */
    public function show(Request $request, User $user): View
    {
        $user->load(['faculty', 'career.faculty']);
        $tab = $request->string('tab')->toString() ?: 'perfil';

        if ($user->isStudent()) {
            $currentSemester = $this->semesters->current();
            $studentCareers = $user->activeCareers();
            $allCareers = Career::where('is_active', true)->with('faculty')->orderBy('faculty_id')->orderBy('name')->get();
            $currentTuitions = Tuition::where('student_id', $user->id)
                ->where('semester_id', $currentSemester->id)
                ->with('career')
                ->get()
                ->keyBy('career_id');
            $allTuitions = Tuition::where('student_id', $user->id)
                ->with(['semester', 'career'])
                ->orderByDesc('semester_id')
                ->get();

            $academicRecord = Enrollment::where('student_id', $user->id)
                ->with(['subject', 'semester', 'offering', 'career.faculty'])
                ->orderByDesc('semester_id')
                ->orderByDesc('attempt_number')
                ->get();

            $progress = $user->career
                ? $this->progressService->buildCareerProgress($user, $user->career)
                : null;

            return view('admin.users.show-student', compact(
                'user',
                'tab',
                'currentSemester',
                'studentCareers',
                'allCareers',
                'currentTuitions',
                'allTuitions',
                'academicRecord',
                'progress'
            ));
        }

        if ($user->isTeacher()) {
            $user->load('faculties');
            $teachingHistory = $this->offerings->getForTeacher($user);
            $teacherFaculties = $user->assignedFaculties();
            $allFaculties = Faculty::where('is_active', true)->orderBy('name')->get();

            return view('admin.users.show-teacher', compact('user', 'tab', 'teachingHistory', 'teacherFaculties', 'allFaculties'));
        }

        return view('admin.users.show-teacher', [
            'user' => $user,
            'tab' => 'perfil',
            'teachingHistory' => collect(),
            'teacherFaculties' => collect(),
            'allFaculties' => collect(),
        ]);
    }

    /**
     * Solo el Administrador puede modificar los datos personales de estudiantes y docentes.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'document_number' => ['nullable', 'string', 'max:30'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $user->update([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'document_number' => array_key_exists('document_number', $validated)
                ? ($validated['document_number'] !== null && trim($validated['document_number']) !== '' ? trim($validated['document_number']) : null)
                : $user->document_number,
            'phone' => array_key_exists('phone', $validated)
                ? ($validated['phone'] !== null && trim($validated['phone']) !== '' ? trim($validated['phone']) : null)
                : $user->phone,
        ]);

        $this->notifications->logActivity(
            $request->user()->id,
            'usuario_actualizado',
            "Datos personales actualizados por Administración para {$user->name} ({$user->roleLabel()})."
        );

        return back()->with('success', "Datos personales de \"{$user->name}\" actualizados correctamente.");
    }

    /**
     * Permite al Administrador asignar múltiples facultades a un docente.
     */
    public function syncFaculties(Request $request, User $user): RedirectResponse
    {
        if (! $user->isTeacher()) {
            abort(403, 'Solo los usuarios con rol Docente pueden tener facultades asignadas.');
        }

        $validated = $request->validate([
            'faculty_ids' => ['required', 'array', 'min:1'],
            'faculty_ids.*' => ['integer', 'exists:faculties,id'],
        ], [
            'faculty_ids.required' => 'El docente debe tener al menos una facultad asignada.',
            'faculty_ids.min' => 'El docente debe tener al menos una facultad asignada.',
        ]);

        $ids = array_values(array_unique(array_map('intval', $validated['faculty_ids'])));
        $primaryId = in_array((int) $user->faculty_id, $ids, true) ? (int) $user->faculty_id : $ids[0];

        $syncPayload = [];
        foreach ($ids as $fid) {
            $syncPayload[$fid] = ['is_primary' => $fid === $primaryId];
        }

        $user->faculties()->sync($syncPayload);
        $user->update(['faculty_id' => $primaryId]);

        $facNames = Faculty::whereIn('id', $ids)->orderBy('name')->pluck('name')->implode(', ');
        $this->notifications->logActivity(
            $request->user()->id,
            'docente_facultades_actualizadas',
            "Facultades actualizadas para el docente {$user->name}: {$facNames}."
        );

        return back()->with('success', "Facultades actualizadas para {$user->name}: {$facNames}.");
    }

    public function addCareer(Request $request, User $user): RedirectResponse
    {
        if (! $user->isStudent()) {
            abort(403);
        }

        $validated = $request->validate([
            'career_id' => ['required', 'exists:careers,id'],
        ]);

        if ($user->activeCareers()->count() >= 2) {
            return back()->with('error', 'Un estudiante puede cursar como máximo 2 carreras simultáneas.');
        }

        $career = Career::with('faculty')->findOrFail($validated['career_id']);
        $user->careers()->syncWithoutDetaching([
            $career->id => ['is_primary' => $user->career_id === null, 'is_active' => true],
        ]);

        if (! $user->career_id) {
            $user->update(['career_id' => $career->id]);
        }

        $this->notifications->logActivity(
            $request->user()->id,
            'doble_carrera_asignada',
            "Se habilitó la carrera {$career->name} ({$career->faculty->name}) para {$user->name}."
        );

        return back()->with('success', "Carrera \"{$career->name}\" asignada a {$user->name} (Doble Carrera habilitada).");
    }

    public function removeCareer(Request $request, User $user, Career $career): RedirectResponse
    {
        if (! $user->isStudent() || (int) $user->career_id === (int) $career->id) {
            return back()->with('error', 'No se puede remover la carrera principal del estudiante.');
        }

        $user->careers()->updateExistingPivot($career->id, ['is_active' => false]);

        return back()->with('success', "Segunda carrera \"{$career->name}\" desactivada para {$user->name}.");
    }

    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', "Estado de \"{$user->name}\" actualizado.");
    }
}
