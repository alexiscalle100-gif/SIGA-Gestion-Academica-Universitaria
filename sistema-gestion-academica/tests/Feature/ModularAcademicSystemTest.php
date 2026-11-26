<?php

namespace Tests\Feature;

use App\Models\Career;
use App\Models\CurriculumItem;
use App\Models\Enrollment;
use App\Models\Faculty;
use App\Models\GradeAdjustment;
use App\Models\Offering;
use App\Models\OfferingTask;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\Tuition;
use App\Models\User;
use App\Services\EnrollmentService;
use App\Services\OfferingService;
use App\Services\SemesterService;
use App\Services\TaskGradingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ModularAcademicSystemTest extends TestCase
{
    use RefreshDatabase;

    private Faculty $facIng;

    private Faculty $facGas;

    private Career $carSis;

    private Career $carGas;

    private Semester $semester;

    private User $admin;

    private User $docIng1;

    private User $docIng2;

    private User $docGas;

    private User $student;

    private Subject $mat101;

    private Subject $sis101;

    private Subject $sis102;

    private Subject $gas101;

    protected function setUp(): void
    {
        parent::setUp();

        $this->semester = app(SemesterService::class)->current();

        $this->facIng = Faculty::create(['code' => 'ING', 'name' => 'Facultad de Ingeniería']);
        $this->facGas = Faculty::create(['code' => 'GAS', 'name' => 'Facultad de Gastronomía']);

        $this->carSis = Career::create([
            'faculty_id' => $this->facIng->id,
            'code' => 'SIS',
            'name' => 'Ingeniería de Sistemas',
            'shifts' => ['manana', 'tarde', 'noche'],
            'is_active' => true,
        ]);

        $this->carGas = Career::create([
            'faculty_id' => $this->facGas->id,
            'code' => 'GAS',
            'name' => 'Gastronomía',
            'shifts' => ['manana', 'tarde'], // NO noche
            'is_active' => true,
        ]);

        $this->mat101 = Subject::create(['faculty_id' => $this->facIng->id, 'code' => 'MAT-101', 'area_prefix' => 'MAT', 'name' => 'Cálculo I']);
        $this->sis101 = Subject::create(['faculty_id' => $this->facIng->id, 'code' => 'SIS-101', 'area_prefix' => 'SIS', 'name' => 'Intro Programación']);
        $this->sis102 = Subject::create(['faculty_id' => $this->facIng->id, 'code' => 'SIS-102', 'area_prefix' => 'SIS', 'name' => 'POO']);
        $this->gas101 = Subject::create(['faculty_id' => $this->facGas->id, 'code' => 'GAS-101', 'area_prefix' => 'GAS', 'name' => 'Técnicas Culinarias']);

        CurriculumItem::create(['career_id' => $this->carSis->id, 'subject_id' => $this->mat101->id, 'level' => 1, 'month_in_level' => 1, 'order_in_curriculum' => 1]);
        CurriculumItem::create(['career_id' => $this->carSis->id, 'subject_id' => $this->sis101->id, 'level' => 1, 'month_in_level' => 2, 'order_in_curriculum' => 2]);
        $itemPoo = CurriculumItem::create(['career_id' => $this->carSis->id, 'subject_id' => $this->sis102->id, 'level' => 2, 'month_in_level' => 1, 'order_in_curriculum' => 7]);
        $itemPoo->prerequisites()->attach($this->sis101->id);

        CurriculumItem::create(['career_id' => $this->carGas->id, 'subject_id' => $this->gas101->id, 'level' => 1, 'month_in_level' => 1, 'order_in_curriculum' => 1]);

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'password' => Hash::make('Admin123*')]);
        $this->docIng1 = User::factory()->create(['role' => User::ROLE_TEACHER, 'faculty_id' => $this->facIng->id]);
        $this->docIng2 = User::factory()->create(['role' => User::ROLE_TEACHER, 'faculty_id' => $this->facIng->id]);
        $this->docGas = User::factory()->create(['role' => User::ROLE_TEACHER, 'faculty_id' => $this->facGas->id]);
        $this->student = User::factory()->create(['role' => User::ROLE_STUDENT, 'career_id' => $this->carSis->id]);
        $this->student->careers()->attach($this->carSis->id, ['is_primary' => true, 'is_active' => true]);
    }

    public function test_faculty_and_career_deactivate_instead_of_delete_when_data_exists(): void
    {
        $this->actingAs($this->admin)
            ->delete(route('admin.faculties.destroy', $this->facIng))
            ->assertRedirect();

        $this->assertDatabaseHas('faculties', ['id' => $this->facIng->id, 'is_active' => false]);

        $emptyFac = Faculty::create(['code' => 'VAC', 'name' => 'Facultad Vacía']);
        $this->actingAs($this->admin)
            ->delete(route('admin.faculties.destroy', $emptyFac))
            ->assertRedirect();

        $this->assertDatabaseMissing('faculties', ['id' => $emptyFac->id]);
    }

    public function test_offering_rules_faculty_teacher_classroom_and_shifts(): void
    {
        $service = app(OfferingService::class);
        $month = array_key_first($this->semester->months());
        $start = sprintf('%04d-%02d-01', $this->semester->year, $month);
        $end = sprintf('%04d-%02d-28', $this->semester->year, $month);

        // 1. Bloquear docente de otra facultad
        try {
            $service->createOffering([
                'subject_id' => $this->mat101->id,
                'semester_id' => $this->semester->id,
                'month' => $month,
                'start_date' => $start,
                'end_date' => $end,
                'shift' => 'manana',
                'max_capacity' => 50,
                'teacher_id' => $this->docGas->id,
                'classroom' => 101,
            ], $this->admin);
            $this->fail('Debió rechazar docente de otra facultad');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('teacher_id', $e->errors());
        }

        // 2. Bloquear turno no habilitado en Gastronomía (noche)
        try {
            $service->createOffering([
                'subject_id' => $this->gas101->id,
                'semester_id' => $this->semester->id,
                'month' => $month,
                'start_date' => $start,
                'end_date' => $end,
                'shift' => 'noche',
                'max_capacity' => 30,
                'teacher_id' => $this->docGas->id,
                'classroom' => 301,
            ], $this->admin);
            $this->fail('Debió rechazar turno noche para Gastronomía');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('shift', $e->errors());
        }

        // 3. Crear oferta válida en Aula 101 turno mañana
        $off1 = $service->createOffering([
            'subject_id' => $this->mat101->id,
            'semester_id' => $this->semester->id,
            'month' => $month,
            'start_date' => $start,
            'end_date' => $end,
            'shift' => 'manana',
            'max_capacity' => 50,
            'teacher_id' => $this->docIng1->id,
            'classroom' => 101,
        ], $this->admin);

        // 4. Bloquear choque de aula en mismo mes y turno
        try {
            $service->createOffering([
                'subject_id' => $this->sis101->id,
                'semester_id' => $this->semester->id,
                'month' => $month,
                'start_date' => $start,
                'end_date' => $end,
                'shift' => 'manana',
                'max_capacity' => 50,
                'teacher_id' => $this->docIng2->id,
                'classroom' => 101,
            ], $this->admin);
            $this->fail('Debió bloquear choque de aula mismo mes y turno');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('classroom', $e->errors());
        }

        // 5. Permitir misma aula 101 y mismo docente en turno DISTINTO (tarde)
        $off2 = $service->createOffering([
            'subject_id' => $this->sis101->id,
            'semester_id' => $this->semester->id,
            'month' => $month,
            'start_date' => $start,
            'end_date' => $end,
            'shift' => 'tarde',
            'max_capacity' => 50,
            'teacher_id' => $this->docIng1->id,
            'classroom' => 101,
        ], $this->admin);

        $this->assertNotNull($off1->id);
        $this->assertNotNull($off2->id);
    }

    public function test_student_enrollment_all_rules_and_priority_messages(): void
    {
        $enrollService = app(EnrollmentService::class);
        $month = now()->month;
        $today = now();

        // Prioridad 1: Sin matrícula activa -> mensaje exacto de Caja o Contabilidad
        $stateNoTuition = $enrollService->getStudentPortalState($this->student, $today);
        $this->assertSame('no_tuition', $stateNoTuition['state']);
        $this->assertSame(
            'Para habilitar tus materias, pasa por Caja o Contabilidad y realiza tu pago de matrícula.',
            $stateNoTuition['message']
        );

        // Activar matrícula
        Tuition::create([
            'student_id' => $this->student->id,
            'career_id' => $this->carSis->id,
            'semester_id' => $this->semester->id,
            'is_active' => true,
            'max_subjects' => 6,
        ]);

        // Prioridad 3: Sin ofertas disponibles
        $stateEmpty = $enrollService->getStudentPortalState($this->student, $today);
        $this->assertSame('no_offerings', $stateEmpty['state']);
        $this->assertSame('No hay materias ofertadas disponibles para ti este mes.', $stateEmpty['message']);

        // Crear ofertas
        $offMat = Offering::create([
            'subject_id' => $this->mat101->id,
            'semester_id' => $this->semester->id,
            'month' => $month,
            'start_date' => $today->format('Y-m-d'),
            'end_date' => $today->copy()->addDays(25)->format('Y-m-d'),
            'shift' => 'manana',
            'max_capacity' => 50,
            'teacher_id' => $this->docIng1->id,
            'classroom' => 101,
        ]);

        $offSisSameShift = Offering::create([
            'subject_id' => $this->sis101->id,
            'semester_id' => $this->semester->id,
            'month' => $month,
            'start_date' => $today->format('Y-m-d'),
            'end_date' => $today->copy()->addDays(25)->format('Y-m-d'),
            'shift' => 'manana',
            'max_capacity' => 50,
            'teacher_id' => $this->docIng2->id,
            'classroom' => 102,
        ]);

        $offSisDiffShift = Offering::create([
            'subject_id' => $this->sis101->id,
            'semester_id' => $this->semester->id,
            'month' => $month,
            'start_date' => $today->format('Y-m-d'),
            'end_date' => $today->copy()->addDays(25)->format('Y-m-d'),
            'shift' => 'tarde',
            'max_capacity' => 50,
            'teacher_id' => $this->docIng2->id,
            'classroom' => 102,
        ]);

        $offPoo = Offering::create([
            'subject_id' => $this->sis102->id,
            'semester_id' => $this->semester->id,
            'month' => $month,
            'start_date' => $today->format('Y-m-d'),
            'end_date' => $today->copy()->addDays(25)->format('Y-m-d'),
            'shift' => 'noche',
            'max_capacity' => 50,
            'teacher_id' => $this->docIng1->id,
            'classroom' => 103,
        ]);

        // Bloquear por prerrequisito (SIS-102 exige SIS-101)
        try {
            $enrollService->enroll($this->student, $offPoo, $today);
            $this->fail('Debió exigir prerrequisito SIS-101');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('SIS-101', $e->errors()['offering_id'][0]);
        }

        // Bloquear si pasaron más de 4 días desde el inicio
        try {
            $enrollService->enroll($this->student, $offMat, $today->copy()->addDays(5));
            $this->fail('Debió bloquear ventana > 4 días');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('offering_id', $e->errors());
        }

        // Inscribir en MAT-101 mañana
        $enr1 = $enrollService->enroll($this->student, $offMat, $today);
        $this->assertSame(1, $enr1->attempt_number);

        // Bloquear choque mismo mes y mismo turno (mañana)
        try {
            $enrollService->enroll($this->student, $offSisSameShift, $today);
            $this->fail('Debió bloquear choque mismo mes y turno');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('Choque de turno', $e->errors()['offering_id'][0]);
        }

        // Permitir 2da materia el mismo mes en TURNO DISTINTO (tarde)
        $enr2 = $enrollService->enroll($this->student, $offSisDiffShift, $today);
        $this->assertSame(Enrollment::STATUS_IN_PROGRESS, $enr2->status);

        // No se puede eliminar una oferta con inscritos; al deshabilitarla anula inscripciones y libera cupo
        $offService = app(OfferingService::class);
        try {
            $offService->deleteOffering($offMat, $this->admin);
            $this->fail('Debió bloquear eliminar oferta con inscritos');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('offering', $e->errors());
        }

        $offService->disableOffering($offMat, $this->admin);
        $this->assertSame(Enrollment::STATUS_ANNULLED, $enr1->fresh()->status);
    }

    public function test_teacher_tasks_grading_closing_ungraded_as_zero_and_audited_adjustment_and_admin_forbidden(): void
    {
        $grading = app(TaskGradingService::class);
        $today = now();

        $offering = Offering::create([
            'subject_id' => $this->mat101->id,
            'semester_id' => $this->semester->id,
            'month' => $today->month,
            'start_date' => $today->format('Y-m-d'),
            'end_date' => $today->copy()->addDays(25)->format('Y-m-d'),
            'shift' => 'manana',
            'max_capacity' => 50,
            'teacher_id' => $this->docIng1->id,
            'classroom' => 101,
        ]);

        $enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'offering_id' => $offering->id,
            'subject_id' => $this->mat101->id,
            'semester_id' => $this->semester->id,
            'attempt_number' => 1,
            'status' => Enrollment::STATUS_IN_PROGRESS,
        ]);

        // Admin NO puede acceder a aula/tareas del docente (HTTP 403)
        $this->actingAs($this->admin)
            ->get(route('teacher.offerings.show', $offering))
            ->assertStatus(403);

        // Otro docente NO puede ver ofertas ajenas (HTTP 403)
        $this->actingAs($this->docIng2)
            ->get(route('teacher.offerings.show', $offering))
            ->assertStatus(403);

        // Crear tareas: primero solo 60 pts -> cerrar debe fallar
        $t1 = $grading->createTask($offering, $this->docIng1, [
            'title' => 'Parcial',
            'max_points' => 60,
            'due_date' => $today->addDays(10)->format('Y-m-d'),
        ]);

        try {
            $grading->closeOffering($offering, $this->docIng1);
            $this->fail('Debió exigir suma exacta de 100 pts');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('100', $e->errors()['close'][0]);
        }

        // Completar 100 pts (t2 = 40 pts, quedará Sin calificar -> cuenta como 0 al cerrar)
        $grading->createTask($offering, $this->docIng1, [
            'title' => 'Final',
            'max_points' => 40,
            'due_date' => $today->addDays(20)->format('Y-m-d'),
        ]);

        $grading->gradeTask($t1, $enrollment, $this->docIng1, 48, 'Faltó un punto');
        $this->assertSame(1, $grading->countUngradedEntries($offering));

        // Cerrar materia: 48 + 0 (sin calificar) = 48 < 51 -> Reprobada y congelada
        $stats = $grading->closeOffering($offering, $this->docIng1);
        $this->assertSame(['passed' => 0, 'failed' => 1], $stats);

        $enrollment->refresh();
        $this->assertSame(48, $enrollment->final_score);
        $this->assertSame(Enrollment::STATUS_FAILED, $enrollment->status);
        $this->assertNotNull($enrollment->closed_at);

        // Corrección auditada de nota cerrada por el docente titular (48 -> 55 => Aprobada)
        $adj = $grading->adjustClosedGrade($enrollment, $this->docIng1, 55, 'Revisión de examen final');
        $this->assertInstanceOf(GradeAdjustment::class, $adj);
        $this->assertSame(48, $adj->previous_score);
        $this->assertSame(55, $adj->new_score);

        $enrollment->refresh();
        $this->assertSame(55, $enrollment->final_score);
        $this->assertSame(Enrollment::STATUS_PASSED, $enrollment->status);
    }

    public function test_dual_career_across_different_faculties_blocks_schedule_and_subject_conflicts_and_allows_other_shift(): void
    {
        $enrollService = app(EnrollmentService::class);
        $today = now();
        $month = $today->month;

        // Habilitar segunda carrera (Gastronomía en otra facultad) para el mismo estudiante
        $this->student->careers()->attach($this->carGas->id, ['is_primary' => false, 'is_active' => true]);

        Tuition::create(['student_id' => $this->student->id, 'career_id' => $this->carSis->id, 'semester_id' => $this->semester->id, 'is_active' => true, 'max_subjects' => 6]);
        Tuition::create(['student_id' => $this->student->id, 'career_id' => $this->carGas->id, 'semester_id' => $this->semester->id, 'is_active' => true, 'max_subjects' => 6]);

        // Oferta de Ingeniería en Mañana (07:30-11:30)
        $offIngManana = Offering::create([
            'subject_id' => $this->mat101->id, 'semester_id' => $this->semester->id, 'month' => $month,
            'start_date' => $today->format('Y-m-d'), 'end_date' => $today->copy()->addDays(25)->format('Y-m-d'),
            'shift' => 'manana', 'start_time' => '07:30', 'end_time' => '11:30',
            'max_capacity' => 50, 'teacher_id' => $this->docIng1->id, 'classroom' => 101,
        ]);

        // Oferta de Gastronomía (OTRA FACULTAD) en Mañana (07:30-11:30) -> DEBE CHOCAR!
        $offGasManana = Offering::create([
            'subject_id' => $this->gas101->id, 'semester_id' => $this->semester->id, 'month' => $month,
            'start_date' => $today->format('Y-m-d'), 'end_date' => $today->copy()->addDays(25)->format('Y-m-d'),
            'shift' => 'manana', 'start_time' => '07:30', 'end_time' => '11:30',
            'max_capacity' => 30, 'teacher_id' => $this->docGas->id, 'classroom' => 301,
        ]);

        // Oferta de Gastronomía en Tarde (14:00-18:00) -> NO CHOCA!
        $offGasTarde = Offering::create([
            'subject_id' => $this->gas101->id, 'semester_id' => $this->semester->id, 'month' => $month,
            'start_date' => $today->format('Y-m-d'), 'end_date' => $today->copy()->addDays(25)->format('Y-m-d'),
            'shift' => 'tarde', 'start_time' => '14:00', 'end_time' => '18:00',
            'max_capacity' => 30, 'teacher_id' => $this->docGas->id, 'classroom' => 301,
        ]);

        $enrollService->enroll($this->student, $offIngManana, $today, $this->carSis->id);

        try {
            $enrollService->enroll($this->student, $offGasManana, $today, $this->carGas->id);
            $this->fail('Debió bloquear choque de horario entre dos carreras de distintas facultades');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('segunda carrera', $e->errors()['offering_id'][0]);
        }

        $enrGas = $enrollService->enroll($this->student, $offGasTarde, $today, $this->carGas->id);
        $this->assertSame($this->carGas->id, $enrGas->career_id);
    }

    public function test_only_teacher_can_record_submission_status_comments_and_grades(): void
    {
        $today = now();
        $offering = Offering::create([
            'subject_id' => $this->mat101->id, 'semester_id' => $this->semester->id, 'month' => $today->month,
            'start_date' => $today->format('Y-m-d'), 'end_date' => $today->copy()->addDays(25)->format('Y-m-d'),
            'shift' => 'manana', 'start_time' => '07:30', 'end_time' => '11:30',
            'max_capacity' => 50, 'teacher_id' => $this->docIng1->id, 'classroom' => 101,
        ]);

        $enrollment = Enrollment::create([
            'student_id' => $this->student->id, 'career_id' => $this->carSis->id, 'offering_id' => $offering->id,
            'subject_id' => $this->mat101->id, 'semester_id' => $this->semester->id, 'attempt_number' => 1,
            'status' => Enrollment::STATUS_IN_PROGRESS,
        ]);

        $task = OfferingTask::create([
            'offering_id' => $offering->id, 'title' => 'Actividad 1', 'max_points' => 100,
            'due_date' => $today->copy()->addDays(10)->format('Y-m-d'), 'created_by' => $this->docIng1->id,
        ]);

        // Estudiante solo visualiza sus actividades (no puede marcar presentado ni enviar comentarios ni calificarse)
        $this->actingAs($this->student)
            ->get(route('student.enrollments.show', $enrollment))
            ->assertStatus(200)
            ->assertDontSee('Marcar Presentada');

        $this->actingAs($this->student)
            ->post(route('teacher.tasks.grade', [$task, $enrollment]), ['score' => 100, 'is_submitted' => 1])
            ->assertStatus(403);

        // Solo el docente titular marca presentada, comenta y anota la nota
        $this->actingAs($this->docIng1)
            ->post(route('teacher.tasks.grade', [$task, $enrollment]), [
                'is_submitted' => 1,
                'score' => 88,
                'comment' => 'Muy bien',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('task_grades', [
            'offering_task_id' => $task->id,
            'enrollment_id' => $enrollment->id,
            'score' => 88,
            'comment' => 'Muy bien',
        ]);
    }
}
