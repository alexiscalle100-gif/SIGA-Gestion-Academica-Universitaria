@extends('layouts.app')

@section('title', 'Mis Calificaciones')
@section('header', 'Mis Calificaciones y Rendimiento Académico')
@section('subheader', 'Consulta tus notas parciales, nota final y progreso acumulado por materia')

@section('content')
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
        <div class="text-xs text-slate-500 uppercase font-semibold">Promedio General</div>
        <div class="mt-1 text-3xl font-bold text-[#0F2744]">{{ number_format($performance['average'], 1) }}<span class="text-sm text-slate-400">/100</span></div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
        <div class="text-xs text-slate-500 uppercase font-semibold">Aprobadas</div>
        <div class="mt-1 text-3xl font-bold text-emerald-700">{{ $performance['passed'] }}</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
        <div class="text-xs text-slate-500 uppercase font-semibold">En Curso</div>
        <div class="mt-1 text-3xl font-bold text-sky-700">{{ $performance['in_progress'] }}</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
        <div class="text-xs text-slate-500 uppercase font-semibold">Reprobadas</div>
        <div class="mt-1 text-3xl font-bold text-rose-600">{{ $performance['failed'] }}</div>
    </div>
</div>

<div class="flex justify-end gap-2 mb-4">
    <a href="{{ route('reports.download', ['type' => 'student-all-grades', 'format' => 'pdf']) }}"
       class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold">Boletín PDF</a>
    <a href="{{ route('reports.download', ['type' => 'student-all-grades', 'format' => 'excel']) }}"
       class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold">Boletín Excel</a>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3.5">Materia</th>
                    <th class="px-4 py-3.5 text-center">P1 (30%)</th>
                    <th class="px-4 py-3.5 text-center">P2 (30%)</th>
                    <th class="px-4 py-3.5 text-center">Final (40%)</th>
                    <th class="px-4 py-3.5 text-center">Nota Final</th>
                    <th class="px-4 py-3.5 w-40">Rendimiento</th>
                    <th class="px-4 py-3.5 text-center">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($enrollments as $enr)
                    @php
                        $g = $enr->grade;
                        $score = $g?->final_score !== null ? (float) $g->final_score : 0;
                    @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3.5">
                            <div class="font-semibold text-[#0F2744]">{{ $enr->course->name }}</div>
                            <div class="text-xs text-slate-500">{{ $enr->course->code }} · {{ $enr->course->teacher->name }}</div>
                        </td>
                        <td class="px-4 py-3.5 text-center">{{ $g?->partial_1 ?? '—' }}</td>
                        <td class="px-4 py-3.5 text-center">{{ $g?->partial_2 ?? '—' }}</td>
                        <td class="px-4 py-3.5 text-center">{{ $g?->final_exam ?? '—' }}</td>
                        <td class="px-4 py-3.5 text-center font-bold text-[#0F2744]">{{ $g?->final_score !== null ? number_format($score, 2) : '—' }}</td>
                        <td class="px-4 py-3.5">
                            <div class="w-full h-2.5 rounded-full bg-slate-200 overflow-hidden">
                                <div class="h-full rounded-full {{ $score >= 51 ? 'bg-emerald-500' : ($score > 0 ? 'bg-rose-500' : 'bg-slate-300') }}"
                                     style="width: {{ min(100, max(0, $score)) }}%"></div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ ($g?->status === 'aprobado') ? 'bg-emerald-100 text-emerald-800' : (($g?->status === 'reprobado') ? 'bg-rose-100 text-rose-800' : 'bg-sky-100 text-sky-800') }}">
                                {{ $g?->statusLabel() ?? 'En curso' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-8 text-center text-slate-500">No tienes materias inscritas para visualizar calificaciones.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
