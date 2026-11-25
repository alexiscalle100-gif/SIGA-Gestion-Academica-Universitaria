@extends('layouts.app')

@section('title', 'Reportes')
@section('header', 'Centro de Reportes')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @if($user->isAdmin())
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between" x-data="{ careerId: '' }">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">Administrador</span>
                <h2 class="mt-2 font-bold text-base text-[#0F2744]">Pensum y Materias por Carrera</h2>
                <p class="text-xs text-slate-500 mt-1">Exporta el listado de las 48 materias por nivel, mes, créditos y prerrequisitos.</p>
                <div class="mt-4">
                    <select x-model="careerId" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                        <option value="">Todas las carreras</option>
                        @foreach($careers as $c)
                            <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-5 flex gap-2.5">
                <a :href="'{{ route('reports.download', ['type' => 'subjects-by-career', 'format' => 'pdf']) }}' + (careerId ? '?career_id=' + careerId : '')"
                   class="flex-1 text-center py-2.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold">Descargar PDF</a>
                <a :href="'{{ route('reports.download', ['type' => 'subjects-by-career', 'format' => 'excel']) }}' + (careerId ? '?career_id=' + careerId : '')"
                   class="flex-1 text-center py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold">Descargar Excel</a>
            </div>
        </div>
    @endif

    @if($user->isTeacher())
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between"
             x-data="{ offeringId: '{{ $teacherOfferings->first()?->id ?? '' }}' }">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Docente</span>
                <h2 class="mt-2 font-bold text-base text-[#0F2744]">Nómina de Inscritos por Oferta</h2>
                <p class="text-xs text-slate-500 mt-1">Listado de estudiantes inscritos con intento y carrera.</p>
                <div class="mt-4">
                    <select x-model="offeringId" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                        @forelse($teacherOfferings as $o)
                            <option value="{{ $o->id }}">{{ $o->semester->code }} · {{ $o->monthLabel() }} — {{ $o->subject->code }} ({{ $o->active_enrollments_count }} inscritos)</option>
                        @empty
                            <option value="">Sin ofertas</option>
                        @endforelse
                    </select>
                </div>
            </div>
            <div class="mt-5 flex gap-2.5">
                @if($teacherOfferings->isNotEmpty())
                    <a :href="'{{ route('reports.download', ['type' => 'offering-enrolled-students', 'format' => 'pdf']) }}?offering_id=' + offeringId"
                       class="flex-1 text-center py-2.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold">Descargar PDF</a>
                    <a :href="'{{ route('reports.download', ['type' => 'offering-enrolled-students', 'format' => 'excel']) }}?offering_id=' + offeringId"
                       class="flex-1 text-center py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold">Descargar Excel</a>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between"
             x-data="{ offeringId: '{{ $teacherOfferings->first()?->id ?? '' }}' }">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Docente</span>
                <h2 class="mt-2 font-bold text-base text-[#0F2744]">Acta de Calificaciones por Oferta</h2>
                <p class="text-xs text-slate-500 mt-1">Acumulados, notas finales enteras y estado de aprobación.</p>
                <div class="mt-4">
                    <select x-model="offeringId" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                        @forelse($teacherOfferings as $o)
                            <option value="{{ $o->id }}">{{ $o->semester->code }} · {{ $o->monthLabel() }} — {{ $o->subject->code }} ({{ $o->statusLabel() }})</option>
                        @empty
                            <option value="">Sin ofertas</option>
                        @endforelse
                    </select>
                </div>
            </div>
            <div class="mt-5 flex gap-2.5">
                @if($teacherOfferings->isNotEmpty())
                    <a :href="'{{ route('reports.download', ['type' => 'offering-grades', 'format' => 'pdf']) }}?offering_id=' + offeringId"
                       class="flex-1 text-center py-2.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold">Descargar PDF</a>
                    <a :href="'{{ route('reports.download', ['type' => 'offering-grades', 'format' => 'excel']) }}?offering_id=' + offeringId"
                       class="flex-1 text-center py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold">Descargar Excel</a>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Docente</span>
                <h2 class="mt-2 font-bold text-base text-[#0F2744]">Historial de Materias Dictadas</h2>
                <p class="text-xs text-slate-500 mt-1">Resumen de todas las ofertas impartidas por semestre y turno.</p>
            </div>
            <div class="mt-5 flex gap-2.5">
                <a href="{{ route('reports.download', ['type' => 'teacher-history', 'format' => 'pdf']) }}"
                   class="flex-1 text-center py-2.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold">Descargar PDF</a>
                <a href="{{ route('reports.download', ['type' => 'teacher-history', 'format' => 'excel']) }}"
                   class="flex-1 text-center py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold">Descargar Excel</a>
            </div>
        </div>
    @endif

    @if($user->isStudent())
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-sky-100 text-sky-800">Estudiante</span>
                <h2 class="mt-2 font-bold text-base text-[#0F2744]">Récord e Historial Académico</h2>
                <p class="text-xs text-slate-500 mt-1">Listado completo de intentos, materias cursadas, notas finales y promedio.</p>
            </div>
            <div class="mt-5 flex gap-2.5">
                <a href="{{ route('reports.download', ['type' => 'student-history', 'format' => 'pdf']) }}"
                   class="flex-1 text-center py-2.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold">Descargar PDF</a>
                <a href="{{ route('reports.download', ['type' => 'student-history', 'format' => 'excel']) }}"
                   class="flex-1 text-center py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold">Descargar Excel</a>
            </div>
        </div>
    @endif
</div>
@endsection
