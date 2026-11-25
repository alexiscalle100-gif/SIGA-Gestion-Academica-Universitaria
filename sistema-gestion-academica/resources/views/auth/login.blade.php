@extends('layouts.guest')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="bg-white rounded-2xl shadow-xl border border-slate-200/80 p-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#0F2744]">Iniciar Sesión</h1>
        <p class="text-sm text-slate-500 mt-1">Accede con tu correo institucional y contraseña.</p>
    </div>

    @if(session('success'))
        <div class="mb-4 px-3.5 py-2.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 px-3.5 py-2.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Correo electrónico</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                   placeholder="usuario@upds.edu.bo"
                   class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-[#1E3A5F] focus:ring-2 focus:ring-sky-500/20 outline-none text-sm">
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">Contraseña</label>
            <input id="password" name="password" type="password" required
                   placeholder="••••••••"
                   class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-[#1E3A5F] focus:ring-2 focus:ring-sky-500/20 outline-none text-sm">
        </div>

        <div class="flex items-center justify-between text-xs">
            <label class="flex items-center gap-2 text-slate-600 cursor-pointer">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-[#1E3A5F]">
                <span>Mantener sesión activa</span>
            </label>
            <a href="{{ route('register') }}" class="font-semibold text-[#1E3A5F] hover:underline">
                ¿No tienes cuenta? Regístrate
            </a>
        </div>

        <button type="submit"
                class="w-full py-2.5 px-4 rounded-lg bg-[#0F2744] hover:bg-[#1E3A5F] text-white font-semibold text-sm shadow-sm transition cursor-pointer">
            Ingresar a la Plataforma
        </button>
    </form>

    <!-- Accesos rápidos para copiar o autocompletar -->
    <div class="mt-6 pt-5 border-t border-slate-200">
        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2.5">Accesos de prueba</div>
        <div class="grid grid-cols-1 gap-2 text-xs">
            <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-between gap-2">
                <div class="min-w-0">
                    <span class="font-bold text-amber-700">Administrador:</span>
                    <span class="font-mono text-slate-700 select-all">admin@upds.edu.bo</span>
                    <span class="text-slate-400 mx-1">/</span>
                    <span class="font-mono text-slate-600 select-all">Admin123*</span>
                </div>
                <button type="button" onclick="document.getElementById('email').value='admin@upds.edu.bo';document.getElementById('password').value='Admin123*';"
                        class="px-2 py-1 rounded bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold text-[11px] shrink-0 cursor-pointer">Usar</button>
            </div>
            <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-between gap-2">
                <div class="min-w-0">
                    <span class="font-bold text-emerald-700">Docente:</span>
                    <span class="font-mono text-slate-700 select-all">docente@upds.edu.bo</span>
                    <span class="text-slate-400 mx-1">/</span>
                    <span class="font-mono text-slate-600 select-all">Docente123*</span>
                </div>
                <button type="button" onclick="document.getElementById('email').value='docente@upds.edu.bo';document.getElementById('password').value='Docente123*';"
                        class="px-2 py-1 rounded bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold text-[11px] shrink-0 cursor-pointer">Usar</button>
            </div>
            <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-between gap-2">
                <div class="min-w-0">
                    <span class="font-bold text-sky-700">Estudiante:</span>
                    <span class="font-mono text-slate-700 select-all">estudiante@upds.edu.bo</span>
                    <span class="text-slate-400 mx-1">/</span>
                    <span class="font-mono text-slate-600 select-all">Estudiante123*</span>
                </div>
                <button type="button" onclick="document.getElementById('email').value='estudiante@upds.edu.bo';document.getElementById('password').value='Estudiante123*';"
                        class="px-2 py-1 rounded bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold text-[11px] shrink-0 cursor-pointer">Usar</button>
            </div>
        </div>
    </div>
</div>
@endsection
