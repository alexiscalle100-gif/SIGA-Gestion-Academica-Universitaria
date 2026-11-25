@extends('layouts.app')

@section('title', $offering->subject->code . ' — Aula Docente')
@section('header', $offering->subject->code . ' — ' . $offering->subject->name)

@section('content')
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div class="flex flex-wrap items-center gap-2 text-xs">
        <a href="{{ route('teacher.offerings.index') }}" class="font-semibold text-slate-600 hover:text-[#0F2744]">← Mis Materias</a>
        <span class="text-slate-300">|</span>
        <span class="px-2.5 py-1 rounded-full bg-slate-100 font-semibold text-slate-700">{{ $offering->semester->code }} · {{ $offering->monthLabel() }} · {{ $offering->scheduleText() }} · Aula {{ $offering->classroom }}</span>
        <span class="px-2.5 py-1 rounded-full font-bold {{ $totalTaskPoints === 100 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
            Tareas: {{ $totalTaskPoints }} / 100 pts
        </span>
        <span class="px-2.5 py-1 rounded-full font-bold {{ $offering->isManuallyClosed() ? 'bg-emerald-100 text-emerald-800' : 'bg-sky-100 text-sky-800' }}">
            {{ $offering->statusLabel() }}
        </span>
    </div>

    <div class="flex items-center gap-2" x-data="{ closeModal: false }">
        <a href="{{ route('reports.download', ['type' => 'offering-grades', 'format' => 'pdf', 'offering_id' => $offering->id]) }}" class="px-3 py-1.5 rounded-lg bg-rose-600 text-white text-xs font-semibold">Acta PDF</a>
        <a href="{{ route('reports.download', ['type' => 'offering-grades', 'format' => 'excel', 'offering_id' => $offering->id]) }}" class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-semibold">Acta Excel</a>

        @if(! $offering->isManuallyClosed() && ! $offering->is_disabled)
            <button type="button"
                    @click="closeModal = true"
                    {{ $totalTaskPoints !== 100 ? 'disabled' : '' }}
                    class="px-4 py-1.5 rounded-lg text-xs font-bold {{ $totalTaskPoints === 100 ? 'bg-[#0F2744] hover:bg-[#1E3A5F] text-white cursor-pointer' : 'bg-slate-200 text-slate-400 cursor-not-allowed' }}"
                    title="{{ $totalTaskPoints !== 100 ? 'Las tareas deben sumar exactamente 100 puntos para cerrar' : 'Cerrar materia y congelar notas' }}">
                Cerrar Materia
            </button>

            <div x-show="closeModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4">
                <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                    <h3 class="text-lg font-bold text-[#0F2744]">Confirmar Cierre de Materia</h3>
                    <p class="text-sm text-slate-600 mt-2">
                        Al cerrar <strong>{{ $offering->subject->code }}</strong>, el sistema calculará la nota final entera (0–100) de cada estudiante, asignará <strong>Aprobada (≥ 51)</strong> o <strong>Reprobada (&lt; 51)</strong> y congelará las notas.
                    </p>
                    @if($ungradedCount > 0)
                        <div class="mt-3 p-3 rounded-xl bg-amber-50 border border-amber-300 text-xs text-amber-900 font-semibold">
                            ⚠ Aviso: existen {{ $ungradedCount }} tarea(s) "Sin calificar". Al cerrar la materia, toda tarea sin calificar contará automáticamente como 0.
                        </div>
                    @endif
                    <div class="mt-5 flex justify-end gap-2.5">
                        <button type="button" @click="closeModal = false" class="px-4 py-2 rounded-lg border border-slate-300 text-sm font-semibold text-slate-700 cursor-pointer">Cancelar</button>
                        <form method="POST" action="{{ route('teacher.offerings.close', $offering) }}">
                            @csrf
                            <button type="submit" class="px-5 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold cursor-pointer">Sí, Cerrar Materia</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Pestañas del aula docente -->
