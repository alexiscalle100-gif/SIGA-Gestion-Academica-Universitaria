@extends('layouts.app')

@section('title', $course->code . ' - ' . $course->name)
@section('header', $course->code . ' — ' . $course->name)
@section('subheader', $course->career->name . ' · Gestión ' . $course->term)

@section('content')
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <a href="{{ route('courses.index') }}" class="text-sm font-semibold text-slate-600 hover:text-[#0F2744]">← Volver al listado de cursos</a>
    <div class="flex items-center gap-2">
        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.courses.edit', $course) }}" class="px-3.5 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold">Editar Curso</a>
        @endif
        @if($canManageGrades)
            <a href="{{ route('grades.index', ['course_id' => $course->id]) }}" class="px-3.5 py-2 rounded-lg bg-[#0F2744] hover:bg-[#1E3A5F] text-white text-xs font-semibold">Gestionar Calificaciones</a>
            <a href="{{ route('reports.download', ['type' => 'course-enrolled-students', 'format' => 'pdf', 'course_id' => $course->id]) }}"
               class="px-3.5 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold">Nómina PDF</a>
            <a href="{{ route('reports.download', ['type' => 'course-enrolled-students', 'format' => 'excel', 'course_id' => $course->id]) }}"
               class="px-3.5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold">Nómina Excel</a>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Ficha académica -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4 h-fit">
        <h2 class="font-bold text-[#0F2744] border-b border-slate-100 pb-2.5">Información del Curso</h2>
        <p class="text-sm text-slate-600 leading-relaxed">{{ $course->description ?: 'Materia oficial de la malla curricular institucional.' }}</p>

        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-slate-500">Código:</dt><dd class="font-mono font-bold text-[#0F2744]">{{ $course->code }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Carrera:</dt><dd class="font-semibold">{{ $course->career->name }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Docente:</dt><dd class="font-semibold">{{ $course->teacher->name }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Semestre / Créditos:</dt><dd class="font-semibold">Sem. {{ $course->semester }} · {{ $course->credits }} cr.</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Aula:</dt><dd class="font-semibold">{{ $course->classroom ?? 'Por definir' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Cupo:</dt><dd class="font-semibold">{{ $course->active_enrollments_count }} / {{ $course->max_capacity }}</dd></div>
        </dl>

        <div class="pt-3 border-t border-slate-100">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Horarios Oficiales</h3>
            <div class="space-y-2">
                @foreach($course->schedules as $slot)
                    <div class="px-3 py-2 rounded-lg bg-sky-50 border border-sky-100 flex items-center justify-between text-xs">
                        <span class="font-bold text-[#0F2744]">{{ $slot->dayLabel() }}</span>
                        <span class="font-mono text-sky-800">{{ $slot->formattedRange() }}</span>
                        <span class="text-slate-500">{{ $slot->classroom ?: $course->classroom }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Contenido según rol -->
    <div class="lg:col-span-2">
        @if($canManageGrades)
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                    <h2 class="font-bold text-[#0F2744]">Estudiantes Inscritos ({{ $course->activeEnrollments->count() }})</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3">Estudiante</th>
                                <th class="px-4 py-3 text-center">P1 (30%)</th>
                                <th class="px-4 py-3 text-center">P2 (30%)</th>
                                <th class="px-4 py-3 text-center">Final (40%)</th>
                                <th class="px-4 py-3 text-center">Nota Final</th>
                                <th class="px-4 py-3 text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($course->activeEnrollments as $enr)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-slate-800">{{ $enr->student->name }}</div>
                                        <div class="text-xs text-slate-500">{{ $enr->student->code }} · {{ $enr->student->email }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-center">{{ $enr->grade?->partial_1 ?? '—' }}</td>
                                    <td class="px-4 py-3 text-center">{{ $enr->grade?->partial_2 ?? '—' }}</td>
                                    <td class="px-4 py-3 text-center">{{ $enr->grade?->final_exam ?? '—' }}</td>
                                    <td class="px-4 py-3 text-center font-bold text-[#0F2744]">{{ $enr->grade?->final_score ?? '—' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                            {{ ($enr->grade?->status === 'aprobado') ? 'bg-emerald-100 text-emerald-800' : (($enr->grade?->status === 'reprobado') ? 'bg-rose-100 text-rose-800' : 'bg-sky-100 text-sky-800') }}">
                                            {{ $enr->grade?->statusLabel() ?? 'En curso' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="p-6 text-center text-slate-500">No hay estudiantes inscritos todavía.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h2 class="font-bold text-[#0F2744] mb-4">Mi Estado y Calificaciones en esta Materia</h2>
                @if($myEnrollment)
                    @php $g = $myEnrollment->grade; @endphp
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-center">
                            <div class="text-xs text-slate-500">Parcial 1 (30%)</div>
                            <div class="mt-1 text-2xl font-bold text-slate-800">{{ $g?->partial_1 ?? '—' }}</div>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-center">
                            <div class="text-xs text-slate-500">Parcial 2 (30%)</div>
                            <div class="mt-1 text-2xl font-bold text-slate-800">{{ $g?->partial_2 ?? '—' }}</div>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-center">
                            <div class="text-xs text-slate-500">Eval. Final (40%)</div>
                            <div class="mt-1 text-2xl font-bold text-slate-800">{{ $g?->final_exam ?? '—' }}</div>
                        </div>
                        <div class="p-4 rounded-xl bg-sky-50 border border-sky-200 text-center">
                            <div class="text-xs text-sky-700 font-semibold">Nota Final</div>
                            <div class="mt-1 text-2xl font-bold text-[#0F2744]">{{ $g?->final_score ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 text-sm flex items-center justify-between">
                        <span><strong>Observaciones del docente:</strong> {{ $g?->observations ?: 'Sin observaciones registradas.' }}</span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold
                            {{ ($g?->status === 'aprobado') ? 'bg-emerald-100 text-emerald-800' : (($g?->status === 'reprobado') ? 'bg-rose-100 text-rose-800' : 'bg-sky-100 text-sky-800') }}">
                            {{ $g?->statusLabel() ?? 'En curso' }}
                        </span>
                    </div>
                @else
                    <p class="text-sm text-slate-600">No te encuentras inscrito actualmente en este curso.</p>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
