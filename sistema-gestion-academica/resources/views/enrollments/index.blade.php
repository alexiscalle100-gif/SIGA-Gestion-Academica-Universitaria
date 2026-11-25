@extends('layouts.app')

@section('title', 'Inscripción a Cursos')
@section('header', 'Inscripción a Cursos')
@section('subheader', 'Explora la oferta académica disponible. El sistema previene duplicidad y sobreposición de horarios automáticamente.')

@section('content')
<!-- Filtros -->
<div class="bg-white rounded-xl border border-slate-200 p-4 mb-6 shadow-sm">
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <select name="career_id" class="px-3.5 py-2 rounded-lg border border-slate-300 text-sm bg-white">
            <option value="">Todas las carreras</option>
            @foreach($careers as $c)
                <option value="{{ $c->id }}" @selected($careerId == $c->id)>{{ $c->code }} - {{ $c->name }}</option>
            @endforeach
        </select>
        <input type="text" name="search" value="{{ $search }}" placeholder="Buscar curso o código..."
               class="px-3.5 py-2 rounded-lg border border-slate-300 text-sm flex-1 min-w-[200px]">
        <button type="submit" class="px-4 py-2 rounded-lg bg-[#0F2744] hover:bg-[#1E3A5F] text-white text-sm font-semibold cursor-pointer">
            Buscar Oferta
        </button>
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Cursos disponibles -->
    <div class="lg:col-span-2 space-y-4">
        <h2 class="font-bold text-[#0F2744] text-base">Cursos Disponibles ({{ $availableCourses->count() }})</h2>
        @forelse($availableCourses as $course)
            @php
                $isEnrolled = in_array($course->id, $enrolledCourseIds, true);
                $conflictMsg = $conflictsByCourse[$course->id] ?? null;
                $isFull = $course->availableSlots() <= 0;
            @endphp
            <div class="bg-white rounded-xl border {{ $conflictMsg ? 'border-rose-200' : ($isEnrolled ? 'border-sky-200' : 'border-slate-200') }} p-5 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                    <div class="space-y-1.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded bg-sky-100 text-[#0F2744] font-mono text-xs font-bold">{{ $course->code }}</span>
                            <span class="text-xs font-medium text-slate-500">{{ $course->career->name }} · Sem. {{ $course->semester }}</span>
                            <span class="text-xs text-slate-400">{{ $course->credits }} créditos</span>
                        </div>
                        <h3 class="font-bold text-base text-[#0F2744]">{{ $course->name }}</h3>
                        <div class="text-xs text-slate-600">
                            <strong>Docente:</strong> {{ $course->teacher->name }} · <strong>Aula:</strong> {{ $course->classroom ?? 'Por definir' }}
                        </div>
                        <div class="text-xs text-sky-800 font-semibold">
                            Horario: {{ $course->scheduleSummary() }}
                        </div>
                        @if($conflictMsg)
                            <div class="mt-2 px-3 py-2 rounded-lg bg-rose-50 border border-rose-200 text-xs text-rose-700 font-medium">
                                ⚠ {{ $conflictMsg }}
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-col items-end gap-2 shrink-0">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $isFull ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700' }}">
                            Cupos: {{ $course->availableSlots() }} / {{ $course->max_capacity }}
                        </span>

                        @if($isEnrolled)
                            <span class="px-4 py-2 rounded-lg bg-sky-100 text-sky-800 text-xs font-bold">✓ Ya Inscrito</span>
                        @elseif($conflictMsg)
                            <button type="button" disabled
                                    class="px-4 py-2 rounded-lg bg-rose-100 text-rose-600 text-xs font-bold cursor-not-allowed">
                                Choque de Horario
                            </button>
                        @elseif($isFull)
                            <button type="button" disabled
                                    class="px-4 py-2 rounded-lg bg-slate-200 text-slate-500 text-xs font-bold cursor-not-allowed">
                                Sin Cupos
                            </button>
                        @else
                            <form method="POST" action="{{ route('student.enrollments.store') }}">
                                @csrf
                                <input type="hidden" name="course_id" value="{{ $course->id }}">
                                <button type="submit"
                                        class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition cursor-pointer">
                                    Inscribirme
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-slate-200 p-8 text-center text-sm text-slate-500">
                No existen cursos activos que coincidan con el filtro seleccionado.
            </div>
        @endforelse
    </div>

    <!-- Mis inscripciones vigentes -->
    <div class="space-y-4">
        <h2 class="font-bold text-[#0F2744] text-base">Mis Materias Inscritas ({{ $myEnrollments->count() }})</h2>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm divide-y divide-slate-100">
            @forelse($myEnrollments as $enr)
                <div class="p-4">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="font-mono text-xs font-bold text-[#1E3A5F]">{{ $enr->course->code }}</span>
                            <div class="font-semibold text-sm text-slate-800">{{ $enr->course->name }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">{{ $enr->course->scheduleSummary() }}</div>
                        </div>
                        @if(!$enr->grade || $enr->grade->final_score === null)
                            <form method="POST" action="{{ route('student.enrollments.destroy', $enr) }}"
                                  onsubmit="return confirm('¿Seguro deseas retirar la materia {{ $enr->course->code }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-rose-600 hover:underline cursor-pointer">Retirar</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-xs text-slate-500">Todavía no te has inscrito a ninguna materia.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