<div class="mb-6 border-b border-slate-200 flex gap-2">
    <a href="{{ route('teacher.offerings.show', [$offering, 'tab' => 'inscritos']) }}"
       class="px-5 py-3 text-sm font-bold border-b-2 {{ $tab === 'inscritos' ? 'border-[#0F2744] text-[#0F2744]' : 'border-transparent text-slate-500' }}">
        Estudiantes Inscritos ({{ $offering->activeEnrollments->count() }})
    </a>
    <a href="{{ route('teacher.offerings.show', [$offering, 'tab' => 'tareas']) }}"
       class="px-5 py-3 text-sm font-bold border-b-2 {{ $tab === 'tareas' ? 'border-[#0F2744] text-[#0F2744]' : 'border-transparent text-slate-500' }}">
        Tareas ({{ $offering->tasks->count() }} · {{ $totalTaskPoints }}/100 pts)
    </a>
    <a href="{{ route('teacher.offerings.show', [$offering, 'tab' => 'calificaciones']) }}"
       class="px-5 py-3 text-sm font-bold border-b-2 {{ $tab === 'calificaciones' ? 'border-[#0F2744] text-[#0F2744]' : 'border-transparent text-slate-500' }}">
        Calificar Tareas
    </a>
</div>

@if($tab === 'inscritos')
    @if($careerBreakdown->isNotEmpty())
        <div class="mb-4 bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex flex-wrap items-center gap-2">
            <span class="text-xs font-bold uppercase text-slate-500 mr-2">Estudiantes por Carrera:</span>
            @foreach($careerBreakdown as $label => $cnt)
                <span class="px-3 py-1 rounded-full bg-sky-50 border border-sky-200 text-[#0F2744] text-xs font-bold">
                    {{ $label }}: {{ $cnt }}
                </span>
            @endforeach
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3">Estudiante</th>
                    <th class="px-3 py-3">Carrera / Facultad</th>
                    <th class="px-3 py-3 text-center">Intento</th>
                    <th class="px-3 py-3 text-center">Nota Acumulada / Final</th>
                    <th class="px-3 py-3 text-center">Estado</th>
                    @if($offering->isManuallyClosed())
                        <th class="px-4 py-3 text-right">Corrección Auditada</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($offering->activeEnrollments as $enr)
                    @php
                        $enrCareer = $enr->career ?? $enr->student->career;
                    @endphp
                    <tr class="hover:bg-slate-50" x-data="{ adjOpen: false }">
                        <td class="px-5 py-3.5">
                            <div class="font-semibold text-slate-800">{{ $enr->student->name }}</div>
                            <div class="text-xs text-slate-500 font-mono">{{ $enr->student->code }} · {{ $enr->student->email }}</div>
                            @if($enr->gradeAdjustments->isNotEmpty())
                                @php $lastAdj = $enr->gradeAdjustments->first(); @endphp
                                <div class="text-[11px] text-amber-700 mt-1">
                                    Última corrección: {{ $lastAdj->previous_score }} → {{ $lastAdj->new_score }} ({{ $lastAdj->created_at->format('d/m/Y H:i') }} · {{ $lastAdj->reason }})
                                </div>
                            @endif
                        </td>
                        <td class="px-3 py-3.5 text-xs">
                            <span class="px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-900 font-bold border border-indigo-200">{{ $enrCareer?->name ?? '—' }}</span>
                            <div class="text-[11px] text-slate-400 mt-0.5">{{ $enrCareer?->faculty?->name ?? '' }}</div>
                        </td>
                        <td class="px-3 py-3.5 text-center text-xs font-semibold">#{{ $enr->attempt_number }}</td>
                        <td class="px-3 py-3.5 text-center font-bold text-[#0F2744] text-base">{{ $enr->accumulatedScore() }}<span class="text-xs text-slate-400">/100</span></td>
                        <td class="px-3 py-3.5 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                {{ $enr->status === 'aprobada' ? 'bg-emerald-100 text-emerald-800' : ($enr->status === 'reprobada' ? 'bg-rose-100 text-rose-700' : 'bg-sky-100 text-sky-800') }}">
                                {{ $enr->statusLabel() }}
                            </span>
                        </td>
                        @if($offering->isManuallyClosed())
                            <td class="px-4 py-3.5 text-right">
                                <button type="button" @click="adjOpen = !adjOpen" class="px-2.5 py-1 rounded bg-amber-100 text-amber-800 text-xs font-semibold cursor-pointer">
                                    Corregir Nota
                                </button>
                                <div x-show="adjOpen" x-cloak class="mt-2 p-3 rounded-lg bg-amber-50 border border-amber-200 text-left">
                                    <form method="POST" action="{{ route('teacher.grades.adjust', $enr) }}" class="space-y-2">
                                        @csrf
                                        <div class="flex gap-2">
                                            <input type="number" name="new_score" min="0" max="100" value="{{ $enr->final_score }}" required class="w-20 px-2 py-1 rounded border border-slate-300 text-xs bg-white">
                                            <input type="text" name="reason" placeholder="Motivo obligatorio de corrección..." required class="flex-1 px-2 py-1 rounded border border-slate-300 text-xs bg-white">
                                        </div>
                                        <button type="submit" class="px-3 py-1 rounded bg-[#0F2744] text-white text-xs font-bold cursor-pointer">Guardar Corrección</button>
                                    </form>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-8 text-center text-slate-500">No hay estudiantes inscritos en esta oferta todavía.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@elseif($tab === 'tareas')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @if(! $offering->isManuallyClosed() && ! $offering->is_disabled)
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 h-fit">
                <h2 class="font-bold text-[#0F2744] mb-1">Nueva Tarea</h2>
                <p class="text-xs text-slate-500 mb-4">Puntos restantes disponibles: <strong>{{ max(0, 100 - $totalTaskPoints) }}</strong></p>
                <form method="POST" action="{{ route('teacher.tasks.store', $offering) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Título *</label>
                        <input name="title" type="text" required placeholder="Ej. Proyecto Práctico 1" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Puntaje Máx. *</label>
                            <input name="max_points" type="number" min="1" max="{{ max(1, 100 - $totalTaskPoints) }}" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Fecha Entrega *</label>
                            <input name="due_date" type="date" value="{{ $offering->end_date->format('Y-m-d') }}" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Descripción</label>
                        <textarea name="description" rows="2" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm"></textarea>
                    </div>
                    <button type="submit" {{ $totalTaskPoints >= 100 ? 'disabled' : '' }}
                            class="w-full py-2.5 rounded-lg text-sm font-semibold {{ $totalTaskPoints < 100 ? 'bg-[#0F2744] hover:bg-[#1E3A5F] text-white cursor-pointer' : 'bg-slate-200 text-slate-400 cursor-not-allowed' }}">
                        Añadir Tarea
                    </button>
                </form>
            </div>
        @endif

        <div class="{{ (! $offering->isManuallyClosed() && ! $offering->is_disabled) ? 'lg:col-span-2' : 'lg:col-span-3' }} bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                <h2 class="font-bold text-[#0F2744]">Plan de Evaluación de la Oferta</h2>
                <span class="text-xs font-bold {{ $totalTaskPoints === 100 ? 'text-emerald-700' : 'text-amber-700' }}">Total: {{ $totalTaskPoints }} / 100 pts</span>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($offering->tasks as $task)
                    <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <div class="font-bold text-sm text-slate-800">{{ $task->title }}</div>
                            @if($task->description)
                                <div class="text-xs text-slate-500 mt-0.5">{{ $task->description }}</div>
                            @endif
                            <div class="text-xs text-slate-500 mt-1">Entrega: <strong>{{ $task->due_date->format('d/m/Y') }}</strong></div>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <span class="px-3 py-1 rounded-full bg-sky-100 text-[#0F2744] font-bold text-xs">{{ $task->max_points }} pts</span>
                            @if(! $offering->isManuallyClosed() && ! $offering->is_disabled)
                                <form method="POST" action="{{ route('teacher.tasks.destroy', $task) }}" onsubmit="return confirm('¿Eliminar esta tarea y sus calificaciones?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 rounded bg-rose-100 text-rose-700 text-xs font-semibold cursor-pointer">Eliminar</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-slate-500">Crea tareas hasta completar 100 puntos para evaluar y cerrar la materia.</div>
                @endforelse
            </div>
        </div>
    </div>
@else
    <!-- Pestaña Calificar Tareas -->
    @if($offering->tasks->isEmpty())
        <div class="bg-white rounded-xl border border-slate-200 p-8 text-center text-sm text-slate-500">
            Primero crea tareas en la pestaña "Tareas" para poder calificar a los estudiantes.
        </div>
    @else
        <div class="space-y-5">
            @foreach($offering->tasks as $task)
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-[#0F2744]">{{ $task->title }}</span>
                            <span class="text-xs text-slate-500 ml-2">Entrega: {{ $task->due_date->format('d/m/Y') }}</span>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full bg-sky-100 text-[#0F2744] text-xs font-bold">Máx. {{ $task->max_points }} pts</span>
                    </div>
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase text-slate-400 border-b border-slate-100">
                            <tr>
                                <th class="px-5 py-2.5">Estudiante / Carrera</th>
                                <th class="px-3 py-2.5 text-center">Presentación</th>
                                <th class="px-3 py-2.5 text-center">Calificación</th>
                                <th class="px-3 py-2.5 text-center">Nota (0-{{ $task->max_points }})</th>
                                <th class="px-3 py-2.5">Comentario del Docente</th>
                                <th class="px-5 py-2.5 text-right">Anotar Nota</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($offering->activeEnrollments as $enr)
                                @php
                                    $tg = $gradesMap[$enr->id][$task->id] ?? null;
                                    $enrCar = $enr->career ?? $enr->student->career;
                                @endphp
                                <tr>
                                    <form method="POST" action="{{ route('teacher.tasks.grade', [$task, $enr]) }}">
                                        @csrf
                                        <td class="px-5 py-3">
                                            <div class="font-semibold text-slate-800">{{ $enr->student->name }}</div>
                                            <div class="text-[11px] text-slate-500">{{ $enrCar?->code ?? '—' }} · {{ $enrCar?->name ?? '—' }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            @if($tg && $tg->isSubmitted())
                                                <span class="px-2 py-0.5 rounded-full bg-sky-100 text-sky-800 text-[11px] font-bold">Presentada</span>
                                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $tg->submitted_at->format('d/m H:i') }}</div>
                                                @if($tg->submission_note)
                                                    <div class="text-[10px] text-slate-600 italic truncate max-w-[140px]" title="{{ $tg->submission_note }}">{{ $tg->submission_note }}</div>
                                                @endif
                                            @else
                                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[11px] font-semibold">No presentada</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            @if($tg === null || $tg->score === null)
                                                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[11px] font-semibold">Sin calificar</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-semibold">Calificada ({{ $tg->score }})</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <input type="number" name="score" min="0" max="{{ $task->max_points }}" step="1"
                                                   value="{{ $tg?->score }}" placeholder="—"
                                                   {{ $offering->isManuallyClosed() ? 'disabled' : '' }}
                                                   class="w-20 px-2 py-1.5 text-center rounded border border-slate-300 text-sm">
                                        </td>
                                        <td class="px-3 py-3">
                                            <input type="text" name="comment" value="{{ $tg?->comment }}" placeholder="Comentario opcional"
                                                   {{ $offering->isManuallyClosed() ? 'disabled' : '' }}
                                                   class="w-full px-2.5 py-1.5 rounded border border-slate-300 text-xs">
                                        </td>
                                        <td class="px-5 py-3 text-right">
                                            @if(! $offering->isManuallyClosed())
                                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-[#0F2744] hover:bg-[#1E3A5F] text-white text-xs font-semibold cursor-pointer">Guardar</button>
                                            @else
                                                <span class="text-xs text-slate-400">Congelada</span>
                                            @endif
                                        </td>
                                    </form>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    @endif
@endif
@endsection
