@extends('layouts.app')

@section('title', 'Mis Materias')
@section('header', 'Mis Ofertas Académicas')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    @forelse($myOfferings as $off)
        @php $style = $off->subject->areaStyle(); $pts = (int)($off->tasks_sum_max_points ?? 0); @endphp
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between overflow-hidden">
            <div class="p-5">
                <div class="flex items-center justify-between gap-2 mb-2">
                    <span class="px-2.5 py-0.5 rounded font-mono text-xs font-bold border {{ $style['bg'] }} {{ $style['text'] }} {{ $style['border'] }}">{{ $off->subject->code }}</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $off->isManuallyClosed() ? 'bg-emerald-100 text-emerald-800' : ($off->is_disabled ? 'bg-rose-100 text-rose-700' : 'bg-sky-100 text-sky-800') }}">
                        {{ $off->statusLabel() }}
                    </span>
                </div>
                <h2 class="font-bold text-base text-[#0F2744]">{{ $off->subject->name }}</h2>
                <div class="text-xs text-slate-500 mt-1">{{ $off->semester->code }} · {{ $off->monthLabel() }} · {{ $off->scheduleText() }}</div>
                <dl class="mt-4 space-y-1.5 text-xs text-slate-600">
                    <div class="flex justify-between"><dt class="text-slate-400">Fechas:</dt><dd class="font-semibold">{{ $off->start_date->format('d/m/Y') }} - {{ $off->end_date->format('d/m/Y') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-400">Aula / Inscritos:</dt><dd class="font-semibold">Aula {{ $off->classroom }} · {{ $off->active_enrollments_count }}/{{ $off->max_capacity }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-400">Puntaje Tareas:</dt><dd class="font-bold {{ $pts === 100 ? 'text-emerald-700' : 'text-amber-600' }}">{{ $pts }} / 100 pts</dd></div>
                </dl>
            </div>
            <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                <a href="{{ route('teacher.offerings.show', [$off, 'tab' => 'tareas']) }}" class="text-xs font-semibold text-slate-600 hover:text-[#0F2744]">Tareas</a>
                <a href="{{ route('teacher.offerings.show', $off) }}" class="px-3.5 py-1.5 rounded-lg bg-[#0F2744] hover:bg-[#1E3A5F] text-white text-xs font-bold">Gestionar Materia →</a>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white rounded-xl border border-slate-200 p-10 text-center text-slate-500">
            No tienes ofertas asignadas actualmente.
        </div>
    @endforelse
</div>
@endsection
