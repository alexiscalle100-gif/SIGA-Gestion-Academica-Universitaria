@extends('layouts.app')

@section('title', 'Perfil Docente — ' . $user->name)
@section('header', ($user->isTeacher() ? 'Docente: ' : 'Usuario: ') . $user->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.users.index', ['role' => $user->role]) }}" class="text-xs font-semibold text-slate-600 hover:text-[#0F2744]">← Volver a Usuarios</a>
</div>

<div class="mb-6 border-b border-slate-200 flex gap-2">
    <a href="{{ route('admin.users.show', [$user, 'tab' => 'perfil']) }}"
       class="px-5 py-3 text-sm font-bold border-b-2 {{ $tab === 'perfil' ? 'border-[#0F2744] text-[#0F2744]' : 'border-transparent text-slate-500' }}">
        Perfil
    </a>
    @if($user->isTeacher())
        <a href="{{ route('admin.users.show', [$user, 'tab' => 'historial']) }}"
           class="px-5 py-3 text-sm font-bold border-b-2 {{ $tab === 'historial' ? 'border-[#0F2744] text-[#0F2744]' : 'border-transparent text-slate-500' }}">
            Historial ({{ $teachingHistory->count() }})
        </a>
    @endif
</div>

@if($tab === 'perfil')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h2 class="font-bold text-[#0F2744] mb-4">Datos Personales</h2>
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-3 text-xs">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">Nombre completo *</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">Código</label>
                        <input type="text" value="{{ $user->code }}" disabled class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 font-mono text-sm text-slate-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">Correo electrónico *</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">Documento / CI</label>
                        <input type="text" name="document_number" value="{{ old('document_number', $user->document_number) }}" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-600 mb-1">Teléfono</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                    </div>
                </div>
                <button type="submit" class="px-4 py-2 rounded-lg bg-[#0F2744] hover:bg-[#1E3A5F] text-white text-xs font-bold cursor-pointer">
                    Guardar Datos Personales
                </button>
            </form>
        </div>

        @if($user->isTeacher())
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h2 class="font-bold text-[#0F2744] mb-1">Facultades Asignadas (Múltiples Facultades)</h2>
                <p class="text-xs text-slate-500 mb-4">Un docente puede dictar materias en una o varias facultades según la asignación administrativa.</p>
                <form method="POST" action="{{ route('admin.users.faculties.sync', $user) }}" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <div class="space-y-2">
                        @foreach($allFaculties as $fac)
                            @php $checked = $teacherFaculties->contains('id', $fac->id); @endphp
                            <label class="flex items-center justify-between gap-3 p-3 rounded-xl border cursor-pointer {{ $checked ? 'border-[#0F2744] bg-sky-50/60' : 'border-slate-200 bg-white hover:bg-slate-50' }}">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" name="faculty_ids[]" value="{{ $fac->id }}" @checked($checked) class="rounded border-slate-300">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-800">{{ $fac->name }}</div>
                                        <div class="text-xs text-slate-500 font-mono">{{ $fac->code }}</div>
                                    </div>
                                </div>
                                @if($checked)
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold">Asignada</span>
                                @endif
                            </label>
                        @endforeach
                    </div>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold cursor-pointer">
                        Actualizar Facultades del Docente
                    </button>
                </form>
            </div>
        @endif
    </div>
@else
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="font-bold text-[#0F2744]">Historial de Materias Dictadas</h2>
        </div>
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3">Periodo</th>
                    <th class="px-4 py-3">Materia</th>
                    <th class="px-3 py-3">Turno / Aula</th>
                    <th class="px-3 py-3 text-center">Inscritos</th>
                    <th class="px-3 py-3 text-center">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($teachingHistory as $off)
                    <tr>
                        <td class="px-4 py-3 text-xs">{{ $off->semester->code }} · {{ $off->monthLabel() }}</td>
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $off->subject->code }} — {{ $off->subject->name }}</td>
                        <td class="px-3 py-3 text-xs text-slate-600">{{ $off->scheduleText() }} · {{ $off->classroomLabel() }}</td>
                        <td class="px-3 py-3 text-center text-xs font-semibold">{{ $off->active_enrollments_count }}</td>
                        <td class="px-3 py-3 text-center text-xs font-semibold">{{ $off->statusLabel() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-slate-500">Sin ofertas registradas aún.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endif
@endsection
