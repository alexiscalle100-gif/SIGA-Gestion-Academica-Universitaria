@extends('layouts.app')

@section('title', 'Inscripción y Cursos')
@section('header', 'Inscripción a Materias (' . $portal['semester']->code . ')')

@section('content')
    @if($portal['careers']->count() > 1)
        <!-- Selector de carrera para estudiantes con Doble Carrera (incluso en distintas facultades) -->
        <div class="mb-6 bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Doble Carrera Activa — Selecciona
                carrera para inscribirte (los choques de horario se validan entre ambas)</div>
            <div class="flex flex-wrap gap-2.5">
                @foreach($portal['careers'] as $car)
                    <a href="{{ route('student.enrollments.index', ['career_id' => $car->id]) }}"
                        class="px-4 py-2 rounded-lg text-xs font-bold border transition
                               {{ $portal['selected_career']?->id === $car->id ? 'bg-[#0F2744] text-white border-[#0F2744]' : 'bg-slate-50 text-slate-700 border-slate-300 hover:border-[#1E3A5F]' }}">
                        {{ $car->name }} · {{ $car->faculty->name }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            @if($portal['state'] === 'no_tuition')
                <div class="bg-amber-50 border border-amber-300 rounded-xl p-6 text-amber-900 shadow-sm">
                    <div class="font-bold text-base mb-1">Matrícula Semestral Inactiva — {{ $portal['selected_career']?->name }}
                    </div>
                    <p class="text-sm">{{ $portal['message'] }}</p>
                </div>
            @elseif($portal['state'] === 'quota_reached')
                <!-- Cupo de 6 materias alcanzado: sin mensaje, sección oculta -->
            @elseif($portal['state'] === 'no_offerings')
                <div class="bg-white border border-slate-200 rounded-xl p-8 text-center text-slate-600 shadow-sm">
                    <p class="text-sm font-semibold">{{ $portal['message'] }}</p>
                </div>
            @else
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <h2 class="font-bold text-[#0F2744] text-base">
                        Ofertas Disponibles — {{ $portal['selected_career']->name }}
                    </h2>
                    <span class="px-3 py-1 rounded-full bg-sky-100 text-[#0F2744] text-xs font-bold">
                        Cupo semestral: {{ $portal['used_slots'] }} / {{ $portal['max_slots'] }}
                    </span>
                </div>

                <div class="space-y-3.5" x-data="{ confirmOffering: null }">
                    @foreach($portal['offerings'] as $row)
                        @php
                            $off = $row['offering'];
                            $style = $off->subject->areaStyle();
                        @endphp
                        <div
                            class="bg-white rounded-xl border {{ $row['has_shift_conflict'] ? 'border-rose-200' : 'border-slate-200' }} p-5 shadow-sm">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span
                                            class="px-2 py-0.5 rounded font-mono text-xs font-bold border {{ $style['bg'] }} {{ $style['text'] }} {{ $style['border'] }}">{{ $off->subject->code }}</span>
                                        <span class="text-xs font-semibold text-slate-500">Nivel
                                            {{ $row['curriculum_item']->level }} · {{ $off->subject->credits }} cr.</span>
                                        <span class="text-xs text-slate-400">Inscripción hasta
                                            {{ $off->enrollmentDeadline()->format('d/m/Y') }}</span>
                                    </div>
                                    <h3 class="font-bold text-base text-[#0F2744] mt-1">{{ $off->subject->name }}</h3>
                                    <div class="text-xs text-slate-700 mt-1">
                                        <strong>{{ $off->monthLabel() }}</strong> · <strong
                                            class="text-sky-800">{{ $off->scheduleText() }}</strong> · {{ $off->classroomLabel() }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        Docente: {{ $off->teacherLabel() }} · Módulo: {{ $off->start_date->format('d/m/Y') }} al
                                        {{ $off->end_date->format('d/m/Y') }}
                                    </div>
                                    @if($row['has_shift_conflict'])
                                        <div
                                            class="mt-2 px-3 py-2 rounded-lg bg-rose-50 border border-rose-200 text-xs text-rose-700 font-semibold">
                                            ⚠ {{ $row['conflict_message'] }}
                                        </div>
                                    @endif
                                </div>

                                <div class="flex flex-col items-end gap-2 shrink-0">
                                    <span
                                        class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $row['is_full'] ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700' }}">
                                        Lugares: {{ $off->availableSlots() }} / {{ $off->max_capacity }}
                                    </span>

                                    @if($row['is_full'])
                                        <button type="button" disabled
                                            class="px-4 py-2 rounded-lg bg-slate-200 text-slate-500 text-xs font-bold cursor-not-allowed">Sin
                                            cupos</button>
                                    @elseif($row['has_shift_conflict'])
                                        <button type="button" disabled
                                            class="px-4 py-2 rounded-lg bg-rose-100 text-rose-600 text-xs font-bold cursor-not-allowed">Choque
                                            Detectado</button>
                                    @else
                                        <button type="button"
                                            @click="confirmOffering = { id: {{ $off->id }}, code: '{{ $off->subject->code }}', name: '{{ addslashes($off->subject->name) }}', schedule: '{{ addslashes($off->monthLabel() . ' · ' . $off->scheduleText()) }}', teacher: '{{ addslashes($off->teacherLabel()) }}', dates: '{{ $off->start_date->format('d/m/Y') }} al {{ $off->end_date->format('d/m/Y') }}' }"
                                            class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold cursor-pointer">
                                            Inscribirme
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div x-show="confirmOffering" x-cloak
                        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4">
                        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                            <h3 class="text-lg font-bold text-[#0F2744]">Confirmar Inscripción</h3>
                            <p
                                class="text-xs text-amber-800 bg-amber-50 border border-amber-200 rounded-lg p-3 mt-2 font-medium">
                                Importante: una vez confirmada la inscripción no existe baja voluntaria; el módulo concluirá con
                                su calificación final.
                            </p>
                            <dl class="mt-4 space-y-1.5 text-sm bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <div class="flex justify-between">
                                    <dt class="text-slate-500">Carrera:</dt>
                                    <dd class="font-bold text-indigo-800">{{ $portal['selected_career']->name }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-slate-500">Materia:</dt>
                                    <dd class="font-bold text-[#0F2744]"
                                        x-text="confirmOffering?.code + ' - ' + confirmOffering?.name"></dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-slate-500">Horario:</dt>
                                    <dd class="font-semibold text-right" x-text="confirmOffering?.schedule"></dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-slate-500">Fechas:</dt>
                                    <dd class="font-semibold" x-text="confirmOffering?.dates"></dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-slate-500">Docente:</dt>
                                    <dd class="font-semibold" x-text="confirmOffering?.teacher"></dd>
                                </div>
                            </dl>
                            <div class="mt-5 flex justify-end gap-2.5">
                                <button type="button" @click="confirmOffering = null"
                                    class="px-4 py-2 rounded-lg border border-slate-300 text-sm font-semibold text-slate-700 cursor-pointer">Cancelar</button>
                                <form method="POST" action="{{ route('student.enrollments.store') }}">
                                    @csrf
                                    <input type="hidden" name="offering_id" :value="confirmOffering?.id">
                                    <input type="hidden" name="career_id" value="{{ $portal['selected_career']->id }}">
                                    <button type="submit"
                                        class="px-5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold cursor-pointer">Confirmar
                                        Inscripción</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-4">
            <h2 class="font-bold text-[#0F2744] text-base">Mis Inscripciones ({{ $portal['semester']->code }})</h2>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm divide-y divide-slate-100">
                @forelse($currentSemesterEnrollments as $enr)
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span
                                        class="font-mono text-xs font-bold text-[#1E3A5F]">{{ $enr->offering->subject->code }}</span>
                                    <span
                                        class="text-[10px] px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-800 font-bold">{{ $enr->career?->code ?? $student->career?->code }}</span>
                                    <span class="text-[11px] text-slate-400">Intento #{{ $enr->attempt_number }}</span>
                                </div>
                                <div class="font-semibold text-sm text-slate-800 mt-0.5">{{ $enr->offering->subject->name }}
                                </div>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $enr->offering->monthLabel() }} ·
                                    {{ $enr->offering->shiftLabel() }} ({{ $enr->offering->timeRange() }}) · {{ $enr->offering->classroomLabel() }}</div>
                            </div>
                            <span
                                class="px-2 py-0.5 rounded-full text-[11px] font-semibold shrink-0
                                    {{ $enr->status === 'aprobada' ? 'bg-emerald-100 text-emerald-800' : ($enr->status === 'reprobada' ? 'bg-rose-100 text-rose-700' : ($enr->status === 'anulada' ? 'bg-slate-200 text-slate-600' : 'bg-sky-100 text-sky-800')) }}">
                                {{ $enr->statusLabel() }}
                            </span>
                        </div>
                        @if($enr->status !== 'anulada')
                            <div class="mt-2.5 flex items-center justify-between text-xs">
                                <span class="text-slate-500">Nota: <strong
                                        class="text-[#0F2744]">{{ $enr->accumulatedScore() }}/100</strong></span>
                                <a href="{{ route('student.enrollments.show', $enr) }}"
                                    class="font-bold text-sky-700 hover:underline">Ver actividades →</a>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-500">Sin inscripciones registradas en este semestre.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection