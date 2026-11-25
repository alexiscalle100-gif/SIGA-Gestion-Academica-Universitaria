@extends('layouts.app')

@section('title', $enrollment->offering->subject->code . ' — Mis Actividades')
@section('header', $enrollment->offering->subject->code . ' — ' . $enrollment->offering->subject->name)

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('student.enrollments.index', ['career_id' => $enrollment->career_id]) }}"
            class="text-xs font-semibold text-slate-600 hover:text-[#0F2744]">← Volver a Mis Cursos</a>
        <span
            class="px-3 py-1 rounded-full text-xs font-bold
            {{ $enrollment->status === 'aprobada' ? 'bg-emerald-100 text-emerald-800' : ($enrollment->status === 'reprobada' ? 'bg-rose-100 text-rose-700' : 'bg-sky-100 text-sky-800') }}">
            {{ $enrollment->statusLabel() }} · Intento #{{ $enrollment->attempt_number }}
        </span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase text-slate-500">
                {{ $enrollment->closed_at ? 'Nota Final' : 'Nota Acumulada' }}</div>
            <div class="mt-1 text-3xl font-bold text-[#0F2744]">{{ $accumulatedScore }}<span
                    class="text-base text-slate-400"> / 100</span></div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase text-slate-500">Docente y Horario</div>
            <div class="mt-1 text-base font-bold text-slate-800">{{ $enrollment->offering->teacherLabel() }}</div>
            <div class="text-xs text-slate-500">{{ $enrollment->offering->monthLabel() }} ·
                {{ $enrollment->offering->scheduleText() }} · {{ $enrollment->offering->classroomLabel() }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase text-slate-500">Puntos Evaluados en Plan</div>
            <div class="mt-1 text-3xl font-bold text-sky-700">{{ $totalAssignedPoints }}<span
                    class="text-base text-slate-400"> / 100</span></div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="font-bold text-[#0F2744]">Actividades y Calificaciones (Registradas exclusivamente por el docente)</h2>
        </div>
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3">Actividad</th>
                    <th class="px-3 py-3 text-center">Fecha Entrega</th>
                    <th class="px-3 py-3 text-center">Estado de Entrega</th>
                    <th class="px-3 py-3 text-center">Nota</th>
                    <th class="px-5 py-3">Comentario del Docente</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($enrollment->offering->tasks as $task)
                    @php $tg = $gradesByTask->get($task->id); @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3.5">
                            <div class="font-semibold text-slate-800">{{ $task->title }} <span
                                    class="text-xs text-sky-700 font-bold">({{ $task->max_points }} pts)</span></div>
                            @if($task->description)
                                <div class="text-xs text-slate-500">{{ $task->description }}</div>
                            @endif
                        </td>
                        <td class="px-3 py-3.5 text-center text-xs text-slate-600">{{ $task->due_date->format('d/m/Y') }}</td>
                        <td class="px-3 py-3.5 text-center">
                            @if($tg && $tg->isSubmitted())
                                <span class="px-2.5 py-0.5 rounded-full bg-sky-100 text-sky-800 text-xs font-bold">Presentada</span>
                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $tg->submitted_at->format('d/m/Y H:i') }}</div>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500 text-xs font-semibold">Pendiente</span>
                            @endif
                        </td>
                        <td class="px-3 py-3.5 text-center">
                            @if($tg === null || $tg->score === null)
                                <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold">Pendiente de calificar</span>
                            @else
                                <span class="font-bold text-[#0F2744]">{{ $tg->score }} / {{ $task->max_points }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-xs text-slate-600">{{ $tg?->comment ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-500">El docente aún no registró actividades para esta materia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
