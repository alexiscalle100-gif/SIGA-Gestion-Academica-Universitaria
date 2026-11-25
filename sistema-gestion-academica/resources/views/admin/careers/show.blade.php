@extends('layouts.app')

@section('title', 'Pensum — ' . $career->name)
@section('header', 'Pensum: ' . $career->name . ' (' . $career->code . ')')

@section('content')
@if(! $career->is_active)
    <div class="mb-5 p-4 rounded-xl bg-amber-50 border border-amber-300 flex flex-wrap items-center justify-between gap-3">
        <div class="text-sm text-amber-900">
            <strong>Carrera Inhabilitada (pendiente de aprobación):</strong> agrega o revisa las materias de su pensum y haz clic en <em>Aprobar y Habilitar</em> cuando esté lista para recibir estudiantes y ofertas.
        </div>
        <form method="POST" action="{{ route('admin.careers.toggle', $career) }}">
            @csrf @method('PATCH')
            <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold cursor-pointer">
                Aprobar y Habilitar Carrera
            </button>
        </form>
    </div>
@endif

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div class="flex flex-wrap items-center gap-3 text-xs">
        <a href="{{ route('admin.careers.index') }}" class="font-semibold text-slate-600 hover:text-[#0F2744]">← Volver a Carreras</a>
        <span class="text-slate-300">|</span>
        <span class="font-semibold text-slate-700">{{ $career->faculty->name }}</span>
        <span class="px-2.5 py-0.5 rounded-full bg-sky-100 text-[#0F2744] font-bold">{{ $allItems->count() }} Materias en Pensum</span>
        <span class="px-2.5 py-0.5 rounded-full font-bold {{ $career->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
            {{ $career->is_active ? 'Habilitada' : 'Inhabilitada' }}
        </span>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('reports.download', ['type' => 'subjects-by-career', 'format' => 'pdf', 'career_id' => $career->id]) }}" class="px-3 py-1.5 rounded-lg bg-rose-600 text-white text-xs font-semibold">Pensum PDF</a>
        <a href="{{ route('reports.download', ['type' => 'subjects-by-career', 'format' => 'excel', 'career_id' => $career->id]) }}" class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-semibold">Pensum Excel</a>
    </div>
</div>

