<?php

namespace App\Services\Reports;

use App\Models\Career;
use App\Models\CurriculumItem;
use App\Models\Enrollment;
use App\Models\Offering;
use App\Models\User;

class AcademicReportBuilder
{
    public function subjectsByCareer(?Career $career = null): array
    {
        $items = CurriculumItem::query()
            ->with(['career.faculty', 'subject', 'prerequisites'])
            ->when($career, fn ($q) => $q->where('career_id', $career->id))
            ->orderBy('career_id')
            ->orderBy('level')
            ->orderBy('month_in_level')
            ->get();

        $rows = $items->map(function (CurriculumItem $item) {
            $prereqs = $item->prerequisites->pluck('code')->all();
            if ($item->requires_full_level) {
                $prereqs[] = "Nivel {$item->requires_full_level} completo";
            }

            return [
                $item->career->name,
                'Nivel ' . $item->level,
                'Mes ' . $item->month_in_level,
                $item->subject->code,
                $item->subject->name,
                $item->subject->credits,
                ! empty($prereqs) ? implode(', ', $prereqs) : 'Ninguno',
            ];
        })->values()->all();

        return [
            'title' => 'Pensum y Listado de Materias por Carrera',
            'subtitle' => $career ? "{$career->code} - {$career->name}" : 'Todas las carreras registradas',
            'filename' => 'pensum_materias_' . ($career ? strtolower($career->code) : 'todas'),
            'meta' => [
                'Carrera' => $career ? "{$career->code} - {$career->name}" : 'Todas las carreras',
                'Total materias en pensum' => (string) $items->count(),
            ],
            'columns' => ['Carrera', 'Nivel', 'Mes', 'Código', 'Materia', 'Créditos', 'Prerrequisitos'],
            'rows' => $rows,
            'summary' => [
                'Total materias listadas' => (string) $items->count(),
                'Total créditos' => (string) $items->sum(fn ($i) => $i->subject->credits),
            ],
        ];
    }

    public function offeringEnrolledStudents(Offering $offering): array
    {
        $offering->loadMissing(['subject.faculty', 'teacher', 'semester']);
        $enrollments = $offering->activeEnrollments()
            ->with('student.career')
            ->get()
            ->sortBy(fn (Enrollment $e) => $e->student->name)
            ->values();

        $rows = $enrollments->map(fn (Enrollment $e, int $i) => [
            $i + 1,
            $e->student->code ?? ('EST-' . $e->student->id),
            $e->student->name,
            $e->student->email,
            $e->student->career->name ?? '—',
            'Intento #' . $e->attempt_number,
            $e->enrolled_at?->format('d/m/Y') ?? '—',
        ])->all();

        return [
            'title' => 'Nómina de Estudiantes Inscritos en la Oferta',
            'subtitle' => "{$offering->subject->code} - {$offering->subject->name}",
            'filename' => 'inscritos_' . strtolower($offering->subject->code) . '_' . $offering->id,
            'meta' => [
                'Materia' => "{$offering->subject->code} - {$offering->subject->name}",
                'Semestre / Mes / Turno' => "{$offering->semester->code} · {$offering->monthLabel()} · {$offering->shiftLabel()}",
                'Docente' => $offering->teacherLabel(),
                'Aula' => $offering->classroomLabel(),
            ],
            'columns' => ['#', 'Código', 'Estudiante', 'Correo', 'Carrera', 'Intento', 'Fecha Inscripción'],
            'rows' => $rows,
            'summary' => [
                'Total inscritos' => (string) $enrollments->count(),
                'Cupo disponible' => (string) max(0, $offering->max_capacity - $enrollments->count()),
            ],
        ];
    }

