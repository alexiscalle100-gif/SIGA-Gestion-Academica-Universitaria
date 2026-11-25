@extends('layouts.app')

@section('title', 'Carreras y Pensums')
@section('header', 'Carreras y Pensums')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 h-fit">
        <h2 class="font-bold text-[#0F2744] mb-4">Nueva Carrera</h2>
        <form method="POST" action="{{ route('admin.careers.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Facultad *</label>
                <select name="faculty_id" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                    <option value="">— Seleccionar —</option>
                    @foreach($faculties as $fac)
                        <option value="{{ $fac->id }}">{{ $fac->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Código *</label>
                <input name="code" type="text" required placeholder="Ej. IND" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm uppercase">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre *</label>
                <input name="name" type="text" required placeholder="Ej. Ingeniería Industrial" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Turnos habilitados *</label>
                <div class="flex flex-wrap gap-3 text-xs">
                    @foreach(\App\Models\Career::SHIFTS as $key => $label)
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="checkbox" name="shifts[]" value="{{ $key }}" checked class="rounded border-slate-300">
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Descripción</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm"></textarea>
            </div>
            <button type="submit" class="w-full py-2.5 rounded-lg bg-[#0F2744] hover:bg-[#1E3A5F] text-white text-sm font-semibold cursor-pointer">
                Guardar Carrera
            </button>
        </form>
    </div>

    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3">Carrera</th>
                    <th class="px-3 py-3">Turnos</th>
                    <th class="px-3 py-3 text-center">Pensum</th>
                    <th class="px-3 py-3 text-center">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($careers as $career)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-sky-700">{{ $career->code }}</span>
                                <span class="text-xs text-slate-400">{{ $career->faculty->name }}</span>
                            </div>
                            <div class="font-semibold text-[#0F2744]">{{ $career->name }}</div>
                        </td>
                        <td class="px-3 py-3.5">
                            <div class="flex flex-wrap gap-1">
                                @foreach($career->shiftLabels() as $sl)
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[11px] font-medium">{{ $sl }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-3 py-3.5 text-center">
                            <a href="{{ route('admin.careers.show', $career) }}" class="text-xs font-bold text-sky-700 hover:underline">
                                {{ $career->curriculum_items_count }} materias
                            </a>
                        </td>
                        <td class="px-3 py-3.5 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $career->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                {{ $career->is_active ? 'Activa' : 'Inactiva' }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.careers.show', $career) }}" class="px-2.5 py-1 rounded bg-sky-100 text-[#0F2744] text-xs font-semibold">Pensum</a>
                                @if($career->is_active)
                                    <form method="POST" action="{{ route('admin.careers.destroy', $career) }}"
                                          onsubmit="return confirm('Si tiene datos asociados se desactivará en lugar de eliminarse. ¿Continuar?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 rounded bg-rose-100 text-rose-700 text-xs font-semibold cursor-pointer">
                                            {{ ($career->curriculum_items_count + $career->students_count) > 0 ? 'Desactivar' : 'Eliminar' }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
