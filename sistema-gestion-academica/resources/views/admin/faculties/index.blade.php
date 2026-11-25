@extends('layouts.app')

@section('title', 'Facultades')
@section('header', 'Gestión de Facultades')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 h-fit">
        <h2 class="font-bold text-[#0F2744] mb-4">Nueva Facultad</h2>
        <form method="POST" action="{{ route('admin.faculties.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Código *</label>
                <input name="code" type="text" required placeholder="Ej. DER" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm uppercase">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre *</label>
                <input name="name" type="text" required placeholder="Ej. Facultad de Derecho" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Descripción</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm"></textarea>
            </div>
            <button type="submit" class="w-full py-2.5 rounded-lg bg-[#0F2744] hover:bg-[#1E3A5F] text-white text-sm font-semibold cursor-pointer">
                Guardar Facultad
            </button>
        </form>
    </div>

    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3">Facultad</th>
                    <th class="px-3 py-3 text-center">Carreras</th>
                    <th class="px-3 py-3 text-center">Materias</th>
                    <th class="px-3 py-3 text-center">Docentes</th>
                    <th class="px-3 py-3 text-center">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($faculties as $fac)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3.5">
                            <span class="font-mono text-xs font-bold text-sky-700">{{ $fac->code }}</span>
                            <div class="font-semibold text-[#0F2744]">{{ $fac->name }}</div>
                        </td>
                        <td class="px-3 py-3.5 text-center font-semibold">{{ $fac->careers_count }}</td>
                        <td class="px-3 py-3.5 text-center font-semibold">{{ $fac->subjects_count }}</td>
                        <td class="px-3 py-3.5 text-center font-semibold">{{ $fac->teachers_count }}</td>
                        <td class="px-3 py-3.5 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $fac->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                {{ $fac->is_active ? 'Activa' : 'Inactiva' }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if(! $fac->is_active)
                                    <form method="POST" action="{{ route('admin.faculties.update', $fac) }}">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="code" value="{{ $fac->code }}">
                                        <input type="hidden" name="name" value="{{ $fac->name }}">
                                        <input type="hidden" name="description" value="{{ $fac->description }}">
                                        <input type="hidden" name="is_active" value="1">
                                        <button type="submit" class="px-2.5 py-1 rounded bg-emerald-100 text-emerald-800 text-xs font-semibold cursor-pointer">Reactivar</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.faculties.destroy', $fac) }}"
                                          onsubmit="return confirm('Si tiene carreras, docentes o materias asociadas se desactivará en lugar de eliminarse. ¿Continuar?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 rounded bg-rose-100 text-rose-700 text-xs font-semibold cursor-pointer">
                                            {{ ($fac->careers_count + $fac->subjects_count + $fac->teachers_count) > 0 ? 'Desactivar' : 'Eliminar' }}
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
