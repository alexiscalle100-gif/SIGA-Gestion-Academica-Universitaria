<?php

namespace App\Services;

use App\Models\Career;
use App\Models\CurriculumItem;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Collection;

class CurriculumProgressService
{
    /**
     * Devuelve el último intento vigente (no anulado) por materia del estudiante.
     *
     * @return Collection<int, Enrollment> keyed by subject_id
     */
    public function latestAttemptsBySubject(User $student): Collection
    {
        $enrollments = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('status', '!=', Enrollment::STATUS_ANNULLED)
            ->with(['offering.semester', 'offering.teacher', 'subject'])
            ->orderByDesc('attempt_number')
            ->orderByDesc('id')
            ->get();

        return $enrollments->unique('subject_id')->keyBy('subject_id');
    }

    /**
     * IDs de materias aprobadas por el estudiante.
     *
     * @return array<int>
     */
    public function approvedSubjectIds(User $student): array
    {
        return Enrollment::query()
            ->where('student_id', $student->id)
            ->where('status', Enrollment::STATUS_PASSED)
            ->pluck('subject_id')
            ->unique()
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * IDs de materias actualmente en curso.
     *
     * @return array<int>
     */
    public function inProgressSubjectIds(User $student): array
    {
        return Enrollment::query()
            ->where('student_id', $student->id)
            ->where('status', Enrollment::STATUS_IN_PROGRESS)
            ->pluck('subject_id')
            ->unique()
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * Verifica si el estudiante completó TODAS las materias obligatorias de un nivel N en una carrera.
     */
    public function hasCompletedLevel(User $student, Career $career, int $level, ?array $approvedIds = null): bool
    {
        $approved = $approvedIds ?? $this->approvedSubjectIds($student);

        $levelSubjectIds = CurriculumItem::query()
            ->where('career_id', $career->id)
            ->where('level', '<=', $level)
            ->pluck('subject_id')
            ->all();

        if (empty($levelSubjectIds)) {
            return true;
        }

        foreach ($levelSubjectIds as $sid) {
            if (! in_array((int) $sid, $approved, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Verifica si el estudiante cumple los prerrequisitos de un item del pensum.
     * Retorna null si cumple, o un mensaje descriptivo con lo que falta aprobar.
     */
    public function checkPrerequisites(User $student, Career $career, CurriculumItem $item, ?array $approvedIds = null): ?string
    {
        $approved = $approvedIds ?? $this->approvedSubjectIds($student);
        $item->loadMissing('prerequisites');

        // 1. Prerrequisito de nivel completo aprobado ("Nivel N completo aprobado")
        if ($item->requires_full_level !== null) {
            if (! $this->hasCompletedLevel($student, $career, $item->requires_full_level, $approved)) {
                return "Requiere tener aprobado completamente hasta el Nivel {$item->requires_full_level}.";
            }
        }

        // 2. Prerrequisitos por materias específicas
        $missing = [];
        foreach ($item->prerequisites as $prereq) {
            if (! in_array((int) $prereq->id, $approved, true)) {
                $missing[] = "{$prereq->code} ({$prereq->name})";
            }
        }

        if (! empty($missing)) {
            return 'Falta aprobar prerrequisito(s): ' . implode(', ', $missing) . '.';
        }

        return null;
    }

    /**
     * Construye el mapa completo de los 8 niveles (48 materias) del pensum de una carrera con el estado del estudiante.
     */
    public function buildCareerProgress(User $student, Career $career): array
    {
        $items = CurriculumItem::query()
            ->where('career_id', $career->id)
            ->with(['subject', 'prerequisites'])
            ->orderBy('level')
            ->orderBy('month_in_level')
            ->get();

        $latestBySubject = $this->latestAttemptsBySubject($student);
        $approvedIds = $this->approvedSubjectIds($student);

        $levels = [];
        $summary = [
            'total' => $items->count(),
            'aprobada' => 0,
            'reprobada' => 0,
            'en_curso' => 0,
            'no_cursada' => 0,
            'credits_approved' => 0,
            'credits_total' => 0,
        ];

        foreach ($items as $item) {
            $latest = $latestBySubject->get($item->subject_id);
            $status = $latest ? $latest->status : 'no_cursada';
            $summary[$status] = ($summary[$status] ?? 0) + 1;
            $summary['credits_total'] += $item->subject->credits;
            if ($status === Enrollment::STATUS_PASSED) {
                $summary['credits_approved'] += $item->subject->credits;
            }

            $prereqError = $status === Enrollment::STATUS_PASSED
                ? null
                : $this->checkPrerequisites($student, $career, $item, $approvedIds);

            $levels[$item->level][] = [
                'item' => $item,
                'subject' => $item->subject,
                'status' => $status,
                'latest_enrollment' => $latest,
                'prereq_missing' => $prereqError,
            ];
        }

        ksort($levels);

        $summary['percent'] = $summary['total'] > 0
            ? round(($summary['aprobada'] / $summary['total']) * 100, 1)
            : 0;

        return [
            'levels' => $levels,
            'summary' => $summary,
        ];
    }
}
