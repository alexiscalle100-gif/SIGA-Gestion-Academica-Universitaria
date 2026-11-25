@extends('layouts.app')

@section('title', 'Perfil Docente — ' . $user->name)
@section('header', 'Docente: ' . $user->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.users.index', ['role' => 'docente']) }}" class="text-xs font-semibold text-slate-600 hover:text-[#0F2744]">← Volver a Usuarios</a>
</div>

<!-- Dos pestañas exactas de la Sección 8: Perfil e Historial -->
<div class="mb-6 border-b border-slate-200 flex gap-2">
    <a href="{{ route('admin.users.show', [$user, 'tab' => 'perfil']) }}"
       class="px-5 py-3 text-sm font-bold border-b-2 {{ $tab === 'perfil' ? 'border-[#0F2744] text-[#0F2744]' : 'border-transparent text-slate-500' }}">
        Perfil
    </a>
    <a href="{{ route('admin.users.show', [$user, 'tab' => 'historial']) }}"
       class="px-5 py-3 text-sm font-bold border-b-2 {{ $tab === 'historial' ? 'border-[#0F2744] text-[#0F2744]' : 'border-transparent text-slate-500' }}">
        Historial ({{ $teachingHistory->count() }})
    </a>
</div>

@if($tab === 'perfil')
    <div class="max-w-xl bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h2 class="font-bold text-[#0F2744] mb-4">Datos del Docente</h2>
        <dl class="space-y-2.5 text-sm">
            <div class="flex justify-between"><dt class="text-slate-500">Nombre:</dt><dd class="font-semibold">{{ $user->name }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Código:</dt><dd class="font-mono font-semibold">{{ $user->code }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Correo:</dt><dd>{{ $user->email }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Documento / CI:</dt><dd>{{ $user->document_number ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Teléfono:</dt><dd>{{ $user->phone ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Facultad:</dt><dd class="font-bold text-[#0F2744]">{{ $user->faculty->name ?? '—' }}</dd></div>
        </dl>
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
                        <td class="px-3 py-3 text-xs text-slate-600">{{ $off->shiftLabel() }} · Aula {{ $off->classroom }}</td>
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
