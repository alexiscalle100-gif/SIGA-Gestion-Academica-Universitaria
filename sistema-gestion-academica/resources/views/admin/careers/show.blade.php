@extends('layouts.app')

@section('title', 'Pensum — ' . $career->name)
@section('header', 'Pensum: ' . $career->name . ' (' . $career->code . ')')

@section('content')
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-3 text-xs">
        <a href="{{ route('admin.careers.index') }}" class="font-semibold text-slate-600 hover:text-[#0F2744]">← Volver a Carreras</a>
        <span class="text-slate-300">|</span>
        <span class="font-semibold text-slate-700">{{ $career->faculty->name }}</span>
        <span class="px-2.5 py-0.5 rounded-full bg-sky-100 text-[#0F2744] font-bold">8 Niveles · 48 Materias (1/mes)</span>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('reports.download', ['type' => 'subjects-by-career', 'format' => 'pdf', 'career_id' => $career->id]) }}" class="px-3 py-1.5 rounded-lg bg-rose-600 text-white text-xs font-semibold">Pensum PDF</a>
        <a href="{{ route('reports.download', ['type' => 'subjects-by-career', 'format' => 'excel', 'career_id' => $career->id]) }}" class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-semibold">Pensum Excel</a>
    </div>
</div>

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
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    Prerreq.:
                                    @if($item->requires_full_level)
                                        <span class="font-semibold text-amber-700">Nivel {{ $item->requires_full_level }} completo</span>
                                    @endif
                                    {{ $item->prerequisites->pluck('code')->implode(', ') }}
                                </div>
                            @endif
                        </div>
                        <span class="text-xs font-semibold text-slate-500 shrink-0">{{ $item->subject->credits }} cr.</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
@endsection
