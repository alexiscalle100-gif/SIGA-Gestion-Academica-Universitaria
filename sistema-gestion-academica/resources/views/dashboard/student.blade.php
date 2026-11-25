@extends('layouts.app')

@section('title', 'Dashboard Estudiante')
@section('header', 'Panel del Estudiante')

@section('content')
    @if($studentCareers->count() > 1)
        <div
            class="mb-5 bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex flex-wrap items-center justify-between gap-3">
            <div class="text-xs font-semibold text-slate-600">
                Doble Carrera activa ({{ $studentCareers->count() }} carreras en distintas facultades):
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach($studentCareers as $sc)
                    <a href="{{ route('dashboard', ['career_id' => $sc->id]) }}"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold border {{ $selectedCareer?->id === $sc->id ? 'bg-[#0F2744] text-white border-[#0F2744]' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                        {{ $sc->code }} · {{ $sc->name }} ({{ $sc->faculty->code }})
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    @if($portal['state'] === 'no_tuition')
        <div
            class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-300 text-amber-900 text-sm font-medium flex items-center gap-3">
            <span
                class="w-8 h-8 rounded-full bg-amber-200 text-amber-900 flex items-center justify-center font-bold shrink-0">!</span>
            <span>{{ $portal['message'] }}</span>
        </div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-7">
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase text-slate-500">Matrícula {{ $currentSemester->code }}</div>
            <div
                class="mt-2 text-2xl font-bold {{ ($portal['tuition']?->is_active) ? 'text-emerald-700' : 'text-amber-600' }}">
                {{ ($portal['tuition']?->is_active) ? 'Activa' : 'Inactiva' }}
            </div>
            <div class="mt-1 text-xs text-slate-500">{{ $selectedCareer->name ?? ($user->career->name ?? 'Sin carrera') }}
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase text-slate-500">Cupo Semestral</div>
            <div class="mt-2 text-3xl font-bold text-[#0F2744]">{{ $portal['used_slots'] }}<span
                    class="text-lg text-slate-400"> / {{ $portal['max_slots'] }}</span></div>
            <div class="mt-1 text-xs text-slate-500">Materias inscritas en {{ $currentSemester->code }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase text-slate-500">Avance Pensum</div>
            <div class="mt-2 text-3xl font-bold text-sky-700">{{ $careerProgress['summary']['aprobada'] ?? 0 }}<span
                    class="text-lg text-slate-400"> / {{ $careerProgress['summary']['total'] ?? 48 }}</span></div>
            <div class="mt-1 text-xs text-slate-500">{{ $careerProgress['summary']['percent'] ?? 0 }}% completado</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase text-slate-500">En Curso</div>
            <div class="mt-2 text-3xl font-bold text-indigo-700">{{ $inProgress->count() }}</div>
            <div class="mt-1 text-xs text-slate-500">Módulos activos</div>
        </div>
    </div>

    <div class="flex flex-wrap gap-3 mb-7">
        @if($portal['state'] !== 'quota_reached')
            <a href="{{ route('student.enrollments.index') }}"
                class="px-4 py-2.5 rounded-lg bg-[#0F2744] hover:bg-[#1E3A5F] text-white text-sm font-semibold">Inscripción de
                Materias</a>
        @endif
        <a href="{{ route('student.progress') }}"
            class="px-4 py-2.5 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-semibold">Mi
            Pensum (48 Materias) e Historial</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200">
                <h2 class="font-bold text-[#0F2744]">Mis Materias en Curso</h2>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($inProgress as $enr)
                    @php $style = $enr->offering->subject->areaStyle(); @endphp
                    <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50">
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span
                                    class="px-2 py-0.5 rounded font-mono text-xs font-bold {{ $style['bg'] }} {{ $style['text'] }}">{{ $enr->offering->subject->code }}</span>
                                @if($enr->career)
                                    <span
                                        class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[11px] font-semibold">{{ $enr->career->code }}
                                        · {{ $enr->career->faculty->code ?? '' }}</span>
                                @endif
                                <span class="text-xs text-slate-500">{{ $enr->offering->monthLabel() }} ·
                                    {{ $enr->offering->scheduleText() }} · Aula {{ $enr->offering->classroom }}</span>
                            </div>
                            <div class="font-bold text-slate-800 mt-1">{{ $enr->offering->subject->name }}</div>
                            <div class="text-xs text-slate-500">Docente: {{ $enr->offering->teacher->name }} · Intento
                                #{{ $enr->attempt_number }}</div>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <div class="text-right">
                                <div class="text-xs text-slate-400">Acumulado</div>
                                <div class="text-lg font-bold text-[#0F2744]">{{ $enr->accumulatedScore() }}<span
                                        class="text-xs text-slate-400">/100</span></div>
                            </div>
                            <a href="{{ route('student.enrollments.show', $enr) }}"
                                class="px-3.5 py-2 rounded-lg bg-[#1E3A5F] hover:bg-[#0F2744] text-white text-xs font-semibold">
                                Ver Tareas
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-slate-500">No tienes materias en curso actualmente.</div>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                <h2 class="font-bold text-[#0F2744]">Notificaciones Recientes</h2>
                <a href="{{ route('notifications.index') }}" class="text-xs font-semibold text-sky-700 hover:underline">Ver
                    todas</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recentNotifications as $notif)
                    <div class="px-5 py-3.5 {{ $notif->isUnread() ? 'bg-sky-50/50' : '' }}">
                        <div class="font-semibold text-xs text-[#0F2744]">{{ $notif->title }}</div>
                        <div class="text-xs text-slate-600 mt-0.5">{{ $notif->message }}</div>
                        <div class="text-[11px] text-slate-400 mt-1">{{ $notif->created_at->diffForHumans() }}</div>
                    </div>
                @empty
                    <div class="p-6 text-center text-sm text-slate-500">Sin notificaciones recientes.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection