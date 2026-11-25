@extends('layouts.app')

@section('title', 'Gestión de Usuarios')
@section('header', 'Gestión de Usuarios y Roles')
@section('subheader', 'Crea cuentas, asigna o cambia roles y administra el acceso al sistema')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Formulario crear usuario -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 h-fit" x-data="{ role: 'estudiante' }">
        <h2 class="font-bold text-[#0F2744] mb-4">Nuevo Usuario</h2>
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre completo *</label>
                <input name="name" type="text" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Correo electrónico *</label>
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
            <div x-show="role === 'estudiante'" x-cloak>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Carrera</label>
                <select name="career_id" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                    <option value="">— Seleccionar —</option>
                    @foreach($careers as $c)
                        <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">CI / Doc.</label>
                    <input name="document_number" type="text" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Teléfono</label>
                    <input name="phone" type="text" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Contraseña temporal *</label>
                <input name="password" type="password" required minlength="8" placeholder="Mínimo 8 caracteres"
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
            </div>
            <button type="submit" class="w-full py-2.5 rounded-lg bg-[#0F2744] hover:bg-[#1E3A5F] text-white text-sm font-semibold cursor-pointer">
                Crear Usuario
            </button>
        </form>
    </div>

    <!-- Listado y control de roles -->
    <div class="lg:col-span-2 space-y-4">
        <form method="GET" class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-sm flex flex-wrap gap-2.5">
            <select name="role" class="px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                <option value="">Todos los roles</option>
                <option value="administrador" @selected($role === 'administrador')>Administrador</option>
                <option value="docente" @selected($role === 'docente')>Docente</option>
                <option value="estudiante" @selected($role === 'estudiante')>Estudiante</option>
            </select>
            <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nombre, correo o código..."
                   class="px-3 py-2 rounded-lg border border-slate-300 text-sm flex-1 min-w-[180px]">
            <button type="submit" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-semibold cursor-pointer">Filtrar</button>
        </form>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Usuario</th>
                            <th class="px-4 py-3">Asignar Rol</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($users as $u)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-800">{{ $u->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $u->email }} · <span class="font-mono">{{ $u->code ?? '—' }}</span></div>
                                </td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('admin.users.role', $u) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" class="px-2.5 py-1.5 rounded border border-slate-300 text-xs bg-white">
                                            <option value="administrador" @selected($u->role === 'administrador')>Administrador</option>
                                            <option value="docente" @selected($u->role === 'docente')>Docente</option>
                                            <option value="estudiante" @selected($u->role === 'estudiante')>Estudiante</option>
                                        </select>
                                        <button type="submit" class="px-2.5 py-1.5 rounded bg-sky-100 text-[#0F2744] text-xs font-bold hover:bg-sky-200 cursor-pointer">
                                            Aplicar
                                        </button>
                                    </form>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($u->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                              onsubmit="return confirm('¿Eliminar permanentemente al usuario {{ $u->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1.5 rounded bg-rose-100 text-rose-700 text-xs font-bold hover:bg-rose-200 cursor-pointer">
                                                Eliminar
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Tu cuenta</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-slate-200">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
