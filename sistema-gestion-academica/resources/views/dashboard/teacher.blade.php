@extends('layouts.app')

@section('title', 'Dashboard Docente')
@section('header', 'Panel del Docente')

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold">
            Facultad(es): {{ $user->assignedFaculties()->pluck('name')->implode(' · ') ?: 'Sin asignar' }} · Semestre {{ $currentSemester->code }}
        </span>
        <a href="{{ route('teacher.offerings.index') }}"
            class="px-4 py-2 rounded-lg bg-[#0F2744] hover:bg-[#1E3A5F] text-white text-xs font-semibold">
            Gestionar Mis Materias y Tareas
        </a>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-7">
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase text-slate-500">Ofertas Activas</div>
            <div class="mt-2 text-3xl font-bold text-[#0F2744]">{{ $stats['active_offerings'] }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase text-slate-500">Estudiantes Activos</div>
            <div class="mt-2 text-3xl font-bold text-sky-700">{{ $stats['total_students'] }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase text-slate-500">Materias Cerradas</div>
            <div class="mt-2 text-3xl font-bold text-emerald-700">{{ $stats['closed_offerings'] }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase text-slate-500">Historial Total</div>
            <div class="mt-2 text-3xl font-bold text-slate-700">{{ $stats['total_offerings'] }}</div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="font-bold text-[#0F2744]">Mis Ofertas Asignadas</h2>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($myOfferings as $off)
                @php $style = $off->subject->areaStyle();
                $pts = (int) ($off->tasks_sum_max_points ?? 0); @endphp
                <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50">
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span
                                class="px-2 py-0.5 rounded font-mono text-xs font-bold {{ $style['bg'] }} {{ $style['text'] }}">{{ $off->subject->code }}</span>
                            <span class="text-xs text-slate-500">{{ $off->semester->code }} · {{ $off->monthLabel() }} ·
                                {{ $off->scheduleText() }} · {{ $off->classroomLabel() }}</span>
                        </div>
                        <div class="font-bold text-slate-800 mt-1">{{ $off->subject->name }}</div>
                        <div class="text-xs text-slate-500 mt-0.5">
                            {{ $off->start_date->format('d/m/Y') }} al {{ $off->end_date->format('d/m/Y') }} · Tareas
                            configuradas: <strong
                                class="{{ $pts === 100 ? 'text-emerald-700' : 'text-amber-700' }}">{{ $pts }}/100 pts</strong>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <span
                            class="px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">{{ $off->active_enrollments_count }}
                            inscritos</span>
                        <span
                            class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $off->isManuallyClosed() ? 'bg-emerald-100 text-emerald-800' : ($off->is_disabled ? 'bg-rose-100 text-rose-800' : 'bg-sky-100 text-sky-800') }}">
                            {{ $off->statusLabel() }}
                        </span>
                        <a href="{{ route('teacher.offerings.show', $off) }}"
                            class="px-3.5 py-2 rounded-lg bg-[#0F2744] hover:bg-[#1E3A5F] text-white text-xs font-semibold">
                            Abrir Aula
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-sm text-slate-500">Aún no tienes ofertas asignadas.</div>
            @endforelse
        </div>
    </div>
@endsection