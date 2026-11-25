@extends('layouts.app')

@section('title', 'Matrículas Semestrales')
@section('header', 'Control de Matrículas Semestrales')

@section('content')
<form method="GET" class="bg-white rounded-xl border border-slate-200 p-4 mb-6 shadow-sm flex flex-wrap items-center gap-3">
    <select name="semester_id" onchange="this.form.submit()" class="px-3.5 py-2 rounded-lg border border-slate-300 text-sm bg-white font-semibold">
        @foreach($semesters as $sem)
            <option value="{{ $sem->id }}" @selected($sem->id === $selectedSemester->id)>Semestre {{ $sem->code }} ({{ $sem->periodLabel() }})</option>
        @endforeach
    </select>
    <input type="text" name="search" value="{{ $search }}" placeholder="Buscar estudiante por nombre, código o correo..."
           class="px-3.5 py-2 rounded-lg border border-slate-300 text-sm flex-1 min-w-[220px]">
    <button type="submit" class="px-4 py-2 rounded-lg bg-[#0F2744] text-white text-sm font-semibold cursor-pointer">Buscar</button>
</form>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <table class="w-full text-sm text-left">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
            <tr>
                <th class="px-5 py-3">Estudiante</th>
                <th class="px-4 py-3">Carrera / Facultad</th>
                <th class="px-4 py-3 text-center">Cupo Usado ({{ $selectedSemester->code }})</th>
                <th class="px-4 py-3 text-center">Matrícula</th>
                <th class="px-5 py-3 text-right">Acción</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($rows as $row)
                @php
                    $st = $row['student'];
                    $car = $row['career'];
                    $tuition = $row['tuition'];
                    $isActive = $tuition && $tuition->is_active;
                @endphp
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3.5">
                        <a href="{{ route('admin.users.show', $st) }}" class="font-semibold text-[#0F2744] hover:underline">{{ $st->name }}</a>
                        <div class="text-xs text-slate-500 font-mono">{{ $st->code }} · {{ $st->email }}</div>
                    </td>
                    <td class="px-4 py-3.5 text-xs">
                        <div class="flex items-center gap-1.5">
                            <span class="font-semibold text-slate-800">{{ $car->name }}</span>
                            @if($row['is_secondary'])
                                <span class="px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800 text-[10px] font-bold">2da Carrera</span>
                            @endif
                        </div>
                        <div class="text-slate-400">{{ $car->faculty->name }}</div>
                    </td>
                    <td class="px-4 py-3.5 text-center font-bold text-slate-700">{{ $row['used'] }} / 6</td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $isActive ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $isActive ? 'Activa' : 'Inactiva' }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <form method="POST" action="{{ route('admin.tuitions.toggle') }}">
                            @csrf
                            <input type="hidden" name="student_id" value="{{ $st->id }}">
                            <input type="hidden" name="semester_id" value="{{ $selectedSemester->id }}">
                            <input type="hidden" name="career_id" value="{{ $car->id }}">
                            <input type="hidden" name="is_active" value="{{ $isActive ? 0 : 1 }}">
                            <button type="submit"
                                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold cursor-pointer {{ $isActive ? 'bg-rose-100 text-rose-700 hover:bg-rose-200' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}">
                                {{ $isActive ? 'Desactivar' : 'Activar Matrícula' }}
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