    public function offeringGrades(Offering $offering): array
    {
        $offering->loadMissing(['subject', 'teacher', 'semester', 'tasks']);
        $enrollments = $offering->activeEnrollments()
            ->with(['student', 'taskGrades'])
            ->get()
            ->sortBy(fn (Enrollment $e) => $e->student->name)
            ->values();

        $rows = $enrollments->map(fn (Enrollment $e) => [
            $e->student->code ?? ('EST-' . $e->student->id),
            $e->student->name,
            'Intento #' . $e->attempt_number,
            $e->accumulatedScore() . ' / 100',
            $e->final_score !== null ? (string) $e->final_score : 'Pendiente cierre',
            $e->statusLabel(),
        ])->all();

        $passed = $enrollments->where('status', Enrollment::STATUS_PASSED)->count();
        $failed = $enrollments->where('status', Enrollment::STATUS_FAILED)->count();

        return [
            'title' => 'Acta de Calificaciones de la Oferta',
            'subtitle' => "{$offering->subject->code} - {$offering->subject->name}",
            'filename' => 'calificaciones_' . strtolower($offering->subject->code) . '_' . $offering->id,
            'meta' => [
                'Materia' => "{$offering->subject->code} - {$offering->subject->name}",
                'Periodo' => "{$offering->semester->code} · {$offering->monthLabel()} · {$offering->shiftLabel()}",
                'Docente' => $offering->teacherLabel(),
                'Estado Oferta' => $offering->statusLabel(),
            ],
            'columns' => ['Código', 'Estudiante', 'Intento', 'Acumulado', 'Nota Final', 'Estado'],
            'rows' => $rows,
            'summary' => [
                'Total alumnos' => (string) $enrollments->count(),
                'Aprobados / Reprobados' => "{$passed} aprobada(s) / {$failed} reprobada(s)",
            ],
        ];
    }

    public function teacherOfferingsHistory(User $teacher): array
    {
        $offerings = Offering::where('teacher_id', $teacher->id)
            ->with(['subject', 'semester'])
            ->withCount('activeEnrollments')
            ->orderByDesc('semester_id')
            ->orderByDesc('month')
            ->get();

        $rows = $offerings->map(fn (Offering $o) => [
            $o->semester->code,
            $o->monthLabel(),
            $o->subject->code,
            $o->subject->name,
            $o->shiftLabel(),
            $o->classroomLabel(),
            "{$o->active_enrollments_count} / {$o->max_capacity}",
            $o->statusLabel(),
        ])->all();

        return [
            'title' => 'Historial de Materias Dictadas (Docente)',
            'subtitle' => $teacher->name,
            'filename' => 'historial_docente_' . ($teacher->code ?? $teacher->id),
            'meta' => [
                'Docente' => $teacher->name,
                'Facultad(es)' => $teacher->assignedFaculties()->pluck('name')->implode(', ') ?: '—',
            ],
            'columns' => ['Semestre', 'Mes', 'Código', 'Materia', 'Turno', 'Aula', 'Inscritos', 'Estado'],
            'rows' => $rows,
            'summary' => [
                'Total ofertas asignadas' => (string) $offerings->count(),
            ],
        ];
    }

    public function studentAcademicHistory(User $student): array
    {
        $enrollments = Enrollment::where('student_id', $student->id)
            ->with(['subject', 'semester', 'offering.teacher'])
            ->orderBy('semester_id')
            ->orderBy('attempt_number')
            ->get();

        $rows = $enrollments->map(fn (Enrollment $e) => [
            $e->semester->code,
            $e->offering->monthLabel(),
            $e->subject->code,
            $e->subject->name,
            '#' . $e->attempt_number,
            $e->final_score !== null ? (string) $e->final_score : ($e->status === Enrollment::STATUS_IN_PROGRESS ? $e->accumulatedScore() . ' (acum.)' : '—'),
            $e->statusLabel(),
            $e->closed_at?->format('d/m/Y') ?? '—',
        ])->all();

        $scores = $enrollments->where('status', '!=', Enrollment::STATUS_ANNULLED)->whereNotNull('final_score')->pluck('final_score');

        return [
            'title' => 'Récord e Historial Académico del Estudiante',
            'subtitle' => $student->name,
            'filename' => 'historial_estudiante_' . ($student->code ?? $student->id),
            'meta' => [
                'Estudiante' => $student->name . ($student->code ? " ({$student->code})" : ''),
                'Carrera' => $student->career->name ?? '—',
            ],
            'columns' => ['Semestre', 'Mes', 'Código', 'Materia', 'Intento', 'Nota', 'Estado', 'Cierre'],
            'rows' => $rows,
            'summary' => [
                'Intentos registrados' => (string) $enrollments->count(),
                'Promedio de notas finales' => $scores->isNotEmpty() ? number_format((float) $scores->avg(), 1) : 'N/A',
            ],
        ];
    }
}
