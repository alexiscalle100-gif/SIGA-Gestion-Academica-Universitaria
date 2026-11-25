@extends('layouts.app')

@section('title', 'Dashboard Administrador')
@section('header', 'Panel de Control Institucional')

@section('content')
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <span class="px-3 py-1 rounded-full bg-sky-100 text-[#0F2744] text-xs font-bold">
        Semestre activo: {{ $currentSemester->code }} ({{ $currentSemester->periodLabel() }})
    </span>
    <div class="flex flex-wrap gap-2.5">
        <a href="{{ route('admin.offerings.index', ['tab' => 'habilitacion']) }}" class="px-4 py-2 rounded-lg bg-[#0F2744] hover:bg-[#1E3A5F] text-white text-xs font-semibold">+ Habilitar Materia</a>
        <a href="{{ route('admin.tuitions.index') }}" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold">Gestionar Matrículas</a>
        <a href="{{ route('admin.careers.index') }}" class="px-4 py-2 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold">Carreras y Pensums</a>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-7">
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
        <div class="text-xs font-semibold uppercase text-slate-500">Facultades / Carreras</div>
        <div class="mt-2 text-3xl font-bold text-[#0F2744]">{{ $stats['faculties'] }} <span class="text-lg text-slate-400">/ {{ $stats['careers'] }}</span></div>
        <div class="mt-1 text-xs text-slate-500">{{ $stats['subjects'] }} materias en catálogo</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
        <div class="text-xs font-semibold uppercase text-slate-500">Ofertas Activas</div>
        <div class="mt-2 text-3xl font-bold text-sky-700">{{ $stats['active_offerings'] }}</div>
        <div class="mt-1 text-xs text-slate-500">Semestre {{ $currentSemester->code }}</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
        <div class="text-xs font-semibold uppercase text-slate-500">Matrículas Activas</div>
        <div class="mt-2 text-3xl font-bold text-emerald-700">{{ $stats['active_tuitions'] }}</div>
        <div class="mt-1 text-xs text-slate-500">de {{ $stats['students'] }} estudiantes registrados</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
        <div class="text-xs font-semibold uppercase text-slate-500">Inscripciones Semestre</div>
        <div class="mt-2 text-3xl font-bold text-indigo-700">{{ $stats['enrollments'] }}</div>
        <div class="mt-1 text-xs text-slate-500">{{ $stats['teachers'] }} docentes activos</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="font-bold text-[#0F2744]">Ofertas del Semestre {{ $currentSemester->code }}</h2>
            <a href="{{ route('admin.offerings.index', ['tab' => 'ofertas']) }}" class="text-xs font-semibold text-sky-700 hover:underline">Ver todas →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Materia</th>
                        <th class="px-4 py-3">Mes / Turno</th>
                        <th class="px-4 py-3">Docente / Aula</th>
                        <th class="px-4 py-3 text-center">Cupo</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentOfferings as $off)
                        @php $style = $off->subject->areaStyle(); @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded font-mono text-[11px] font-bold {{ $style['bg'] }} {{ $style['text'] }}">{{ $off->subject->code }}</span>
                                <div class="font-semibold text-slate-800 mt-0.5">{{ $off->subject->name }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600">
                                <div class="font-semibold">{{ $off->monthLabel() }}</div>
                                <div>{{ $off->shiftLabel() }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600">
                                <div>{{ $off->teacher->name }}</div>
                                <div class="text-slate-400">Aula {{ $off->classroom }}</div>
                            </td>
                            <td class="px-4 py-3 text-center text-xs font-semibold">{{ $off->active_enrollments_count }}/{{ $off->max_capacity }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-sky-100 text-sky-800">{{ $off->statusLabel() }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-6 text-center text-slate-500">Sin ofertas registradas en el semestre actual.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="font-bold text-[#0F2744]">Actividades Recientes</h2>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($recentActivities as $act)
                <div class="px-5 py-3.5">
                    <div class="text-xs text-slate-800">{{ $act->description }}</div>
                    <div class="mt-1 flex items-center justify-between text-[11px] text-slate-400">
                        <span>{{ $act->user->name ?? 'Sistema' }}</span>
                        <span>{{ $act->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-sm text-slate-500">Sin actividades registradas.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
