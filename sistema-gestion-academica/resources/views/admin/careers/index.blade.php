@extends('layouts.app')

@section('title', 'Carreras y Pensums')
@section('header', 'Carreras y Pensums')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="{ editCareer: null }">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 h-fit">
        <h2 class="font-bold text-[#0F2744] mb-1">Nueva Carrera</h2>
        <p class="text-xs text-amber-800 bg-amber-50 border border-amber-200 rounded-lg p-2.5 mb-4">
            Toda carrera nueva se crea <strong>Inhabilitada</strong> hasta agregar sus materias en el pensum y aprobar su habilitación.
        </p>
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
                <input name="code" type="text" required placeholder="Ej. ARQ" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm uppercase">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre *</label>
                <input name="name" type="text" required placeholder="Ej. Arquitectura" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
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
                Crear Carrera (Inhabilitada)
            </button>
        </form>
    </div>

    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
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
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $career->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $career->is_active ? 'Habilitada' : 'Inhabilitada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                    <a href="{{ route('admin.careers.show', $career) }}" class="px-2.5 py-1 rounded bg-sky-100 text-[#0F2744] text-xs font-semibold">Pensum</a>
                                    <button type="button"
                                            @click='editCareer = @json([
                                                "id" => $career->id,
                                                "url" => route("admin.careers.update", $career),
                                                "faculty_id" => $career->faculty_id,
                                                "code" => $career->code,
                                                "name" => $career->name,
                                                "shifts" => $career->shifts ?? [],
                                                "description" => $career->description ?? "",
                                            ])'
                                            class="px-2.5 py-1 rounded bg-amber-100 text-amber-800 text-xs font-semibold cursor-pointer">
                                        Editar
                                    </button>
                                    @if(! $career->is_active)
                                        <form method="POST" action="{{ route('admin.careers.toggle', $career) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="px-2.5 py-1 rounded bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold cursor-pointer">
                                                Habilitar
                                            </button>
                                        </form>
                                        @if(($career->curriculum_items_count + $career->students_count) === 0)
                                            <form method="POST" action="{{ route('admin.careers.destroy', $career) }}"
                                                  onsubmit="return confirm('¿Eliminar permanentemente esta carrera sin datos asociados?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="px-2.5 py-1 rounded bg-rose-100 text-rose-700 text-xs font-semibold cursor-pointer">Eliminar</button>
                                            </form>
                                        @endif
                                    @else
                                        <form method="POST" action="{{ route('admin.careers.destroy', $career) }}"
                                              onsubmit="return confirm('Si tiene datos asociados se inhabilitará en lugar de eliminarse (podrás volver a habilitarla). ¿Continuar?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 rounded bg-rose-100 text-rose-700 text-xs font-semibold cursor-pointer">
                                                {{ ($career->curriculum_items_count + $career->students_count) > 0 ? 'Deshabilitar' : 'Eliminar' }}
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

    <!-- Modal Editar Carrera -->
    <div x-show="editCareer" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200" x-if="editCareer">
            <h3 class="text-lg font-bold text-[#0F2744] mb-4">Modificar Carrera</h3>
            <form method="POST" :action="editCareer?.url" class="space-y-3">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Facultad *</label>
                    <select name="faculty_id" :value="editCareer?.faculty_id" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                        @foreach($faculties as $fac)
                            <option value="{{ $fac->id }}">{{ $fac->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Código *</label>
                        <input name="code" type="text" x-model="editCareer.code" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm uppercase">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre *</label>
                        <input name="name" type="text" x-model="editCareer.name" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Turnos habilitados *</label>
                    <div class="flex flex-wrap gap-3 text-xs">
                        @foreach(\App\Models\Career::SHIFTS as $key => $label)
                            <label class="flex items-center gap-1.5 cursor-pointer">
                                <input type="checkbox" name="shifts[]" value="{{ $key }}"
                                       :checked="editCareer?.shifts?.includes('{{ $key }}')"
                                       class="rounded border-slate-300">
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Descripción</label>
                    <textarea name="description" rows="2" x-model="editCareer.description" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm"></textarea>
                </div>
                <div class="pt-3 flex justify-end gap-2">
                    <button type="button" @click="editCareer = null" class="px-4 py-2 rounded-lg border border-slate-300 text-sm font-semibold text-slate-700 cursor-pointer">Cancelar</button>
                    <button type="submit" class="px-5 py-2 rounded-lg bg-[#0F2744] hover:bg-[#1E3A5F] text-white text-sm font-bold cursor-pointer">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
