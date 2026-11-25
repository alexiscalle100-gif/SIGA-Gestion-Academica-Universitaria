<?php

namespace App\Services;

use App\Models\Career;
use App\Models\CurriculumItem;
use App\Models\Enrollment;
use App\Models\Offering;
use App\Models\Tuition;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EnrollmentService
{
    public function __construct(
        private readonly SemesterService $semesters,
        private readonly CurriculumProgressService $progress,
    ) {}

    /**
     * Calcula el estado del portal de inscripción para la carrera seleccionada del estudiante.
     * Soporta Doble Carrera (incluso en distintas facultades), verificando choques de horario y
     * choques de materia contra TODAS las carreras que estudia el alumno en el mismo mes.
     */
    public function getStudentPortalState(User $student, ?Carbon $referenceDate = null, ?int $careerId = null): array
    {
        $today = ($referenceDate ?? now())->copy();
        $semester = $this->semesters->current($today);

        $studentCareers = $student->activeCareers();
        $selectedCareer = $careerId
            ? ($studentCareers->firstWhere('id', $careerId) ?? $studentCareers->first())
            : $studentCareers->first();

        $tuition = $selectedCareer
            ? Tuition::query()
                ->where('student_id', $student->id)
                ->where('career_id', $selectedCareer->id)
                ->where('semester_id', $semester->id)
                ->with('career.faculty')
                ->first()
            : null;

        // Cupo de 6 materias para esta carrera en el semestre actual
        $careerSemesterUsedSlots = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('semester_id', $semester->id)
            ->when($selectedCareer, fn ($q) => $q->where(function ($inner) use ($selectedCareer) {
                $inner->where('career_id', $selectedCareer->id)->orWhereNull('career_id');
            }))
            ->where('status', '!=', Enrollment::STATUS_ANNULLED)
            ->count();

        if (! $selectedCareer || ! $tuition || ! $tuition->is_active) {
            return [
                'state' => 'no_tuition',
                'semester' => $semester,
                'careers' => $studentCareers,
                'selected_career' => $selectedCareer,
                'tuition' => $tuition,
                'used_slots' => $careerSemesterUsedSlots,
                'max_slots' => Tuition::DEFAULT_MAX_SUBJECTS,
                'message' => 'Para habilitar tus materias, pasa por Caja o Contabilidad y realiza tu pago de matrícula.',
                'offerings' => collect(),
            ];
        }

        if ($careerSemesterUsedSlots >= $tuition->max_subjects) {
            return [
                'state' => 'quota_reached',
                'semester' => $semester,
                'careers' => $studentCareers,
                'selected_career' => $selectedCareer,
                'tuition' => $tuition,
                'used_slots' => $careerSemesterUsedSlots,
                'max_slots' => $tuition->max_subjects,
                'message' => null,
                'offerings' => collect(),
            ];
        }

        $career = $tuition->career;
        $curriculumBySubject = CurriculumItem::query()
            ->where('career_id', $career->id)
            ->with('prerequisites')
            ->get()
            ->keyBy('subject_id');

        $approvedIds = $this->progress->approvedSubjectIds($student);
        $inProgressIds = $this->progress->inProgressSubjectIds($student);

        // TODAS las inscripciones activas del estudiante en el semestre (de CUALQUIER carrera/facultad) para detectar choques cruzados!
        $allSemesterEnrollments = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('semester_id', $semester->id)
            ->where('status', '!=', Enrollment::STATUS_ANNULLED)
            ->with(['offering.subject.faculty', 'career.faculty'])
            ->get();

        $candidateOfferings = Offering::query()
            ->where('semester_id', $semester->id)
            ->where('is_disabled', false)
            ->whereNull('closed_at')
            ->whereIn('subject_id', $curriculumBySubject->keys())
            ->with(['subject.faculty', 'teacher', 'semester'])
            ->withCount('activeEnrollments')
            ->orderBy('month')
            ->orderBy('start_date')
            ->orderBy('shift')
            ->get()
            ->filter(function (Offering $offering) use ($today, $approvedIds, $inProgressIds, $curriculumBySubject, $student, $career) {
                if (! $offering->isWithinEnrollmentWindow($today)) {
                    return false;
                }
                if (in_array((int) $offering->subject_id, $approvedIds, true)) {
                    return false;
                }
                if (in_array((int) $offering->subject_id, $inProgressIds, true)) {
                    return false;
                }
                $currItem = $curriculumBySubject->get($offering->subject_id);
                if (! $currItem) {
                    return false;
                }

                return $this->progress->checkPrerequisites($student, $career, $currItem, $approvedIds) === null;
            })
            ->map(function (Offering $offering) use ($allSemesterEnrollments, $curriculumBySubject, $career) {
                $conflictMsg = $this->detectCrossCareerConflict($allSemesterEnrollments, $offering, $career);
                $isFull = $offering->availableSlots() <= 0;
                $currItem = $curriculumBySubject->get($offering->subject_id);

                return [
                    'offering' => $offering,
                    'curriculum_item' => $currItem,
                    'is_full' => $isFull,
                    'has_shift_conflict' => $conflictMsg !== null,
                    'conflict_message' => $conflictMsg,
                    'can_enroll' => ! $isFull && $conflictMsg === null,
                ];
            })
            ->values();

        if ($candidateOfferings->isEmpty()) {
            return [
                'state' => 'no_offerings',
                'semester' => $semester,
                'careers' => $studentCareers,
                'selected_career' => $selectedCareer,
                'tuition' => $tuition,
                'used_slots' => $careerSemesterUsedSlots,
                'max_slots' => $tuition->max_subjects,
                'message' => 'No hay materias ofertadas disponibles para ti este mes.',
                'offerings' => collect(),
            ];
        }

        return [
            'state' => 'ready',
            'semester' => $semester,
            'careers' => $studentCareers,
            'selected_career' => $selectedCareer,
            'tuition' => $tuition,
            'used_slots' => $careerSemesterUsedSlots,
            'max_slots' => $tuition->max_subjects,
            'message' => null,
            'offerings' => $candidateOfferings,
        ];
    }

    /**
     * Inscribe manualmente a un estudiante validando todas las reglas + choques cruzados entre doble carrera/facultades.
     */
    public function enroll(User $student, Offering $offering, ?Carbon $referenceDate = null, ?int $careerId = null): Enrollment
    {
        if (! $student->isStudent()) {
            throw ValidationException::withMessages([
                'offering_id' => 'Solo los usuarios con rol Estudiante pueden realizar inscripciones.',
            ]);
        }

        $today = ($referenceDate ?? now())->copy();
        $semester = $this->semesters->current($today);
        $offering->loadMissing(['subject.faculty', 'semester', 'teacher']);

        // Resolver la carrera destino del estudiante (soporta 1 o 2 carreras)
        $studentCareers = $student->activeCareers();
        $targetCareer = $careerId
            ? $studentCareers->firstWhere('id', $careerId)
            : $studentCareers->first(fn (Career $c) => CurriculumItem::where('career_id', $c->id)->where('subject_id', $offering->subject_id)->exists())
                ?? $studentCareers->first();

        // Regla 1: Tiene matrícula activa en el semestre actual para esa carrera
        $tuition = $targetCareer
            ? Tuition::query()
                ->where('student_id', $student->id)
                ->where('career_id', $targetCareer->id)
                ->where('semester_id', $semester->id)
                ->where('is_active', true)
                ->with('career.faculty')
                ->first()
            : null;

        if (! $tuition) {
            throw ValidationException::withMessages([
                'offering_id' => 'Para habilitar tus materias, pasa por Caja o Contabilidad y realiza tu pago de matrícula.',
            ]);
        }

        if ((int) $offering->semester_id !== (int) $semester->id) {
            throw ValidationException::withMessages([
                'offering_id' => 'La oferta seleccionada no corresponde al semestre actual.',
            ]);
        }

        // Regla 2: La materia está en el pensum de la carrera de su matrícula
        $curriculumItem = CurriculumItem::query()
            ->where('career_id', $tuition->career_id)
            ->where('subject_id', $offering->subject_id)
            ->with('prerequisites')
            ->first();

        if (! $curriculumItem) {
            throw ValidationException::withMessages([
                'offering_id' => "La materia {$offering->subject->code} no pertenece al pensum de {$tuition->career->name}.",
            ]);
        }

        // Regla 3: Prerrequisitos aprobados
        if ($prereqError = $this->progress->checkPrerequisites($student, $tuition->career, $curriculumItem)) {
            throw ValidationException::withMessages(['offering_id' => $prereqError]);
        }

        // Regla 4: La materia no está ya aprobada ni en curso
        $alreadyPassedOrActive = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('subject_id', $offering->subject_id)
            ->whereIn('status', [Enrollment::STATUS_PASSED, Enrollment::STATUS_IN_PROGRESS])
            ->exists();

        if ($alreadyPassedOrActive) {
            throw ValidationException::withMessages([
                'offering_id' => "La materia {$offering->subject->code} ya se encuentra aprobada o en curso.",
            ]);
        }

        // Regla 5: Oferta habilitada y dentro de ventana de 4 días desde el inicio
        if ($offering->is_disabled || $offering->isManuallyClosed() || ! $offering->isWithinEnrollmentWindow($today)) {
            throw ValidationException::withMessages([
                'offering_id' => 'La ventana de inscripción para esta oferta está cerrada (solo hasta 4 días después de la fecha de inicio).',
            ]);
        }

        // Regla 6: Control de choque de horario/turno y choque de materia incluso entre DOBLE CARRERA / DISTINTAS FACULTADES
        $allSemesterEnrollments = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('semester_id', $semester->id)
            ->where('status', '!=', Enrollment::STATUS_ANNULLED)
            ->with(['offering.subject.faculty', 'career.faculty'])
            ->get();

        if ($conflictMsg = $this->detectCrossCareerConflict($allSemesterEnrollments, $offering, $tuition->career)) {
            throw ValidationException::withMessages(['offering_id' => $conflictMsg]);
        }

        // Regla 7: Menos de 6 inscripciones (salvo anuladas) en el semestre para esa carrera
        $careerSemesterCount = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('semester_id', $semester->id)
            ->where(function ($q) use ($tuition) {
                $q->where('career_id', $tuition->career_id)->orWhereNull('career_id');
            })
            ->where('status', '!=', Enrollment::STATUS_ANNULLED)
            ->count();

        if ($careerSemesterCount >= $tuition->max_subjects) {
            throw ValidationException::withMessages([
                'offering_id' => "Has alcanzado el límite máximo de {$tuition->max_subjects} materias permitidas para {$tuition->career->name} en el semestre {$semester->code}.",
            ]);
        }

        // Regla 8: Lugares disponibles con bloqueo transaccional
        return DB::transaction(function () use ($student, $offering, $semester, $tuition) {
            $lockedOffering = Offering::where('id', $offering->id)->lockForUpdate()->firstOrFail();
            $currentCount = $lockedOffering->activeEnrollments()->count();

            if ($currentCount >= $lockedOffering->max_capacity) {
                throw ValidationException::withMessages([
                    'offering_id' => 'Esta oferta no cuenta con cupos disponibles.',
                ]);
            }

            $previousAttempts = Enrollment::query()
                ->where('student_id', $student->id)
                ->where('subject_id', $lockedOffering->subject_id)
                ->where('status', '!=', Enrollment::STATUS_ANNULLED)
                ->max('attempt_number') ?? 0;

            return Enrollment::create([
                'student_id' => $student->id,
                'career_id' => $tuition->career_id,
                'offering_id' => $lockedOffering->id,
                'subject_id' => $lockedOffering->subject_id,
                'semester_id' => $semester->id,
                'attempt_number' => $previousAttempts + 1,
                'final_score' => null,
                'status' => Enrollment::STATUS_IN_PROGRESS,
                'enrolled_at' => now(),
            ]);
        });
    }

    /**
     * Detecta choque de horario (turno o franja horaria exacta) o choque de materia equivalente
     * en el mismo mes, abarcando TODAS las carreras/facultades del estudiante (doble carrera).
     */
    public function detectCrossCareerConflict($semesterEnrollments, Offering $targetOffering, Career $targetCareer): ?string
    {
        $targetBaseName = $this->normalizeSubjectName($targetOffering->subject->name);

        foreach ($semesterEnrollments as $enr) {
            $existingOff = $enr->offering;
            if (! $existingOff || (int) $existingOff->month !== (int) $targetOffering->month) {
                continue;
            }

            $otherCareerName = $enr->career?->name ?? 'tu carrera actual';
            $otherFacultyName = $enr->career?->faculty?->name ?? ($existingOff->subject->faculty->name ?? '');
            $isOtherCareer = $enr->career_id && (int) $enr->career_id !== (int) $targetCareer->id;

            // 1. Choque de horario (mismo turno o franja horaria solapada en el mismo mes)
            if (
                $existingOff->shift === $targetOffering->shift
                || $existingOff->overlapsTimeWith($targetOffering->resolvedStartTime(), $targetOffering->resolvedEndTime())
            ) {
                if ($isOtherCareer) {
                    return sprintf(
                        'Choque de horario con tu segunda carrera "%s" (%s): ya cursas %s - %s en %s en horario %s.',
                        $otherCareerName,
                        $otherFacultyName,
                        $existingOff->subject->code,
                        $existingOff->subject->name,
                        $targetOffering->monthLabel(),
                        $existingOff->scheduleText()
                    );
                }

                return sprintf(
                    'Choque de turno/horario: ya estás inscrito en %s - %s en %s en horario %s. Puedes elegir otro turno.',
                    $existingOff->subject->code,
                    $existingOff->subject->name,
                    $targetOffering->monthLabel(),
                    $existingOff->scheduleText()
                );
            }

            // 2. Choque de materia equivalente entre facultades en el mismo mes (doble carrera)
            if ($isOtherCareer && $this->normalizeSubjectName($existingOff->subject->name) === $targetBaseName) {
                return sprintf(
                    'Choque de materia entre facultades: ya estás cursando "%s" (%s) en %s (%s) durante este mismo mes.',
                    $existingOff->subject->name,
                    $existingOff->subject->code,
                    $otherCareerName,
                    $otherFacultyName
                );
            }
        }

        return null;
    }

    private function normalizeSubjectName(string $name): string
    {
        $clean = preg_replace('/\s*\([^)]*\)\s*/', '', mb_strtolower(trim($name)));

        return trim((string) $clean);
    }
}
