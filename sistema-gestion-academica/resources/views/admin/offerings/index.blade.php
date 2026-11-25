@extends('layouts.app')

@section('title', 'Ofertas Académicas')
@section('header', 'Gestión de Ofertas de Materias')

@section('content')
    <!-- Dos pestañas exactas de la Sección 5 -->
    <div class="mb-6 border-b border-slate-200 flex gap-2">
        <a href="{{ route('admin.offerings.index', ['tab' => 'habilitacion']) }}"
            class="px-5 py-3 text-sm font-bold border-b-2 transition {{ $tab === 'habilitacion' ? 'border-[#0F2744] text-[#0F2744]' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
            Habilitación de materias
        </a>
        <a href="{{ route('admin.offerings.index', ['tab' => 'ofertas', 'f_semester' => $filterSemester]) }}"
            class="px-5 py-3 text-sm font-bold border-b-2 transition {{ $tab === 'ofertas' ? 'border-[#0F2744] text-[#0F2744]' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
            Ofertas habilitadas ({{ $allOfferings->count() }})
        </a>
    </div>

    @if($tab === 'habilitacion')
        <!-- Paso 1: Botones de facultades -->
        <div class="mb-6">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2.5">1. Selecciona la Facultad</div>
            <div class="flex flex-wrap gap-3">
                @foreach($faculties as $fac)
                    <a href="{{ route('admin.offerings.index', ['tab' => 'habilitacion', 'faculty_id' => $fac->id, 'semester_id' => $selectedSemester->id, 'month' => $selectedMonth, 'shift' => $selectedShift]) }}"
                        class="px-4 py-2.5 rounded-xl text-sm font-bold border transition shadow-2xs
                               {{ $selectedFacultyId === $fac->id ? 'bg-[#0F2744] text-white border-[#0F2744]' : 'bg-white text-slate-700 border-slate-300 hover:border-[#1E3A5F]' }}">
                        {{ $fac->name }} ({{ $fac->code }})
                    </a>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Paso 2: Catálogo de materias SOLO de esa facultad -->
            <div class="lg:col-span-5 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col max-h-[680px]"
                x-data="{ q: '' }">
                <div class="p-4 border-b border-slate-200 space-y-2.5">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-500">2. Catálogo de Materias de la
                        Facultad ({{ $facultySubjects->count() }})</div>
                    <input type="text" x-model="q" placeholder="Buscar por código o nombre..."
                        class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                </div>
                <div class="divide-y divide-slate-100 overflow-y-auto flex-1">
                    @foreach($facultySubjects as $subj)
                        @php
                            $style = $subj->areaStyle();
                            $isSel = $selectedSubject && $selectedSubject->id === $subj->id;
                            $careersList = $subj->curriculumItems->pluck('career.code')->unique()->implode(', ');
                        @endphp
                        <a href="{{ route('admin.offerings.index', ['tab' => 'habilitacion', 'faculty_id' => $selectedFacultyId, 'subject_id' => $subj->id, 'semester_id' => $selectedSemester->id, 'month' => $selectedMonth, 'shift' => $selectedShift]) }}"
                            x-show="q === '' || '{{ strtolower($subj->code . ' ' . $subj->name) }}'.includes(q.toLowerCase())"
                            class="block px-4 py-3 transition {{ $isSel ? 'bg-sky-50 border-l-4 border-[#0F2744]' : 'hover:bg-slate-50' }}">
                            <div class="flex items-center justify-between gap-2">
                                <span
                                    class="px-2 py-0.5 rounded font-mono text-[11px] font-bold border {{ $style['bg'] }} {{ $style['text'] }} {{ $style['border'] }}">{{ $subj->code }}</span>
                                <span class="text-[11px] text-slate-400">Carreras: {{ $careersList ?: '—' }}</span>
                            </div>
                            <div class="font-semibold text-sm text-slate-800 mt-1">{{ $subj->name }}</div>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Paso 3 y 4: Formulario de Oferta + Lista de Docentes con disponibilidad + Confirmación -->
            <div class="lg:col-span-7">
                @if($selectedSubject)
                    @php
                        $defaultStart = sprintf('%04d-%02d-01', $selectedSemester->year, $selectedMonth);
                        $defaultEnd = \Illuminate\Support\Carbon::parse($defaultStart)->endOfMonth()->format('Y-m-d');
                        $shiftDefaults = \App\Models\Career::SHIFT_SCHEDULES[$selectedShift] ?? ['start' => '07:30', 'end' => '11:30'];
                    @endphp
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6" x-data="{
                                    confirmOpen: false,
                                    teacherName: 'Docente por asignar',
                                    classroom: '{{ old('classroom', '') }}',
                                    capacity: '{{ old('max_capacity', 50) }}',
                                    startDate: '{{ old('start_date', $defaultStart) }}',
                                    endDate: '{{ old('end_date', $defaultEnd) }}',
                                    startTime: '{{ old('start_time', $shiftDefaults['start']) }}',
                                    endTime: '{{ old('end_time', $shiftDefaults['end']) }}',
                                    daysLabel: '{{ old('days_label', 'Lunes a Viernes') }}'
                                 }">
                        <div class="pb-4 mb-4 border-b border-slate-200 flex items-center justify-between gap-3">
                            <div>
                                <div class="text-xs font-bold uppercase text-slate-400">3. Configurar Oferta (se crea habilitada)
                                </div>
                                <h2 class="text-lg font-bold text-[#0F2744] mt-0.5">{{ $selectedSubject->code }} —
                                    {{ $selectedSubject->name }}</h2>
                            </div>
                        </div>

                        <!-- Filtro de semestre/mes/turno para refrescar disponibilidad de docentes en vivo -->
                        <form method="GET"
                            class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5 p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                            <input type="hidden" name="tab" value="habilitacion">
                            <input type="hidden" name="faculty_id" value="{{ $selectedFacultyId }}">
                            <input type="hidden" name="subject_id" value="{{ $selectedSubject->id }}">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Semestre</label>
                                <select name="semester_id" onchange="this.form.submit()"
                                    class="w-full px-2.5 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                                    @foreach($semesters as $sem)
                                        <option value="{{ $sem->id }}" @selected($sem->id === $selectedSemester->id)>{{ $sem->code }}
                                            ({{ $sem->periodLabel() }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Mes</label>
                                <select name="month" onchange="this.form.submit()"
                                    class="w-full px-2.5 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                                    @foreach($selectedSemester->months() as $mNum => $mName)
                                        <option value="{{ $mNum }}" @selected($mNum === $selectedMonth)>{{ $mName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Turno (habilitados)</label>
                                <select name="shift" onchange="this.form.submit()"
                                    class="w-full px-2.5 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                                    @foreach($allowedShifts as $sh)
                                        <option value="{{ $sh }}" @selected($sh === $selectedShift)>
                                            {{ \App\Models\Career::SHIFT_SCHEDULES[$sh]['label'] ?? \App\Models\Career::SHIFTS[$sh] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>

                        <!-- Formulario real de creación -->
                        <form id="createOfferingForm" method="POST" action="{{ route('admin.offerings.store') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="subject_id" value="{{ $selectedSubject->id }}">
                            <input type="hidden" name="semester_id" value="{{ $selectedSemester->id }}">
                            <input type="hidden" name="month" value="{{ $selectedMonth }}">
                            <input type="hidden" name="shift" value="{{ $selectedShift }}">

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Fecha de inicio *</label>
                                    <input type="date" name="start_date" x-model="startDate" required
                                        class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Fecha de fin *</label>
                                    <input type="date" name="end_date" x-model="endDate" required
                                        class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Horario inicio (Turno
                                        {{ \App\Models\Career::SHIFTS[$selectedShift] }}) *</label>
                                    <input type="time" name="start_time" x-model="startTime" required
                                        class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Horario fin *</label>
                                    <input type="time" name="end_time" x-model="endTime" required
                                        class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Días de clase *</label>
                                    <input type="text" name="days_label" x-model="daysLabel" required
                                        class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                                </div>
                                <div class="grid grid-cols-2 gap-2.5">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nº Aula <span class="text-slate-400 font-normal">(opcional)</span></label>
                                        <input type="number" name="classroom" min="1" max="9999" x-model="classroom" placeholder="Asignar luego"
                                            class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Cupo (≤50) *</label>
                                        <input type="number" name="max_capacity" min="1" max="50" x-model="capacity" required
                                            class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                                    </div>
                                </div>
                            </div>

                            <!-- Paso 4: Lista de docentes de esa facultad mostrando choques (opcional: asignable después) -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                                    4. Docentes de la Facultad ({{ \App\Models\Semester::ALL_MONTHS[$selectedMonth] }} · Turno
                                    {{ \App\Models\Career::SHIFTS[$selectedShift] }}) — Opcional
                                </label>
                                <div class="space-y-2">
                                    <label class="flex items-center justify-between gap-3 p-3 rounded-xl border border-dashed border-slate-300 hover:border-[#0F2744] cursor-pointer bg-slate-50">
                                        <div class="flex items-center gap-3">
                                            <input type="radio" name="teacher_id" value="" checked @change="teacherName = 'Docente por asignar'">
                                            <div>
                                                <div class="font-semibold text-sm text-slate-700">Sin docente asignado (asignar después)</div>
                                                <div class="text-xs text-slate-500">Podrás asignar o cambiar docente y aula desde "Ofertas habilitadas → Editar".</div>
                                            </div>
                                        </div>
                                        <span class="px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700 text-xs font-semibold">Opcional</span>
                                    </label>
                                    @forelse($teachersAvailability as $entry)
                                        @php $t = $entry['teacher']; @endphp
                                        <label
                                            class="flex items-center justify-between gap-3 p-3 rounded-xl border {{ $entry['available'] ? 'border-slate-200 hover:border-[#0F2744] cursor-pointer bg-white' : 'border-rose-200 bg-rose-50/70 opacity-75 cursor-not-allowed' }}">
                                            <div class="flex items-center gap-3">
                                                <input type="radio" name="teacher_id" value="{{ $t->id }}" {{ $entry['available'] ? '' : 'disabled' }} @change="teacherName = '{{ addslashes($t->name) }}'">
                                                <div>
                                                    <div class="font-semibold text-sm text-slate-800">{{ $t->name }}</div>
                                                    <div class="text-xs text-slate-500">{{ $t->code }} · {{ $t->email }}</div>
                                                </div>
                                            </div>
                                            @if($entry['available'])
                                                <span
                                                    class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold">Disponible</span>
                                            @else
                                                <span
                                                    class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-700 text-[11px] font-semibold">{{ $entry['conflict_label'] }}</span>
                                            @endif
                                        </label>
                                    @empty
                                        <div class="p-3 rounded-lg bg-amber-50 border border-amber-200 text-xs text-amber-800">
                                            No hay docentes asignados aún a esta facultad. Puedes habilitar la oferta sin docente y asignarlo luego.
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <div class="pt-3 flex justify-end">
                                <button type="button"
                                    @click="if(document.getElementById('createOfferingForm').reportValidity()) confirmOpen = true"
                                    class="px-5 py-2.5 rounded-lg bg-[#0F2744] hover:bg-[#1E3A5F] text-white text-sm font-semibold cursor-pointer">
                                    Revisar y Confirmar Oferta
                                </button>
                            </div>

                            <!-- Pantalla / Modal de confirmación antes de guardar -->
                            <div x-show="confirmOpen" x-cloak
                                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4">
                                <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                                    <h3 class="text-lg font-bold text-[#0F2744]">Confirmar Habilitación de Materia</h3>
                                    <p class="text-xs text-slate-500 mt-1">Una vez creada, la fecha de inicio, fecha de fin y turno
                                        quedarán bloqueados.</p>
                                    <dl class="mt-4 space-y-2 text-sm bg-slate-50 p-4 rounded-xl border border-slate-200">
                                        <div class="flex justify-between">
                                            <dt class="text-slate-500">Materia:</dt>
                                            <dd class="font-bold text-[#0F2744]">{{ $selectedSubject->code }} -
                                                {{ $selectedSubject->name }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-slate-500">Semestre / Mes:</dt>
                                            <dd class="font-semibold">{{ $selectedSemester->code }} ·
                                                {{ \App\Models\Semester::ALL_MONTHS[$selectedMonth] }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-slate-500">Turno / Horario:</dt>
                                            <dd class="font-semibold">{{ \App\Models\Career::SHIFTS[$selectedShift] }} (<span
                                                    x-text="startTime"></span>-<span x-text="endTime"></span>)</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-slate-500">Fechas:</dt>
                                            <dd class="font-semibold"><span x-text="startDate"></span> al <span
                                                    x-text="endDate"></span></dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-slate-500">Docente:</dt>
                                            <dd class="font-semibold" x-text="teacherName"></dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-slate-500">Aula / Cupo:</dt>
                                            <dd class="font-semibold"><span x-text="classroom ? ('Aula ' + classroom) : 'Aula por asignar'"></span> · <span
                                                    x-text="capacity"></span> alumnos</dd>
                                        </div>
                                    </dl>
                                    <div class="mt-5 flex justify-end gap-2.5">
                                        <button type="button" @click="confirmOpen = false"
                                            class="px-4 py-2 rounded-lg border border-slate-300 text-sm font-semibold text-slate-700 cursor-pointer">Volver</button>
                                        <button type="submit"
                                            class="px-5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold cursor-pointer">Confirmar
                                            y Habilitar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="bg-white rounded-xl border border-slate-200 p-12 text-center text-slate-500">
                        Selecciona una materia del catálogo de la izquierda para habilitar una oferta académica.
                    </div>
                @endif
            </div>
        </div>
    @else
        <!-- PESTAÑA 2: Ofertas habilitadas -->
        <form method="GET" class="bg-white rounded-xl border border-slate-200 p-4 mb-5 shadow-sm flex flex-wrap gap-3">
            <input type="hidden" name="tab" value="ofertas">
            <select name="f_faculty" class="px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                <option value="">Todas las facultades</option>
                @foreach($faculties as $fac)
                    <option value="{{ $fac->id }}" @selected($filterFaculty === $fac->id)>{{ $fac->name }}</option>
                @endforeach
            </select>
            <select name="f_semester" class="px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                @foreach($semesters as $sem)
                    <option value="{{ $sem->id }}" @selected($filterSemester === $sem->id)>{{ $sem->code }}</option>
                @endforeach
            </select>
            <select name="f_month" class="px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                <option value="">Todos los meses</option>
                @foreach(\App\Models\Semester::ALL_MONTHS as $mNum => $mName)
                    <option value="{{ $mNum }}" @selected($filterMonth === $mNum)>{{ $mName }}</option>
                @endforeach
            </select>
            <select name="f_shift" class="px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                <option value="">Todos los turnos</option>
                @foreach(\App\Models\Career::SHIFTS as $sk => $sl)
                    <option value="{{ $sk }}" @selected($filterShift === $sk)>{{ $sl }}</option>
                @endforeach
            </select>
            <button type="submit"
                class="px-4 py-2 rounded-lg bg-[#0F2744] text-white text-sm font-semibold cursor-pointer">Filtrar</button>
        </form>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Materia</th>
                            <th class="px-3 py-3">Mes / Turno</th>
                            <th class="px-3 py-3">Fechas</th>
                            <th class="px-3 py-3">Docente / Aula</th>
                            <th class="px-3 py-3 text-center">Inscritos</th>
                            <th class="px-3 py-3 text-center">Estado</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($allOfferings as $off)
                            @php $style = $off->subject->areaStyle();
                            $st = $off->computedStatus(); @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <span
                                        class="px-2 py-0.5 rounded font-mono text-[11px] font-bold {{ $style['bg'] }} {{ $style['text'] }}">{{ $off->subject->code }}</span>
                                    <div class="font-semibold text-slate-800 mt-0.5">{{ $off->subject->name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $off->subject->faculty->name }}</div>
                                </td>
                                <td class="px-3 py-3 text-xs">
                                    <div class="font-semibold text-slate-700">{{ $off->monthLabel() }} · {{ $off->shiftLabel() }}
                                    </div>
                                    <div class="text-sky-700 font-mono text-[11px]">{{ $off->timeRange() }}</div>
                                </td>
                                <td class="px-3 py-3 text-xs text-slate-600">
                                    {{ $off->start_date->format('d/m/Y') }}<br>{{ $off->end_date->format('d/m/Y') }}
                                </td>
                                <td class="px-3 py-3 text-xs">
                                    <div class="font-semibold {{ $off->teacher ? 'text-slate-700' : 'text-amber-700 italic' }}">{{ $off->teacherLabel() }}</div>
                                    <div class="{{ $off->classroom ? 'text-slate-500' : 'text-amber-700 italic' }}">{{ $off->classroomLabel() }}</div>
                                </td>
                                <td class="px-3 py-3 text-center text-xs font-bold">{{ $off->active_enrollments_count }} /
                                    {{ $off->max_capacity }}</td>
                                <td class="px-3 py-3 text-center">
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold
                                                {{ $st === 'habilitada' ? 'bg-sky-100 text-sky-800' : ($st === 'en_curso' ? 'bg-emerald-100 text-emerald-800' : ($st === 'deshabilitada' ? 'bg-rose-100 text-rose-700' : 'bg-slate-200 text-slate-700')) }}">
                                        {{ $off->statusLabel() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if(!$off->is_disabled && !$off->isManuallyClosed())
                                            <a href="{{ route('admin.offerings.edit', $off) }}"
                                                class="px-2.5 py-1 rounded bg-amber-100 text-amber-800 text-xs font-semibold">Editar</a>
                                            <form method="POST" action="{{ route('admin.offerings.disable', $off) }}"
                                                onsubmit="return confirm('¿Deshabilitar esta oferta? Las inscripciones activas quedarán anuladas y no consumirán cupo.');">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                    class="px-2.5 py-1 rounded bg-slate-200 text-slate-700 text-xs font-semibold cursor-pointer">Deshabilitar</button>
                                            </form>
                                        @endif
                                        @if($off->canBeDeleted())
                                            <form method="POST" action="{{ route('admin.offerings.destroy', $off) }}"
                                                onsubmit="return confirm('¿Eliminar permanentemente esta oferta sin inscritos?');">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="px-2.5 py-1 rounded bg-rose-100 text-rose-700 text-xs font-semibold cursor-pointer">Eliminar</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-500">No se encontraron ofertas con los filtros
                                    seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection