@extends('layouts.app')

@section('title', 'Mi Perfil')
@section('header', 'Mi Perfil')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 text-center h-fit">
            <div
                class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-[#0F2744] to-sky-600 text-white text-3xl font-bold flex items-center justify-center shadow-md">
                {{ strtoupper(mb_substr($user->name, 0, 1)) }}
            </div>
            <h2 class="mt-4 text-lg font-bold text-[#0F2744]">{{ $user->name }}</h2>
            <p class="text-sm text-slate-500">{{ $user->email }}</p>
            <div class="mt-3 flex items-center justify-center gap-2">
                <span
                    class="px-3 py-1 rounded-full text-xs font-semibold bg-sky-100 text-[#0F2744]">{{ $user->roleLabel() }}</span>
                @if($user->code)
                    <span class="px-3 py-1 rounded-full text-xs font-mono bg-slate-100 text-slate-700">{{ $user->code }}</span>
                @endif
            </div>
            <dl class="mt-6 pt-5 border-t border-slate-200 text-left text-xs space-y-2.5">
                <div class="flex justify-between">
                    <dt class="text-slate-500">CI / Doc.:</dt>
                    <dd class="font-semibold text-slate-700">{{ $user->document_number ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Teléfono:</dt>
                    <dd class="font-semibold text-slate-700">{{ $user->phone ?? '—' }}</dd>
                </div>
                @if($user->isTeacher())
                    <div>
                        <dt class="text-slate-500 mb-1">Facultad(es) asignadas:</dt>
                        <dd class="flex flex-wrap gap-1">
                            @foreach($user->assignedFaculties() as $fac)
                                <span
                                    class="px-2 py-0.5 rounded bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold text-[11px]">{{ $fac->name }}</span>
                            @endforeach
                        </dd>
                    </div>
                @endif
                @if($user->isStudent())
                    <div>
                        <dt class="text-slate-500 mb-1">Carrera(s):</dt>
                        <dd class="flex flex-wrap gap-1">
                            @foreach($user->activeCareers() as $car)
                                <span
                                    class="px-2 py-0.5 rounded bg-sky-50 border border-sky-200 text-[#0F2744] font-semibold text-[11px]">{{ $car->name }}
                                    ({{ $car->faculty->code }})</span>
                            @endforeach
                        </dd>
                    </div>
                @endif
            </dl>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-base font-bold text-[#0F2744] mb-4">Datos Personales</h3>
                @if($user->isStudent())
                    <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-600 mb-4">
                        Los datos personales de los estudiantes son de solo lectura. Si necesitas corregir tu nombre, correo,
                        documento o teléfono, solicita el cambio en <strong>Administración</strong>.
                    </div>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="p-3 rounded-lg bg-slate-50 border border-slate-200">
                            <dt class="text-xs text-slate-500">Nombre completo</dt>
                            <dd class="font-semibold text-slate-800 mt-0.5">{{ $user->name }}</dd>
                        </div>
                        <div class="p-3 rounded-lg bg-slate-50 border border-slate-200">
                            <dt class="text-xs text-slate-500">Correo institucional</dt>
                            <dd class="font-semibold text-slate-800 mt-0.5">{{ $user->email }}</dd>
                        </div>
                        <div class="p-3 rounded-lg bg-slate-50 border border-slate-200">
                            <dt class="text-xs text-slate-500">Documento / CI</dt>
                            <dd class="font-semibold text-slate-800 mt-0.5">{{ $user->document_number ?? '—' }}</dd>
                        </div>
                        <div class="p-3 rounded-lg bg-slate-50 border border-slate-200">
                            <dt class="text-xs text-slate-500">Teléfono</dt>
                            <dd class="font-semibold text-slate-800 mt-0.5">{{ $user->phone ?? '—' }}</dd>
                        </div>
                    </dl>
                @else
                    <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre completo *</label>
                                <input name="name" type="text" value="{{ old('name', $user->name) }}" required
                                    class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Correo electrónico *</label>
                                <input name="email" type="email" value="{{ old('email', $user->email) }}" required
                                    class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Documento / CI</label>
                                <input name="document_number" type="text"
                                    value="{{ old('document_number', $user->document_number) }}"
                                    class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Teléfono</label>
                                <input name="phone" type="text" value="{{ old('phone', $user->phone) }}"
                                    class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm">
                            </div>
                        </div>
                        <button type="submit"
                            class="px-5 py-2.5 rounded-lg bg-[#0F2744] hover:bg-[#1E3A5F] text-white text-sm font-semibold cursor-pointer">Guardar
                            Cambios</button>
                    </form>
                @endif
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-base font-bold text-[#0F2744] mb-4">Cambiar Contraseña</h3>
                <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Contraseña actual *</label>
                        <input name="current_password" type="password" required
                            class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nueva contraseña *</label>
                            <input name="password" type="password" required minlength="8"
                                class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Confirmar *</label>
                            <input name="password_confirmation" type="password" required minlength="8"
                                class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm">
                        </div>
                    </div>
                    <button type="submit"
                        class="px-5 py-2.5 rounded-lg bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold cursor-pointer">Actualizar
                        Contraseña</button>
                </form>
            </div>

            @if($user->isTeacher())
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200">
                        <h3 class="font-bold text-[#0F2744]">Mi Historial de Materias Dictadas</h3>
                    </div>
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3">Periodo</th>
                                <th class="px-4 py-3">Materia</th>
                                <th class="px-4 py-3">Turno / Aula</th>
                                <th class="px-4 py-3 text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($teacherHistory as $off)
                                <tr>
                                    <td class="px-4 py-3 text-xs">{{ $off->semester->code }} · {{ $off->monthLabel() }}</td>
                                    <td class="px-4 py-3 font-semibold text-[#0F2744]">{{ $off->subject->code }} —
                                        {{ $off->subject->name }}</td>
                                    <td class="px-4 py-3 text-xs text-slate-600">{{ $off->scheduleText() }} ·
                                        {{ $off->classroomLabel() }}</td>
                                    <td class="px-4 py-3 text-center text-xs font-semibold">{{ $off->statusLabel() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-6 text-center text-slate-500">Sin historial todavía.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection