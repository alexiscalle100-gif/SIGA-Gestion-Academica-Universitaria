<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Models\Faculty;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(private readonly NotificationService $notifications)
    {
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Ingrese su correo institucional.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'password.required' => 'Ingrese su contraseña.',
        ]);

        if (Auth::attempt(array_merge($credentials, ['is_active' => true]), $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = $request->user();

            $this->notifications->logActivity(
                $user->id,
                'inicio_sesion',
                "{$user->name} ({$user->roleLabel()}) inició sesión."
            );

            return redirect()->intended(route('dashboard'))
                ->with('success', "Bienvenido, {$user->name}.");
        }

        return back()
            ->withErrors(['email' => 'Credenciales inválidas o cuenta desactivada.'])
            ->onlyInput('email');
    }

    public function showRegister(): View
    {
        $faculties = Faculty::where('is_active', true)->orderBy('name')->get();
        $careers = Career::where('is_active', true)->with('faculty')->orderBy('name')->get();

        return view('auth.register', compact('faculties', 'careers'));
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'document_number' => ['nullable', 'string', 'max:30'],
            'phone' => ['nullable', 'string', 'max:30'],
            'faculty_id' => ['nullable', 'exists:faculties,id'],
            'career_id' => ['nullable', 'exists:careers,id'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if ($validated['role'] === User::ROLE_TEACHER && empty($validated['faculty_id'])) {
            return back()->withInput()->withErrors(['faculty_id' => 'Los docentes deben pertenecer a una facultad.']);
        }
        if ($validated['role'] === User::ROLE_STUDENT && empty($validated['career_id'])) {
            return back()->withInput()->withErrors(['career_id' => 'Los estudiantes deben seleccionar una carrera.']);
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
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'code' => $code,
            'document_number' => $validated['document_number'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'faculty_id' => $validated['role'] === User::ROLE_TEACHER ? $validated['faculty_id'] : null,
            'career_id' => $validated['role'] === User::ROLE_STUDENT ? $validated['career_id'] : null,
            'is_active' => true,
        ]);

        if ($user->isStudent() && $user->career_id) {
            $user->careers()->syncWithoutDetaching([$user->career_id => ['is_primary' => true, 'is_active' => true]]);
        }
        if ($user->isTeacher() && $user->faculty_id) {
            $user->faculties()->syncWithoutDetaching([$user->faculty_id => ['is_primary' => true]]);
        }

        $this->notifications->notify(
            $user,
            'Cuenta creada',
            "Tu cuenta ({$user->code}) con rol {$user->roleLabel()} se encuentra registrada.",
            'sistema',
            route('dashboard')
        );

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Registro completado exitosamente.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user) {
            $this->notifications->logActivity($user->id, 'cierre_sesion', "{$user->name} cerró sesión.");
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Has cerrado sesión correctamente.');
    }
}
