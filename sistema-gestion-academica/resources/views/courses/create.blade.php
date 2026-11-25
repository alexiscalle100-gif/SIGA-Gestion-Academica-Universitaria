@extends('layouts.app')

@section('title', 'Crear Curso')
@section('header', 'Crear Nuevo Curso')
@section('subheader', 'Define la materia, docente y bloques horarios con validación automática contra sobreposición')

@section('content')
@php
    $initialSchedules = old('schedules', [
        ['day_of_week' => 1, 'start_time' => '08:00', 'end_time' => '10:00', 'classroom' => ''],
    ]);
@endphp
<div class="max-w-4xl bg-white rounded-xl border border-slate-200 shadow-sm p-6"
     x-data="{ slots: @js($initialSchedules) }">
    <form method="POST" action="{{ route('admin.courses.store') }}" class="space-y-5">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Código de Materia *</label>
                <input name="code" type="text" value="{{ old('code') }}" required placeholder="Ej. SIS-405"
                       class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm uppercase">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre del Curso *</label>
                <input name="name" type="text" value="{{ old('name') }}" required placeholder="Ej. Arquitectura de Software"
                       class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Carrera *</label>
                <select name="career_id" required class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                    <option value="">— Seleccionar carrera —</option>
                    @foreach($careers as $career)
                        <option value="{{ $career->id }}" @selected(old('career_id') == $career->id)>{{ $career->code }} - {{ $career->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Docente Asignado *</label>
                <select name="teacher_id" required class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                    <option value="">— Seleccionar docente —</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" @selected(old('teacher_id') == $teacher->id)>{{ $teacher->name }} ({{ $teacher->code }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Gestión *</label>
                <input name="term" type="text" value="{{ old('term', '2026-II') }}" required
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Semestre *</label>
                <input name="semester" type="number" min="1" max="12" value="{{ old('semester', 4) }}" required
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Créditos *</label>
                <input name="credits" type="number" min="1" max="12" value="{{ old('credits', 4) }}" required
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Cupo Máx. *</label>
                <input name="max_capacity" type="number" min="1" max="200" value="{{ old('max_capacity', 30) }}" required
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Aula Principal</label>
                <input name="classroom" type="text" value="{{ old('classroom') }}" placeholder="LAB-201"
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Descripción</label>
            <textarea name="description" rows="2" class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm">{{ old('description') }}</textarea>
        </div>

        <!-- Bloques horarios dinámicos -->
        <div class="pt-4 border-t border-slate-200">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h3 class="text-sm font-bold text-[#0F2744]">Horarios de Clase *</h3>
                    <p class="text-xs text-slate-500">El sistema valida automáticamente que el docente no tenga choques de horario.</p>
                </div>
                <button type="button" @click="slots.push({day_of_week: 2, start_time: '08:00', end_time: '10:00', classroom: ''})"
                        class="px-3 py-1.5 rounded-lg bg-sky-100 text-[#0F2744] text-xs font-bold hover:bg-sky-200 cursor-pointer">
                    + Añadir Bloque
                </button>
            </div>

            <div class="space-y-2.5">
                <template x-for="(slot, idx) in slots" :key="idx">
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 items-end p-3 rounded-lg bg-slate-50 border border-slate-200">
                        <div class="sm:col-span-3">
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Día</label>
                            <select :name="`schedules[${idx}][day_of_week]`" x-model="slot.day_of_week" class="w-full px-2.5 py-1.5 rounded border border-slate-300 text-sm bg-white">
                                @foreach($days as $num => $label)
                                    <option value="{{ $num }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-3">
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Hora Inicio</label>
                            <input type="time" :name="`schedules[${idx}][start_time]`" x-model="slot.start_time" required class="w-full px-2.5 py-1.5 rounded border border-slate-300 text-sm bg-white">
                        </div>
                        <div class="sm:col-span-3">
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Hora Fin</label>
                            <input type="time" :name="`schedules[${idx}][end_time]`" x-model="slot.end_time" required class="w-full px-2.5 py-1.5 rounded border border-slate-300 text-sm bg-white">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Aula</label>
                            <input type="text" :name="`schedules[${idx}][classroom]`" x-model="slot.classroom" placeholder="Opcional" class="w-full px-2.5 py-1.5 rounded border border-slate-300 text-sm bg-white">
                        </div>
                        <div class="sm:col-span-1 text-right">
                            <button type="button" x-show="slots.length > 1" @click="slots.splice(idx, 1)"
                                    class="px-2.5 py-1.5 rounded bg-rose-100 text-rose-700 text-xs font-bold hover:bg-rose-200 cursor-pointer">✕</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
            <a href="{{ route('courses.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50">Cancelar</a>
            <button type="submit" class="px-5 py-2.5 rounded-lg bg-[#0F2744] hover:bg-[#1E3A5F] text-white text-sm font-semibold cursor-pointer">Guardar Curso</button>
        </div>
    </form>
</div>
@endsection
