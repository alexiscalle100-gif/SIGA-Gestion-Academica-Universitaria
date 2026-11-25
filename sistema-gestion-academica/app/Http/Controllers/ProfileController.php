<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\OfferingRepositoryInterface;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private readonly NotificationService $notifications,
        private readonly OfferingRepositoryInterface $offerings,
    ) {}

    public function show(Request $request): View
    {
        $user = $request->user()->load(['faculty', 'faculties', 'career.faculty', 'careers.faculty']);
        $teacherHistory = $user->isTeacher() ? $this->offerings->getForTeacher($user) : collect();

        return view('profile.show', compact('user', 'teacherHistory'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->isStudent()) {
            abort(403, 'Los datos personales del estudiante solo pueden ser modificados por Administración.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'document_number' => ['nullable', 'string', 'max:30'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'document_number' => $validated['document_number'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ]);

        $this->notifications->logActivity($user->id, 'perfil_actualizado', "{$user->name} actualizó sus datos personales.");

        return back()->with('success', 'Tus datos personales fueron actualizados.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.current_password' => 'La contraseña actual es incorrecta.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);

        $user->update(['password' => Hash::make($validated['password'])]);

        $this->notifications->notify(
            $user,
            'Contraseña actualizada',
            'Tu contraseña ha sido modificada correctamente.',
            'sistema',
            route('profile.show')
        );

        return back()->with('success', 'Contraseña actualizada con éxito.');
    }
}