<!-- Formulario para agregar materias al pensum de esta carrera -->
<div class="mb-6 bg-white rounded-xl border border-slate-200 shadow-sm p-5" x-data="{ mode: '{{ $availableSubjects->isNotEmpty() ? 'existing' : 'new' }}', isElective: false }">
    <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
        <h2 class="font-bold text-[#0F2744] text-sm">Agregar Materia al Pensum ({{ $career->faculty->name }})</h2>
        <div class="flex gap-2 text-xs">
            <button type="button" @click="mode = 'existing'" :class="mode === 'existing' ? 'bg-[#0F2744] text-white' : 'bg-slate-100 text-slate-700'" class="px-3 py-1.5 rounded-lg font-semibold cursor-pointer">
                Materia del Catálogo ({{ $availableSubjects->count() }})
            </button>
            <button type="button" @click="mode = 'new'" :class="mode === 'new' ? 'bg-[#0F2744] text-white' : 'bg-slate-100 text-slate-700'" class="px-3 py-1.5 rounded-lg font-semibold cursor-pointer">
                + Crear Nueva Materia
            </button>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.careers.subjects.store', $career) }}" class="space-y-4">
        @csrf
        <input type="hidden" name="mode" :value="mode">

        <div x-show="mode === 'existing'" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-3">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Seleccionar Materia de {{ $career->faculty->name }} *</label>
                <select name="subject_id" :required="mode === 'existing'" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                    <option value="">— Seleccionar materia —</option>
                    @foreach($availableSubjects as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->code }} — {{ $sub->name }} ({{ $sub->credits }} cr.)</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div x-show="mode === 'new'" x-cloak class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Código *</label>
                <input name="new_code" type="text" :required="mode === 'new'" placeholder="Ej. ARQ-101" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm uppercase">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Área (Prefijo) *</label>
                <input name="new_area_prefix" type="text" :required="mode === 'new'" placeholder="Ej. ARQ / MAT" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm uppercase">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre de la Materia *</label>
                <input name="new_name" type="text" :required="mode === 'new'" placeholder="Ej. Dibujo Arquitectónico I" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Créditos *</label>
                <input name="new_credits" type="number" min="1" max="12" value="4" :required="mode === 'new'" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nivel (1–8) *</label>
                <select name="level" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                    @for($l = 1; $l <= 8; $l++)
                        <option value="{{ $l }}">Nivel {{ $l }} (Año {{ ceil($l/2) }})</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Mes en Nivel (1–6) *</label>
                <select name="month_in_level" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                    @for($m = 1; $m <= 6; $m++)
                        <option value="{{ $m }}">Mes {{ $m }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Prerrequisito: Nivel completo (opcional)</label>
                <select name="requires_full_level" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                    <option value="">Ninguno</option>
                    @for($rl = 1; $rl <= 7; $rl++)
                        <option value="{{ $rl }}">Nivel {{ $rl }} completo aprobado</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Prerrequisito(s) por Materia (Ctrl+clic)</label>
                <select name="prerequisite_ids[]" multiple size="2" class="w-full px-2 py-1 rounded-lg border border-slate-300 text-xs bg-white">
                    @foreach($allItems as $existingItem)
                        <option value="{{ $existingItem->subject_id }}">N{{ $existingItem->level }} · {{ $existingItem->subject->code }} - {{ $existingItem->subject->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
            <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer">
                <input type="checkbox" name="is_elective" value="1" x-model="isElective" class="rounded border-slate-300">
                <span>Es cupo de Electiva</span>
                <input x-show="isElective" x-cloak type="number" name="elective_number" min="1" max="4" value="1" class="w-16 px-2 py-1 rounded border border-slate-300 text-xs" placeholder="1-4">
            </label>
            <button type="submit" class="px-5 py-2 rounded-lg bg-[#0F2744] hover:bg-[#1E3A5F] text-white text-xs font-bold cursor-pointer">
                + Agregar al Pensum
            </button>
        </div>
    </form>
</div>

@if($items->isEmpty())
    <div class="bg-white rounded-xl border border-slate-200 p-10 text-center text-sm text-slate-500">
        Esta carrera aún no tiene materias en su pensum. Usa el formulario superior para agregar materias por nivel y mes.
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @foreach($items as $level => $levelItems)
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 bg-[#0F2744] text-white flex items-center justify-between">
                    <span class="font-bold text-sm">Nivel {{ $level }} (Año {{ ceil($level / 2) }} · Semestre {{ $level % 2 === 1 ? 'I' : 'II' }})</span>
                    <span class="text-xs text-sky-200">{{ $levelItems->count() }} módulos</span>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach($levelItems as $item)
                        @php $style = $item->subject->areaStyle(); @endphp
                        <div class="p-3.5 flex items-start justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-[11px] font-bold text-slate-400">Mes {{ $item->month_in_level }}</span>
                                    <span class="px-2 py-0.5 rounded font-mono text-[11px] font-bold border {{ $style['bg'] }} {{ $style['text'] }} {{ $style['border'] }}">
                                        {{ $item->subject->code }}
                                    </span>
                                    @if($item->is_elective)
                                        <span class="px-1.5 py-0.5 rounded bg-fuchsia-100 text-fuchsia-800 text-[10px] font-bold">Electiva {{ $item->elective_number }}</span>
                                    @endif
                                </div>
                                <div class="font-semibold text-sm text-slate-800 mt-1">{{ $item->subject->name }}</div>
                                @if($item->prerequisites->isNotEmpty() || $item->requires_full_level)
                                    <div class="text-[11px] text-amber-800 bg-amber-50 border border-amber-200 rounded px-2 py-0.5 mt-1 inline-block">
                                        <strong>Requisitos:</strong>
                                        @if($item->requires_full_level)
                                            <span>Nivel {{ $item->requires_full_level }} completo aprobado</span>{{ $item->prerequisites->isNotEmpty() ? ' · ' : '' }}
                                        @endif
                                        {{ $item->prerequisites->map(fn($p) => $p->code . ' (' . $p->name . ')')->implode(', ') }}
                                    </div>
                                @else
                                    <div class="text-[11px] text-slate-400 mt-0.5">Sin prerrequisitos</div>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="text-xs font-semibold text-slate-500">{{ $item->subject->credits }} cr.</span>
                                <form method="POST" action="{{ route('admin.careers.subjects.destroy', [$career, $item]) }}" onsubmit="return confirm('¿Retirar esta materia del pensum?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-2 py-0.5 rounded bg-rose-50 hover:bg-rose-100 text-rose-700 text-[11px] font-semibold cursor-pointer">Quitar</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
