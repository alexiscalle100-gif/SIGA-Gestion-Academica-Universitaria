@extends('layouts.app')

@section('title', 'Docentes y Estudiantes')
@section('header', 'Gestión de Usuarios')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 h-fit" x-data="{ role: 'estudiante' }">
        <h2 class="font-bold text-[#0F2744] mb-4">Registrar Usuario</h2>
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre completo *</label>
                <input name="name" type="text" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Correo *</label>
                <input name="email" type="email" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Rol *</label>
                <select name="role" x-model="role" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                    <option value="estudiante">Estudiante</option>
                    <option value="docente">Docente</option>
                    <option value="administrador">Administrador</option>
                </select>
            </div>
            <div x-show="role === 'docente'" x-cloak>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Facultad *</label>
                <select name="faculty_id" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                    <option value="">— Seleccionar —</option>
                    @foreach($faculties as $fac)
                        <option value="{{ $fac->id }}">{{ $fac->name }}</option>
                    @endforeach
                </select>
            </div>
            <div x-show="role === 'estudiante'" x-cloak>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Carrera *</label>
                <select name="career_id" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                    <option value="">— Seleccionar —</option>
                    @foreach($careers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->faculty->code }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Contraseña *</label>
                <input name="password" type="password" required minlength="8" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
            </div>
            <button type="submit" class="w-full py-2.5 rounded-lg bg-[#0F2744] hover:bg-[#1E3A5F] text-white text-sm font-semibold cursor-pointer">Crear Usuario</button>
        </form>
    </div>

    <div class="lg:col-span-2 space-y-4">
        <form method="GET" class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-sm flex flex-wrap gap-2.5">
            <select name="role" class="px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                <option value="">Todos los roles</option>
                <option value="administrador" @selected($role === 'administrador')>Administrador</option>
                <option value="docente" @selected($role === 'docente')>Docente</option>
                <option value="estudiante" @selected($role === 'estudiante')>Estudiante</option>
            </select>
            <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nombre, correo o código..." class="px-3 py-2 rounded-lg border border-slate-300 text-sm flex-1 min-w-[180px]">
            <button type="submit" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-semibold cursor-pointer">Filtrar</button>
        </form>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Usuario</th>
                        <th class="px-3 py-3">Rol / Unidad</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $u)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-800">{{ $u->name }}</div>
                                <div class="text-xs text-slate-500">{{ $u->email }} · <span class="font-mono">{{ $u->code }}</span></div>
                            </td>
                            <td class="px-3 py-3 text-xs">
                                <span class="px-2 py-0.5 rounded-full font-semibold {{ $u->isAdmin() ? 'bg-amber-100 text-amber-800' : ($u->isTeacher() ? 'bg-emerald-100 text-emerald-800' : 'bg-sky-100 text-sky-800') }}">{{ $u->roleLabel() }}</span>
                                <div class="text-slate-500 mt-0.5">
                                    @if($u->isTeacher())
                                        {{ $u->assignedFaculties()->pluck('code')->implode(', ') ?: '—' }}
                                    @elseif($u->isStudent())
                                        {{ $u->activeCareers()->pluck('name')->implode(' + ') ?: '—' }}
                                    @else
                                        Administración General
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if(! $u->isAdmin())
                                        <a href="{{ route('admin.users.show', $u) }}" class="px-2.5 py-1 rounded bg-sky-100 text-[#0F2744] text-xs font-bold">Ver Perfil</a>
                                    @endif
                                    @if($u->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.toggle', $u) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="px-2.5 py-1 rounded text-xs font-semibold cursor-pointer {{ $u->is_active ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-800' }}">
                                                {{ $u->is_active ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-4 py-3 border-t border-slate-200">{{ $users->links() }}</div>
        </div>
    </div>
</div>
@endsection
