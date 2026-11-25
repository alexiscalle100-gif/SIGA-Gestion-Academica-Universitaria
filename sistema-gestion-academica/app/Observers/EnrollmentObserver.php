<?php

namespace App\Observers;

use App\Models\Enrollment;
use App\Services\NotificationService;

class EnrollmentObserver
{
    public function __construct(private readonly NotificationService $notifications) {}

    public function created(Enrollment $enrollment): void
    {
        $enrollment->loadMissing(['student', 'offering.subject', 'offering.teacher']);
        $student = $enrollment->student;
        $offering = $enrollment->offering;

        if (! $student || ! $offering || ! $offering->subject) {
            return;
        }

        $this->notifications->notify(
            $student,
            'Inscripción confirmada',
            "Te inscribiste en {$offering->subject->code} - {$offering->subject->name} (Intento #{$enrollment->attempt_number}, {$offering->monthLabel()} · turno {$offering->shiftLabel()}, {$offering->classroomLabel()}).",
            'inscripcion',
            route('student.enrollments.show', $enrollment)
        );

        if ($offering->teacher) {
            $this->notifications->notify(
                $offering->teacher,
                'Nuevo estudiante inscrito',
                "{$student->name} se inscribió en tu oferta de {$offering->subject->code} ({$offering->monthLabel()} · {$offering->shiftLabel()}).",
                'inscripcion',
                route('teacher.offerings.show', $offering)
            );
        }

        $this->notifications->logActivity(
            $student->id,
            'inscripcion_creada',
            "{$student->name} se inscribió en {$offering->subject->code} (Intento #{$enrollment->attempt_number}).",
            Enrollment::class,
            $enrollment->id
        );
    }
}
