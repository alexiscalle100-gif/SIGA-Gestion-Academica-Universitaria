@extends('layouts.app')

@section('title', 'Avance de Pensum e Historial')
@section('header', 'Avance de Pensum e Historial Académico')

@section('content')
@if($studentCareers->count() > 1)
    <div class="mb-6 bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex flex-wrap items-center gap-2.5">
        <span class="text-xs font-bold uppercase text-slate-500 mr-2">Ver Pensum de:</span>
        @foreach($studentCareers as $car)
            <a href="{{ route('student.progress', ['career_id' => $car->id]) }}"
               class="px-4 py-2 rounded-lg text-xs font-bold border transition
               {{ $selectedCareer?->id === $car->id ? 'bg-[#0F2744] text-white border-[#0F2744]' : 'bg-slate-50 text-slate-700 border-slate-300' }}">
                {{ $car->name }} ({{ $car->faculty->code }})
            </a>
        @endforeach
    </div>
@endif

@if($progress)
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="text-xs text-slate-500 uppercase font-semibold">Avance Total</div>
            <div class="mt-1 text-2xl font-bold text-[#0F2744]">{{ $progress['summary']['percent'] }}%</div>
            <div class="text-xs text-slate-400">{{ $progress['summary']['aprobada'] }}/{{ $progress['summary']['total'] }} materias</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="text-xs text-slate-500 uppercase font-semibold">Aprobadas</div>
            <div class="mt-1 text-2xl font-bold text-emerald-700">{{ $progress['summary']['aprobada'] }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="text-xs text-slate-500 uppercase font-semibold">En Curso</div>
            <div class="mt-1 text-2xl font-bold text-sky-700">{{ $progress['summary']['en_curso'] }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="text-xs text-slate-500 uppercase font-semibold">Reprobadas</div>
            <div class="mt-1 text-2xl font-bold text-rose-600">{{ $progress['summary']['reprobada'] }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="text-xs text-slate-500 uppercase font-semibold">Promedio Final</div>
            <div class="mt-1 text-2xl font-bold text-indigo-700">{{ number_format($averageScore, 1) }}</div>
        </div>
    </div>

    <!-- Malla de 8 niveles / 48 materias -->
    <div class="mb-8">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
            <h2 class="font-bold text-[#0F2744] text-base">Pensum: {{ $selectedCareer->name }} — {{ $selectedCareer->faculty->name }} (48 materias)</h2>
            <div class="flex flex-wrap gap-2 text-[11px] font-bold">
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Aprobada</span>
                <span class="px-2.5 py-0.5 rounded-full bg-sky-100 text-sky-800">En curso</span>
                <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-700">Reprobada</span>
                <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600">No cursada</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($progress['levels'] as $level => $rows)
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-4 py-2.5 bg-[#0F2744] text-white text-xs font-bold flex justify-between">
                        <span>Nivel {{ $level }}</span>
                        <span>6 meses</span>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @foreach($rows as $r)
                            @php
                                $subj = $r['subject'];
                                $style = $subj->areaStyle();
                                $st = $r['status'];
                                $badgeClass = match($st) {
                                    'aprobada' => 'bg-emerald-100 text-emerald-800',
                                    'reprobada' => 'bg-rose-100 text-rose-700',
                                    'en_curso' => 'bg-sky-100 text-sky-800',
                                    default => 'bg-slate-100 text-slate-500',
                                };
                                $stLabel = match($st) {
                                    'aprobada' => 'Aprobada (' . $r['latest_enrollment']->final_score . ')',
                                    'reprobada' => 'Reprobada (' . $r['latest_enrollment']->final_score . ')',
                                    'en_curso' => 'En curso',
                                    default => 'No cursada',
                                };
                            @endphp
                            <div class="px-4 py-2.5 flex items-center justify-between gap-2 text-xs">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-slate-400 font-bold">M{{ $r['item']->month_in_level }}</span>
                                        <span class="px-1.5 py-0.5 rounded font-mono text-[10px] font-bold border {{ $style['bg'] }} {{ $style['text'] }} {{ $style['border'] }}">{{ $subj->code }}</span>
                                        <span class="font-semibold text-slate-800 truncate">{{ $subj->name }}</span>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold shrink-0 {{ $badgeClass }}">{{ $stLabel }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<!-- Historial completo de intentos -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
        <h2 class="font-bold text-[#0F2744]">Historial Completo de Intentos y Notas</h2>
        <div class="flex gap-2">
            <a href="{{ route('reports.download', ['type' => 'student-history', 'format' => 'pdf']) }}" class="px-3 py-1.5 rounded-lg bg-rose-600 text-white text-xs font-semibold">PDF</a>
            <a href="{{ route('reports.download', ['type' => 'student-history', 'format' => 'excel']) }}" class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-semibold">Excel</a>
        </div>
    </div>
    <table class="w-full text-sm text-left">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
            <tr>
                <th class="px-4 py-3">Semestre / Mes</th>
                <th class="px-4 py-3">Materia</th>
                <th class="px-3 py-3 text-center">Intento</th>
                <th class="px-3 py-3 text-center">Nota Final</th>
                <th class="px-3 py-3 text-center">Estado</th>
                <th class="px-4 py-3 text-right">Detalle</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($history as $enr)
                <tr>
                    <td class="px-4 py-3 text-xs">{{ $enr->semester->code }} · {{ $enr->offering->monthLabel() }}</td>
                    <td class="px-4 py-3 font-semibold text-slate-800">{{ $enr->subject->code }} — {{ $enr->subject->name }}</td>
                    <td class="px-3 py-3 text-center text-xs font-semibold">#{{ $enr->attempt_number }}</td>
                    <td class="px-3 py-3 text-center font-bold text-[#0F2744]">{{ $enr->final_score ?? ($enr->status === 'en_curso' ? $enr->accumulatedScore() . ' (acum.)' : '—') }}</td>
                    <td class="px-3 py-3 text-center">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                            {{ $enr->status === 'aprobada' ? 'bg-emerald-100 text-emerald-800' : ($enr->status === 'reprobada' ? 'bg-rose-100 text-rose-700' : ($enr->status === 'anulada' ? 'bg-slate-200 text-slate-600' : 'bg-sky-100 text-sky-800')) }}">
                            {{ $enr->statusLabel() }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        @if($enr->status !== 'anulada')
                            <a href="{{ route('student.enrollments.show', $enr) }}" class="text-xs font-bold text-sky-700 hover:underline">Ver tareas</a>
                        @else
                            <span class="text-xs text-slate-400">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="p-6 text-center text-slate-500">Sin intentos registrados todavía.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
