@extends('layouts.app')

@section('title', 'Cursos')
@section('header', auth()->user()->isAdmin() ? 'Gestión de Cursos y Horarios' : 'Mis Cursos')
@section('subheader', auth()->user()->isAdmin() ? 'Administra materias, docentes asignados y bloques horarios sin sobreposición' : 'Listado de cursos vinculados a tu cuenta y acceso a detalles relevantes')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    @if(auth()->user()->isAdmin())
        <form method="GET" class="flex flex-wrap items-center gap-2.5">
            <select name="career_id" class="px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                <option value="">Todas las carreras</option>
                @foreach($careers as $c)
                    <option value="{{ $c->id }}" @selected($careerId == $c->id)>{{ $c->code }} - {{ $c->name }}</option>
                @endforeach
            </select>
            <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por código o materia..."
                   class="px-3.5 py-2 rounded-lg border border-slate-300 text-sm w-60 bg-white">
            <button type="submit" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-semibold hover:bg-slate-900 cursor-pointer">Filtrar</button>
        </form>
        <a href="{{ route('admin.courses.create') }}"
           class="px-4 py-2.5 rounded-lg bg-[#0F2744] hover:bg-[#1E3A5F] text-white text-sm font-semibold shadow-sm text-center">
            + Crear Curso
        </a>
    @elseif(auth()->user()->isStudent())
        <div class="text-sm text-slate-600">Mostrando <strong>{{ $courses->count() }}</strong> materias inscritas actualmente.</div>
        <a href="{{ route('student.enrollments.index') }}"
           class="px-4 py-2.5 rounded-lg bg-[#0F2744] hover:bg-[#1E3A5F] text-white text-sm font-semibold shadow-sm text-center">
            + Inscribirme a Más Cursos
        </a>
    @else
        <div class="text-sm text-slate-600">Mostrando <strong>{{ $courses->count() }}</strong> materias asignadas como docente.</div>
    @endif
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    @forelse($courses as $course)
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col justify-between overflow-hidden">
            <div class="p-5">
                <div class="flex items-center justify-between gap-2 mb-2.5">
                    <span class="px-2.5 py-0.5 rounded-md bg-sky-100 text-[#0F2744] font-mono text-xs font-bold">{{ $course->code }}</span>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $course->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        {{ $course->term }} · Sem {{ $course->semester }}
                    </span>
                </div>
                <h3 class="font-bold text-base text-[#0F2744] leading-snug">{{ $course->name }}</h3>
                <p class="text-xs text-slate-500 mt-1">{{ $course->career->name }}</p>

                <dl class="mt-4 space-y-1.5 text-xs text-slate-600">
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Docente:</dt>
                        <dd class="font-semibold text-slate-700">{{ $course->teacher->name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Aula / Créditos:</dt>
                        <dd class="font-semibold">{{ $course->classroom ?? 'N/D' }} · {{ $course->credits }} cr.</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Cupo:</dt>
                        <dd class="font-semibold">{{ $course->active_enrollments_count }} / {{ $course->max_capacity }} alumnos</dd>
                    </div>
                </dl>

                <div class="mt-3.5 p-2.5 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-700">
                    <span class="font-semibold text-[#1E3A5F]">Horario:</span> {{ $course->scheduleSummary() }}
                </div>
            </div>

            <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between gap-2">
                <a href="{{ route('courses.show', $course) }}" class="text-xs font-bold text-[#1E3A5F] hover:underline">
                    Ver Detalles →
                </a>
                @if(auth()->user()->isAdmin())
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.courses.edit', $course) }}"
                           class="px-2.5 py-1 rounded-md bg-amber-100 text-amber-800 hover:bg-amber-200 text-xs font-semibold">Editar</a>
                        <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                              onsubmit="return confirm('¿Deseas eliminar el curso {{ $course->code }}? Esta acción eliminará sus horarios e inscripciones.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-2.5 py-1 rounded-md bg-rose-100 text-rose-700 hover:bg-rose-200 text-xs font-semibold cursor-pointer">Eliminar</button>
                        </form>
                    </div>
                @elseif(auth()->user()->isTeacher())
                    <a href="{{ route('grades.index', ['course_id' => $course->id]) }}"
                       class="px-3 py-1 rounded-md bg-[#0F2744] text-white text-xs font-semibold hover:bg-[#1E3A5F]">Calificaciones</a>
                @endif
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white rounded-xl border border-slate-200 p-10 text-center text-slate-500">
            No se encontraron cursos registrados para mostrar.
        </div>
    @endforelse
</div>
@endsection
