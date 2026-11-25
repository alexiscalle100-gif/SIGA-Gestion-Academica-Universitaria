@extends('layouts.app')

@section('title', 'Registro de Calificaciones')
@section('header', 'Registro y Gestión de Calificaciones')
@section('subheader', 'Asigna o edita notas parciales y finales. El promedio ponderado (30% / 30% / 40%) se calcula automáticamente.')

@section('content')
<!-- Selector de curso -->
<div class="bg-white rounded-xl border border-slate-200 p-4 mb-6 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <form method="GET" class="flex flex-wrap items-center gap-3 flex-1">
        <label class="text-sm font-semibold text-[#0F2744]">Seleccionar Curso:</label>
        <select name="course_id" onchange="this.form.submit()" class="px-3.5 py-2 rounded-lg border border-slate-300 text-sm bg-white min-w-[280px]">
            @forelse($courses as $c)
                <option value="{{ $c->id }}" @selected($selectedCourse && $selectedCourse->id === $c->id)>
                    {{ $c->code }} — {{ $c->name }} ({{ $c->term }})
                </option>
            @empty
                <option value="">Sin cursos disponibles</option>
            @endforelse
        </select>
    </form>

    @if($selectedCourse)
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.download', ['type' => 'course-grades-all-students', 'format' => 'pdf', 'course_id' => $selectedCourse->id]) }}"
               class="px-3.5 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold">Exportar PDF</a>
            <a href="{{ route('reports.download', ['type' => 'course-grades-all-students', 'format' => 'excel', 'course_id' => $selectedCourse->id]) }}"
               class="px-3.5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold">Exportar Excel</a>
        </div>
    @endif
</div>

@if($selectedCourse)
    <!-- KPIs de rendimiento del curso -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="text-xs text-slate-500 uppercase font-semibold">Inscritos</div>
            <div class="mt-1 text-2xl font-bold text-[#0F2744]">{{ $courseMetrics['count'] }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="text-xs text-slate-500 uppercase font-semibold">Promedio Curso</div>
            <div class="mt-1 text-2xl font-bold text-sky-700">{{ number_format($courseMetrics['average'], 1) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="text-xs text-slate-500 uppercase font-semibold">Aprobados (≥51)</div>
            <div class="mt-1 text-2xl font-bold text-emerald-700">{{ $courseMetrics['passed'] }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="text-xs text-slate-500 uppercase font-semibold">Reprobados (&lt;51)</div>
            <div class="mt-1 text-2xl font-bold text-rose-600">{{ $courseMetrics['failed'] }}</div>
        </div>
    </div>

    <!-- Planilla editable de calificaciones -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="font-bold text-[#0F2744]">Planilla de Calificaciones — {{ $selectedCourse->code }} {{ $selectedCourse->name }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Estudiante</th>
                        <th class="px-3 py-3 text-center">Parcial 1 (30%)</th>
                        <th class="px-3 py-3 text-center">Parcial 2 (30%)</th>
                        <th class="px-3 py-3 text-center">Final (40%)</th>
                        <th class="px-3 py-3 text-center">Nota Final</th>
                        <th class="px-3 py-3">Observaciones</th>
                        <th class="px-4 py-3 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($courseEnrollments as $enr)
                        @php $g = $enr->grade; @endphp
                        <tr class="hover:bg-slate-50">
                            <form method="POST" action="{{ route('grades.update', $enr) }}">
                                @csrf
                                @method('PUT')
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-800">{{ $enr->student->name }}</div>
                                    <div class="text-xs text-slate-500 font-mono">{{ $enr->student->code }}</div>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <input type="number" step="0.01" min="0" max="100" name="partial_1" value="{{ $g?->partial_1 }}"
                                           class="w-20 px-2 py-1.5 text-center rounded border border-slate-300 text-sm">
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <input type="number" step="0.01" min="0" max="100" name="partial_2" value="{{ $g?->partial_2 }}"
                                           class="w-20 px-2 py-1.5 text-center rounded border border-slate-300 text-sm">
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <input type="number" step="0.01" min="0" max="100" name="final_exam" value="{{ $g?->final_exam }}"
                                           class="w-20 px-2 py-1.5 text-center rounded border border-slate-300 text-sm">
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <div class="font-bold text-[#0F2744]">{{ $g?->final_score !== null ? number_format((float)$g->final_score, 2) : '—' }}</div>
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[11px] font-semibold
                                        {{ ($g?->status === 'aprobado') ? 'bg-emerald-100 text-emerald-800' : (($g?->status === 'reprobado') ? 'bg-rose-100 text-rose-800' : 'bg-sky-100 text-sky-800') }}">
                                        {{ $g?->statusLabel() ?? 'En curso' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3">
                                    <input type="text" name="observations" value="{{ $g?->observations }}" placeholder="Comentario opcional"
                                           class="w-44 px-2.5 py-1.5 rounded border border-slate-300 text-xs">
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button type="submit" class="px-3.5 py-1.5 rounded-lg bg-[#0F2744] hover:bg-[#1E3A5F] text-white text-xs font-semibold cursor-pointer">
                                        Guardar
                                    </button>
                                </td>
                            </form>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-8 text-center text-slate-500">No hay estudiantes inscritos en este curso.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="bg-white rounded-xl border border-slate-200 p-10 text-center text-slate-500">
        No tienes cursos asignados para registrar calificaciones.
    </div>
@endif
@endsection
