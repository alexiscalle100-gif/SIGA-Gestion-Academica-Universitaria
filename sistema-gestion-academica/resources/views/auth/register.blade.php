@extends('layouts.guest')

@section('title', 'Registro de Usuario')

@section('content')
<div class="bg-white rounded-2xl shadow-xl border border-slate-200/80 p-8" x-data="{ role: '{{ old('role', 'estudiante') }}' }">
    <div class="mb-5">
        <h1 class="text-2xl font-bold text-[#0F2744]">Registro de Usuario</h1>
        <p class="text-sm text-slate-500 mt-1">Crea tu cuenta institucional.</p>
    </div>

    @if($errors->any())
        <div class="mb-4 px-3.5 py-2.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register.submit') }}" class="space-y-3.5">
        @csrf
        <div>
            <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Nombre completo *</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required
                   class="w-full px-3.5 py-2 rounded-lg border border-slate-300 focus:border-[#1E3A5F] outline-none text-sm">
        </div>

        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Correo electrónico *</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                   class="w-full px-3.5 py-2 rounded-lg border border-slate-300 focus:border-[#1E3A5F] outline-none text-sm">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label for="role" class="block text-xs font-semibold text-slate-700 mb-1">Rol *</label>
                <select id="role" name="role" x-model="role" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-300 outline-none text-sm bg-white">
                    <option value="estudiante">Estudiante</option>
                    <option value="docente">Docente</option>
                    <option value="administrador">Administrador</option>
                </select>
            </div>
            <div>
                <label for="document_number" class="block text-xs font-semibold text-slate-700 mb-1">Documento / CI</label>
                <input id="document_number" name="document_number" type="text" value="{{ old('document_number') }}"
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 outline-none text-sm">
            </div>
        </div>

        <div x-show="role === 'docente'" x-cloak>
            <label for="faculty_id" class="block text-xs font-semibold text-slate-700 mb-1">Facultad *</label>
            <select id="faculty_id" name="faculty_id" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                <option value="">— Seleccionar facultad —</option>
                @foreach($faculties as $faculty)
                    <option value="{{ $faculty->id }}" @selected(old('faculty_id') == $faculty->id)>{{ $faculty->name }}</option>
                @endforeach
            </select>
        </div>

        <div x-show="role === 'estudiante'" x-cloak>
            <label for="career_id" class="block text-xs font-semibold text-slate-700 mb-1">Carrera *</label>
            <select id="career_id" name="career_id" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                <option value="">— Seleccionar carrera —</option>
                @foreach($careers as $career)
                    <option value="{{ $career->id }}" @selected(old('career_id') == $career->id)>{{ $career->name }} ({{ $career->faculty->code }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="phone" class="block text-xs font-semibold text-slate-700 mb-1">Teléfono</label>
            <input id="phone" name="phone" type="text" value="{{ old('phone') }}"
                   class="w-full px-3.5 py-2 rounded-lg border border-slate-300 outline-none text-sm">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Contraseña *</label>
                <input id="password" name="password" type="password" required minlength="8"
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 outline-none text-sm">
            </div>
            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">Confirmar *</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8"
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 outline-none text-sm">
            </div>
        </div>

        <button type="submit" class="w-full mt-2 py-2.5 px-4 rounded-lg bg-[#0F2744] hover:bg-[#1E3A5F] text-white font-semibold text-sm cursor-pointer">
            Crear Cuenta
        </button>
    </form>

    <div class="mt-5 text-center text-xs text-slate-500">
        ¿Ya tienes una cuenta?
        <a href="{{ route('login') }}" class="font-semibold text-[#1E3A5F] hover:underline">Inicia sesión</a>
    </div>
</div>
@endsection
