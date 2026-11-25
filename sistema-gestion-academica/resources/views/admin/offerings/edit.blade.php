@extends('layouts.app')

@section('title', 'Editar Oferta')
@section('header', 'Editar Oferta: ' . $offering->subject->code)

@section('content')
<div class="max-w-2xl bg-white rounded-xl border border-slate-200 shadow-sm p-6">
    <!-- Campos bloqueados tras creación -->
    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 mb-5 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
        <div><span class="text-slate-400 block">Materia</span><strong class="text-[#0F2744]">{{ $offering->subject->code }}</strong></div>
        <div><span class="text-slate-400 block">Turno y Horario (bloqueado)</span><strong>{{ $offering->scheduleText() }}</strong></div>
        <div><span class="text-slate-400 block">Inicio (bloqueado)</span><strong>{{ $offering->start_date->format('d/m/Y') }}</strong></div>
        <div><span class="text-slate-400 block">Fin (bloqueado)</span><strong>{{ $offering->end_date->format('d/m/Y') }}</strong></div>
    </div>

    <form method="POST" action="{{ route('admin.offerings.update', $offering) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Número de Aula *</label>
                <input type="number" name="classroom" min="1" max="9999" value="{{ old('classroom', $offering->classroom) }}" required
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Cupo Máximo (mín. {{ $offering->active_enrollments_count }}, máx. 50) *</label>
                <input type="number" name="max_capacity" min="{{ max(1, $offering->active_enrollments_count) }}" max="50" value="{{ old('max_capacity', $offering->max_capacity) }}" required
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-2">Docente asignado (Facultad: {{ $offering->subject->faculty->name }}) *</label>
            <div class="space-y-2">
                @foreach($teachersAvailability as $entry)
                    @php $t = $entry['teacher']; @endphp
                    <label class="flex items-center justify-between gap-3 p-3 rounded-xl border {{ $entry['available'] ? 'border-slate-200 bg-white cursor-pointer' : 'border-rose-200 bg-rose-50 opacity-75 cursor-not-allowed' }}">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="teacher_id" value="{{ $t->id }}"
                                   @checked(old('teacher_id', $offering->teacher_id) == $t->id)
                                   {{ $entry['available'] ? '' : 'disabled' }} required>
                            <span class="text-sm font-semibold text-slate-800">{{ $t->name }} ({{ $t->code }})</span>
                        </div>
                        <span class="text-xs font-semibold {{ $entry['available'] ? 'text-emerald-700' : 'text-rose-700' }}">
                            {{ $entry['available'] ? 'Disponible' : $entry['conflict_label'] }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-2.5 pt-3 border-t border-slate-200">
            <a href="{{ route('admin.offerings.index', ['tab' => 'ofertas']) }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm font-semibold text-slate-700">Cancelar</a>
            <button type="submit" class="px-5 py-2 rounded-lg bg-[#0F2744] hover:bg-[#1E3A5F] text-white text-sm font-semibold cursor-pointer">Guardar Cambios</button>
        </div>
    </form>
</div>
@endsection
