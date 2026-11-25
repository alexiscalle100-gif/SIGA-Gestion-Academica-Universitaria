<?php

namespace Tests\Feature;

use App\Models\Career;
use App\Models\Faculty;
use App\Models\Offering;
use App\Models\Subject;
use App\Models\User;
use App\Repositories\Contracts\OfferingRepositoryInterface;
use App\Services\OfferingService;
use App\Services\Reports\ReportExportContext;
use App\Services\SemesterService;
use App\Support\AcademicConfigSingleton;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Suite de Pruebas de Calidad de Software con KPIs (ISO/IEC 25010) y Patrón Singleton.
 */
class SoftwareQualityKpiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * KPI-05: Verificación estricta del Patrón de Diseño Creacional Singleton y demás patrones (Repository, Strategy).
     */
    public function test_kpi_singleton_and_software_patterns_integrity(): void
    {
        $instanceA = AcademicConfigSingleton::getInstance();
        $instanceB = AcademicConfigSingleton::getInstance();
        $fromContainer = app(AcademicConfigSingleton::class);

        $this->assertSame($instanceA, $instanceB);
        $this->assertSame($instanceA, $fromContainer);
        $this->assertSame(51, $instanceA->passingScore());
        $this->assertSame(50, $instanceA->maxRoomCapacity());
        $this->assertSame(6, $instanceA->semesterSubjectQuota());
        $this->assertSame(4, $instanceA->enrollmentWindowDays());
        $this->assertArrayHasKey('manana', $instanceA->shiftSchedules());
        $this->assertArrayHasKey('tarde', $instanceA->shiftSchedules());
        $this->assertArrayHasKey('noche', $instanceA->shiftSchedules());

        // Protege contra deserialización (romper el Singleton)
        $this->expectException(\LogicException::class);
        $instanceA->__wakeup();
    }

    public function test_kpi_repository_and_strategy_patterns_resolve_cleanly(): void
    {
        $repo = app(OfferingRepositoryInterface::class);
        $this->assertNotNull($repo);

        $exporter = app(ReportExportContext::class);
        $sampleReport = [
            'title' => 'Reporte KPI',
            'subtitle' => 'Prueba automatizada',
            'filename' => 'kpi_test',
            'meta' => ['Sistema' => 'SIGA'],
            'columns' => ['KPI', 'Valor'],
            'rows' => [['Cumplimiento', '100%']],
            'summary' => ['Estado' => 'OK'],
        ];

        $excelResp = $exporter->resolveFormat('excel')->export($sampleReport);
        $this->assertSame(200, $excelResp->getStatusCode());
    }

    /**
     * KPI-01 y KPI-06: Ciclo de vida de Carrera (nace inhabilitada -> agregar materias -> aprobar -> deshabilitar -> reactivar)
     * y Oferta creada sin aula ni docente inicialmente -> asignados después.
     */
    public function test_kpi_career_approval_subject_addition_and_unassigned_offering_flow(): void
    {
        $fac = Faculty::create(['code' => 'ARQ', 'name' => 'Facultad de Arquitectura', 'is_active' => true]);
        $admin = User::create([
            'name' => 'Admin KPI',
            'email' => 'admin.kpi@upds.edu.bo',
            'password' => Hash::make('Admin123*'),
            'role' => User::ROLE_ADMIN,
            'code' => 'ADM-KPI1',
        ]);

        // 1. Nueva carrera inicia INHABILITADA hasta aprobación
        $this->actingAs($admin)->post(route('admin.careers.store'), [
            'faculty_id' => $fac->id,
            'code' => 'ARQ1',
            'name' => 'Arquitectura Digital',
            'shifts' => ['manana', 'tarde'],
        ])->assertRedirect();

        $career = Career::where('code', 'ARQ1')->firstOrFail();
        $this->assertFalse($career->is_active);

        // 2. Modificar carrera
        $this->actingAs($admin)->put(route('admin.careers.update', $career), [
            'faculty_id' => $fac->id,
            'code' => 'ARQ1',
            'name' => 'Arquitectura y Urbanismo',
            'shifts' => ['manana', 'tarde', 'noche'],
        ])->assertRedirect();
        $this->assertSame('Arquitectura y Urbanismo', $career->fresh()->name);

        // 3. Agregar materia nueva al pensum de la carrera recién creada
        $this->actingAs($admin)->post(route('admin.careers.subjects.store', $career), [
            'mode' => 'new',
            'new_code' => 'ARQ-101',
            'new_area_prefix' => 'ARQ',
            'new_name' => 'Dibujo Arquitectónico I',
            'new_credits' => 4,
            'level' => 1,
            'month_in_level' => 1,
        ])->assertRedirect();

        $this->assertSame(1, $career->curriculumItems()->count());

        // 4. Aprobar/Habilitar -> Deshabilitar -> Volver a Reactivar
        $this->actingAs($admin)->patch(route('admin.careers.toggle', $career))->assertRedirect();
        $this->assertTrue($career->fresh()->is_active);

        $this->actingAs($admin)->delete(route('admin.careers.destroy', $career))->assertRedirect();
        $this->assertFalse($career->fresh()->is_active);

        $this->actingAs($admin)->patch(route('admin.careers.toggle', $career))->assertRedirect();
        $this->assertTrue($career->fresh()->is_active);

        // 5. Habilitar oferta SIN aula ni docente inicialmente y luego asignarlos al editar
        $semester = app(SemesterService::class)->current();
        $month = array_key_first($semester->months());
        $subject = Subject::where('code', 'ARQ-101')->firstOrFail();

        $offering = app(OfferingService::class)->createOffering([
            'subject_id' => $subject->id,
            'semester_id' => $semester->id,
            'month' => $month,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(25)->format('Y-m-d'),
            'shift' => 'manana',
            'max_capacity' => 40,
            'teacher_id' => null,
            'classroom' => null,
        ], $admin);

        $this->assertNull($offering->teacher_id);
        $this->assertNull($offering->classroom);
        $this->assertSame('Docente por asignar', $offering->teacherLabel());
        $this->assertSame('Aula por asignar', $offering->classroomLabel());

        $teacher = User::create([
            'name' => 'Arq. Laura Soto',
            'email' => 'laura.soto@upds.edu.bo',
            'password' => Hash::make('Docente123*'),
            'role' => User::ROLE_TEACHER,
            'code' => 'DOC-KPI1',
            'faculty_id' => $fac->id,
        ]);
        $teacher->faculties()->attach($fac->id, ['is_primary' => true]);

        $updated = app(OfferingService::class)->updateOffering($offering, [
            'teacher_id' => $teacher->id,
            'classroom' => 305,
            'max_capacity' => 45,
        ], $admin);

        $this->assertSame($teacher->id, $updated->teacher_id);
        $this->assertSame(305, $updated->classroom);
    }

    /**
     * KPI-03 y KPI-04: Docente con múltiples facultades (con detección de choque entre facultades)
     * y seguridad RBAC estricta para datos personales del estudiante.
     */
    public function test_kpi_multi_faculty_teacher_and_student_rbac_restrictions(): void
    {
        $fac1 = Faculty::create(['code' => 'ING', 'name' => 'Facultad de Ingeniería', 'is_active' => true]);
        $fac2 = Faculty::create(['code' => 'EMP', 'name' => 'Facultad de Ciencias Empresariales', 'is_active' => true]);
        $car1 = Career::create(['faculty_id' => $fac1->id, 'code' => 'SIS', 'name' => 'Sistemas', 'shifts' => ['manana', 'tarde'], 'is_active' => true]);
        $car2 = Career::create(['faculty_id' => $fac2->id, 'code' => 'ADM', 'name' => 'Administración', 'shifts' => ['manana', 'tarde'], 'is_active' => true]);

        $sub1 = Subject::create(['faculty_id' => $fac1->id, 'code' => 'SIS-101', 'area_prefix' => 'SIS', 'name' => 'Prog I', 'credits' => 4]);
        $sub2 = Subject::create(['faculty_id' => $fac2->id, 'code' => 'ADM-101', 'area_prefix' => 'ADM', 'name' => 'Admin I', 'credits' => 4]);

        $admin = User::create(['name' => 'Admin', 'email' => 'adm2@upds.edu.bo', 'password' => Hash::make('Admin123*'), 'role' => User::ROLE_ADMIN, 'code' => 'ADM-2']);
        $teacher = User::create(['name' => 'Doc Multi', 'email' => 'docmulti@upds.edu.bo', 'password' => Hash::make('Docente123*'), 'role' => User::ROLE_TEACHER, 'code' => 'DOC-M1', 'faculty_id' => $fac1->id]);
        $student = User::create(['name' => 'Est Doble', 'email' => 'estdoble@upds.edu.bo', 'password' => Hash::make('Estudiante123*'), 'role' => User::ROLE_STUDENT, 'code' => 'EST-D1', 'career_id' => $car1->id]);
        $student->careers()->attach([
            $car1->id => ['is_primary' => true, 'is_active' => true],
            $car2->id => ['is_primary' => false, 'is_active' => true],
        ]);

        // Admin asigna AMBAS facultades al docente
        $this->actingAs($admin)->put(route('admin.users.faculties.sync', $teacher), [
            'faculty_ids' => [$fac1->id, $fac2->id],
        ])->assertRedirect();

        $this->assertTrue($teacher->fresh()->belongsToFaculty($fac1->id));
        $this->assertTrue($teacher->fresh()->belongsToFaculty($fac2->id));

        // Docente dicta en ING (Mañana) y en EMP (Tarde) -> PERMITIDO; EMP en Mañana -> BLOQUEADO por choque
        $sem = app(SemesterService::class)->current();
        $month = array_key_first($sem->months());
        $svc = app(OfferingService::class);

        $svc->createOffering([
            'subject_id' => $sub1->id, 'semester_id' => $sem->id, 'month' => $month,
            'start_date' => now()->format('Y-m-d'), 'end_date' => now()->addDays(20)->format('Y-m-d'),
            'shift' => 'manana', 'max_capacity' => 30, 'teacher_id' => $teacher->id, 'classroom' => 101,
        ], $admin);

        $off2 = $svc->createOffering([
            'subject_id' => $sub2->id, 'semester_id' => $sem->id, 'month' => $month,
            'start_date' => now()->format('Y-m-d'), 'end_date' => now()->addDays(20)->format('Y-m-d'),
            'shift' => 'tarde', 'max_capacity' => 30, 'teacher_id' => $teacher->id, 'classroom' => 201,
        ], $admin);
        $this->assertSame($teacher->id, $off2->teacher_id);

        try {
            $svc->createOffering([
                'subject_id' => $sub2->id, 'semester_id' => $sem->id, 'month' => $month,
                'start_date' => now()->format('Y-m-d'), 'end_date' => now()->addDays(20)->format('Y-m-d'),
                'shift' => 'manana', 'max_capacity' => 30, 'teacher_id' => $teacher->id, 'classroom' => 202,
            ], $admin);
            $this->fail('Debió bloquear choque de docente entre dos facultades en el mismo mes y turno.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('teacher_id', $e->errors());
        }

        // Seguridad RBAC: Estudiante NO puede modificar sus datos personales (HTTP 403)
        $this->actingAs($student)->put(route('profile.update'), [
            'name' => 'Hackeado',
            'email' => 'estdoble@upds.edu.bo',
        ])->assertStatus(403);

        // Administración SÍ puede actualizar los datos personales del estudiante
        $this->actingAs($admin)->put(route('admin.users.update', $student), [
            'name' => 'Estudiante Actualizado Admin',
            'email' => 'estdoble@upds.edu.bo',
            'phone' => '77777777',
        ])->assertRedirect();
        $this->assertSame('Estudiante Actualizado Admin', $student->fresh()->name);

        // Acceso sin errores al Dashboard e Inscripciones seleccionando la 2da carrera del estudiante (KPI-02 rendimiento)
        $t0 = microtime(true);
        $this->actingAs($admin)->get(route('admin.careers.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.careers.show', $car1))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.faculties.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.offerings.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.offerings.index', ['tab' => 'ofertas']))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.offerings.edit', $off2))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.tuitions.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.users.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.users.show', $student))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.users.show', $teacher))->assertStatus(200);
        $this->actingAs($teacher)->get(route('dashboard'))->assertStatus(200);
        $this->actingAs($teacher)->get(route('teacher.offerings.index'))->assertStatus(200);
        $this->actingAs($teacher)->get(route('teacher.offerings.show', $off2))->assertStatus(200);
        $this->actingAs($student)->get(route('dashboard', ['career_id' => $car2->id]))->assertStatus(200);
        $this->actingAs($student)->get(route('student.enrollments.index', ['career_id' => $car2->id]))->assertStatus(200);
        $this->actingAs($student)->get(route('student.progress', ['career_id' => $car2->id]))->assertStatus(200);
        $this->actingAs($student)->get(route('profile.show'))->assertStatus(200);
        $avgMs = ((microtime(true) - $t0) * 1000) / 17;

        $targetMs = AcademicConfigSingleton::getInstance()->kpiTargets()['max_http_response_ms'];
        $this->assertLessThan($targetMs, $avgMs, "El tiempo promedio de respuesta ({$avgMs} ms) superó el KPI de {$targetMs} ms.");
    }
}
