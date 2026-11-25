@extends('layouts.app')

@section('title', 'Gestión de Carreras')
@section('header', 'Administración de Carreras')
@section('subheader', 'Organiza las carreras institucionales para clasificar materias y reportes por carrera')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 h-fit">
        <h2 class="font-bold text-[#0F2744] mb-4">Registrar Carrera</h2>
        <form method="POST" action="{{ route('admin.careers.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Código *</label>
                <input name="code" type="text" required placeholder="Ej. IND" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm uppercase">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre de Carrera *</label>
                <input name="name" type="text" required placeholder="Ej. Ingeniería Industrial" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Facultad *</label>
                <input name="faculty" type="text" required value="Facultad de Ingeniería" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
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
                    <th class="px-4 py-3 text-center">Materias</th>
                    <th class="px-4 py-3 text-center">Estudiantes</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($careers as $career)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3.5">
                            <span class="font-mono text-xs font-bold text-sky-700">{{ $career->code }}</span>
                            <div class="font-semibold text-[#0F2744]">{{ $career->name }}</div>
                            <div class="text-xs text-slate-500">{{ $career->faculty }}</div>
                        </td>
                        <td class="px-4 py-3.5 text-center font-semibold">{{ $career->courses_count }}</td>
                        <td class="px-4 py-3.5 text-center font-semibold">{{ $career->students_count }}</td>
                        <td class="px-4 py-3.5 text-right">
                            <form method="POST" action="{{ route('admin.careers.destroy', $career) }}"
                                  onsubmit="return confirm('¿Eliminar la carrera {{ $career->name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1 rounded bg-rose-100 text-rose-700 text-xs font-semibold hover:bg-rose-200 cursor-pointer">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
