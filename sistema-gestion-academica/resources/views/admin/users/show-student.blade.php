@extends('layouts.app')

@section('title', 'Perfil Estudiante — ' . $user->name)
@section('header', 'Estudiante: ' . $user->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.users.index', ['role' => 'estudiante']) }}" class="text-xs font-semibold text-slate-600 hover:text-[#0F2744]">← Volver a Usuarios</a>
</div>

<div class="mb-6 border-b border-slate-200 flex gap-2">
    <a href="{{ route('admin.users.show', [$user, 'tab' => 'perfil']) }}"
       class="px-5 py-3 text-sm font-bold border-b-2 {{ $tab === 'perfil' ? 'border-[#0F2744] text-[#0F2744]' : 'border-transparent text-slate-500' }}">
        Perfil
    </a>
    <a href="{{ route('admin.users.show', [$user, 'tab' => 'record']) }}"
       class="px-5 py-3 text-sm font-bold border-b-2 {{ $tab === 'record' ? 'border-[#0F2744] text-[#0F2744]' : 'border-transparent text-slate-500' }}">
        Récord Académico
    </a>
</div>

@if($tab === 'perfil')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5">
            <div>
                <h2 class="font-bold text-[#0F2744] mb-3">Datos Personales</h2>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Nombre:</dt><dd class="font-semibold">{{ $user->name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Código:</dt><dd class="font-mono font-semibold">{{ $user->code }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Correo:</dt><dd>{{ $user->email }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Documento / CI:</dt><dd>{{ $user->document_number ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Teléfono:</dt><dd>{{ $user->phone ?? '—' }}</dd></div>
                </dl>
            </div>

            <div class="pt-4 border-t border-slate-200">
                <h3 class="font-bold text-[#0F2744] text-sm mb-2.5">Carreras del Estudiante (Doble Carrera)</h3>
                <div class="space-y-2">
                    @foreach($studentCareers as $idx => $car)
                        <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-between gap-2 text-xs">
                            <div>
                                <div class="font-bold text-[#0F2744]">{{ $car->name }} ({{ $car->code }})</div>
                                <div class="text-slate-500">{{ $car->faculty->name }} · {{ $idx === 0 ? 'Carrera Principal' : 'Segunda Carrera' }}</div>
                            </div>
                            @if($idx > 0)
                                <form method="POST" action="{{ route('admin.users.careers.remove', [$user, $car]) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-2 py-1 rounded bg-rose-100 text-rose-700 font-semibold cursor-pointer">Quitar</button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($studentCareers->count() < 2)
                    <form method="POST" action="{{ route('admin.users.careers.add', $user) }}" class="mt-3 flex gap-2">
                        @csrf
                        <select name="career_id" required class="flex-1 px-2.5 py-2 rounded-lg border border-slate-300 text-xs bg-white">
                            <option value="">— Habilitar 2da Carrera (incluso otra facultad) —</option>
                            @foreach($allCareers as $c)
                                @if(! $studentCareers->contains('id', $c->id))
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->faculty->name }})</option>
                                @endif
                            @endforeach
                        </select>
                        <button type="submit" class="px-3 py-2 rounded-lg bg-[#0F2744] text-white text-xs font-bold cursor-pointer">Agregar</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h2 class="font-bold text-[#0F2744] mb-4">Matrículas del Semestre {{ $currentSemester->code }}</h2>
            <div class="space-y-3 mb-5">
                @foreach($studentCareers as $car)
                    @php $curT = $currentTuitions->get($car->id); $act = $curT && $curT->is_active; @endphp
                    <div class="p-4 rounded-xl border {{ $act ? 'bg-emerald-50 border-emerald-200' : 'bg-amber-50 border-amber-200' }} flex items-center justify-between gap-3">
                        <div>
                            <div class="text-xs font-bold text-slate-700">{{ $car->name }} ({{ $car->faculty->code }})</div>
                            <div class="text-sm font-bold {{ $act ? 'text-emerald-800' : 'text-amber-800' }}">
                                {{ $act ? 'Matrícula Activa' : 'Matrícula Inactiva' }}
                            </div>
                        </div>
                        <form method="POST" action="{{ route('admin.tuitions.toggle') }}">
                            @csrf
                            <input type="hidden" name="student_id" value="{{ $user->id }}">
                            <input type="hidden" name="semester_id" value="{{ $currentSemester->id }}">
                            <input type="hidden" name="career_id" value="{{ $car->id }}">
                            <input type="hidden" name="is_active" value="{{ $act ? 0 : 1 }}">
                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold cursor-pointer {{ $act ? 'bg-rose-600 text-white' : 'bg-emerald-600 text-white' }}">
                                {{ $act ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

            <h3 class="text-xs font-bold uppercase text-slate-500 mb-2">Historial de Matrículas</h3>
            <div class="space-y-1.5 text-xs">
                @forelse($allTuitions as $t)
                    <div class="flex justify-between p-2 rounded bg-slate-50 border border-slate-200">
                        <span class="font-semibold">{{ $t->semester->code }} — {{ $t->career->name }}</span>
                        <span class="font-bold {{ $t->is_active ? 'text-emerald-700' : 'text-slate-500' }}">{{ $t->is_active ? 'Activa' : 'Inactiva' }}</span>
                    </div>
                @empty
                    <div class="text-slate-400">Sin matrículas previas.</div>
                @endforelse
            </div>
        </div>
    </div>
@else
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="font-bold text-[#0F2744]">Récord Académico (Materias, Carrera, Estado y Nota Final)</h2>
        </div>
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3">Semestre</th>
                    <th class="px-4 py-3">Materia</th>
                    <th class="px-3 py-3">Carrera</th>
                    <th class="px-3 py-3 text-center">Intento</th>
                    <th class="px-3 py-3 text-center">Estado</th>
                    <th class="px-4 py-3 text-center">Nota Final</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($academicRecord as $enr)
                    <tr>
                        <td class="px-4 py-3 text-xs">{{ $enr->semester->code }} · {{ $enr->offering->monthLabel() }}</td>
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $enr->subject->code }} — {{ $enr->subject->name }}</td>
                        <td class="px-3 py-3 text-xs text-slate-600">{{ $enr->career?->name ?? $user->career?->name }}</td>
                        <td class="px-3 py-3 text-center text-xs">#{{ $enr->attempt_number }}</td>
                        <td class="px-3 py-3 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                {{ $enr->status === 'aprobada' ? 'bg-emerald-100 text-emerald-800' : ($enr->status === 'reprobada' ? 'bg-rose-100 text-rose-700' : ($enr->status === 'anulada' ? 'bg-slate-200 text-slate-600' : 'bg-sky-100 text-sky-800')) }}">
                                {{ $enr->statusLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center font-bold text-[#0F2744]">{{ $enr->final_score ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-6 text-center text-slate-500">Sin materias cursadas aún.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endif
@endsection
