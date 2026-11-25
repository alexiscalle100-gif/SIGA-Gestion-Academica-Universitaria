<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Career;
use App\Models\CurriculumItem;
use App\Models\Enrollment;
use App\Models\Faculty;
use App\Models\Offering;
use App\Models\OfferingTask;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\TaskGrade;
use App\Models\Tuition;
use App\Models\User;
use App\Services\SemesterService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $semesterService = app(SemesterService::class);
        $currentSemester = $semesterService->current();
        $prevPeriod = $currentSemester->period === 1 ? 2 : 1;
        $prevYear = $currentSemester->period === 1 ? $currentSemester->year - 1 : $currentSemester->year;
        $semesterService->ensureSemestersForYear($prevYear);
        $prevSemester = Semester::where('year', $prevYear)->where('period', $prevPeriod)->firstOrFail();

        // 1. CUATRO FACULTADES
        $facIng = Faculty::create(['code' => 'ING', 'name' => 'Facultad de Ingeniería', 'description' => 'Ingeniería de Sistemas, Telecomunicaciones e Industrial.']);
        $facEmp = Faculty::create(['code' => 'EMP', 'name' => 'Facultad de Ciencias Empresariales', 'description' => 'Administración de Empresas y Contaduría Pública.']);
        $facJur = Faculty::create(['code' => 'JUR', 'name' => 'Facultad de Ciencias Jurídicas y Sociales', 'description' => 'Derecho y Psicología.']);
        $facGas = Faculty::create(['code' => 'GAS', 'name' => 'Facultad de Gastronomía', 'description' => 'Artes culinarias, nutrición y gestión gastronómica.']);

        // 2. OCHO CARRERAS PRE-APROBADAS (todas con 48 materias / 8 niveles × 6 meses)
        $carSis = Career::create(['faculty_id' => $facIng->id, 'code' => 'SIS', 'name' => 'Ingeniería de Sistemas', 'shifts' => ['manana', 'tarde', 'noche'], 'description' => 'Plan modular de 48 materias en desarrollo y arquitectura de software.', 'is_active' => true]);
        $carTel = Career::create(['faculty_id' => $facIng->id, 'code' => 'TEL', 'name' => 'Ingeniería de Telecomunicaciones', 'shifts' => ['manana', 'tarde', 'noche'], 'description' => 'Plan modular de 48 materias en redes y telecomunicaciones.', 'is_active' => true]);
        $carInd = Career::create(['faculty_id' => $facIng->id, 'code' => 'IND', 'name' => 'Ingeniería Industrial', 'shifts' => ['manana', 'tarde', 'noche'], 'description' => 'Plan modular de 48 materias en producción, calidad y logística.', 'is_active' => true]);

        $carAdm = Career::create(['faculty_id' => $facEmp->id, 'code' => 'ADM', 'name' => 'Administración de Empresas', 'shifts' => ['manana', 'tarde', 'noche'], 'description' => 'Plan modular de 48 materias en gestión estratégica y negocios.', 'is_active' => true]);
        $carCpa = Career::create(['faculty_id' => $facEmp->id, 'code' => 'CPA', 'name' => 'Contaduría Pública', 'shifts' => ['manana', 'tarde', 'noche'], 'description' => 'Plan modular de 48 materias en auditoría, tributación y finanzas.', 'is_active' => true]);

        $carDer = Career::create(['faculty_id' => $facJur->id, 'code' => 'DER', 'name' => 'Derecho', 'shifts' => ['manana', 'tarde', 'noche'], 'description' => 'Plan modular de 48 materias en ciencias jurídicas.', 'is_active' => true]);
        $carPsi = Career::create(['faculty_id' => $facJur->id, 'code' => 'PSI', 'name' => 'Psicología', 'shifts' => ['manana', 'tarde'], 'description' => 'Plan modular de 48 materias en psicología clínica y organizacional.', 'is_active' => true]);

        $carGas = Career::create(['faculty_id' => $facGas->id, 'code' => 'GAS', 'name' => 'Gastronomía', 'shifts' => ['manana', 'tarde'], 'description' => 'Plan modular de 48 materias en alta cocina y nutrición.', 'is_active' => true]);

        $subjects = [];
        $makeSubj = function (Faculty $fac, string $code, string $name, int $credits = 4) use (&$subjects): Subject {
            $prefix = explode('-', $code)[0];
            $s = Subject::firstOrCreate(
                ['code' => $code],
                ['faculty_id' => $fac->id, 'area_prefix' => $prefix, 'name' => $name, 'credits' => $credits, 'is_active' => true]
            );
            $subjects[$code] = $s;

            return $s;
        };

        // =================== PENSUMS FACULTAD DE INGENIERÍA (comparten Niveles 1-3!) ===================
        $sisPlan = [
            [1, 1, 'MAT-101', 'Cálculo I', []],
            [1, 2, 'SIS-101', 'Introducción a la Programación', []],
            [1, 3, 'LIN-101', 'Inglés Técnico I', []],
            [1, 4, 'FIS-101', 'Física General I', ['MAT-101']],
            [1, 5, 'INV-101', 'Metodología de la Investigación', []],
            [1, 6, 'ELC-101', 'Sistemas Digitales I', []],
            [2, 1, 'MAT-102', 'Álgebra Lineal', ['MAT-101']],
            [2, 2, 'SIS-102', 'Programación Orientada a Objetos', ['SIS-101']],
            [2, 3, 'LIN-102', 'Inglés Técnico II', ['LIN-101']],
            [2, 4, 'FIS-102', 'Física General II', ['FIS-101']],
            [2, 5, 'CPA-101', 'Contabilidad Básica', []],
            [2, 6, 'SIS-103', 'Estructuras Discretas', ['MAT-101']],
            [3, 1, 'MAT-201', 'Cálculo II', ['MAT-101']],
            [3, 2, 'SIS-201', 'Estructuras de Datos', ['SIS-102']],
            [3, 3, 'SIS-202', 'Base de Datos I', ['SIS-102']],
            [3, 4, 'ELC-201', 'Arquitectura de Computadoras', ['ELC-101']],
            [3, 5, 'ADM-101', 'Administración General', []],
            [3, 6, 'LIN-201', 'Inglés Técnico III', ['LIN-102']],
            [4, 1, 'MAT-202', 'Probabilidad y Estadística', ['MAT-201']],
            [4, 2, 'SIS-203', 'Base de Datos II', ['SIS-202']],
            [4, 3, 'SIS-204', 'Desarrollo Web I', ['SIS-201']],
            [4, 4, 'RED-201', 'Redes de Computadoras I', ['ELC-201']],
            [4, 5, 'SIS-205', 'Sistemas Operativos', ['ELC-201']],
            [4, 6, 'SIS-206', 'Análisis y Diseño de Sistemas', ['SIS-202']],
            [5, 1, 'SIS-301', 'Desarrollo Web II (Frameworks)', ['SIS-204', 'SIS-203']],
            [5, 2, 'SIS-302', 'Ingeniería de Software I', ['SIS-206']],
            [5, 3, 'RED-301', 'Redes de Computadoras II', ['RED-201']],
            [5, 4, 'SIS-303', 'Programación Móvil', ['SIS-204']],
            [5, 5, 'MAT-301', 'Investigación Operativa', ['MAT-202']],
            [5, 6, 'ADM-201', 'Deontología y Ética Profesional', []],
            [6, 1, 'SIS-304', 'Ingeniería de Software II', ['SIS-302']],
            [6, 2, 'SIS-305', 'Arquitectura de Software', ['SIS-301', 'SIS-302']],
            [6, 3, 'RED-302', 'Seguridad Informática', ['RED-301']],
            [6, 4, 'SIS-306', 'Inteligencia Artificial', ['MAT-202', 'SIS-201']],
            [6, 5, 'ADM-301', 'Emprendedurismo Tecnológico', ['ADM-101']],
            [6, 6, 'ELE-301', 'Electiva 1 (Ingeniería)', [], true, 1],
            [7, 1, 'SIS-401', 'Calidad y Pruebas de Software', ['SIS-304']],
            [7, 2, 'SIS-402', 'Sistemas Distribuidos y Cloud', ['SIS-305', 'RED-301']],
            [7, 3, 'SIS-403', 'Ciencia de Datos', ['SIS-306']],
            [7, 4, 'INV-301', 'Taller de Investigación Aplicada', ['INV-101']],
            [7, 5, 'ELE-401', 'Electiva 2 (Ingeniería)', [], true, 2],
            [7, 6, 'ELE-402', 'Electiva 3 (Ingeniería)', [], true, 3],
            [8, 1, 'SIS-404', 'Gestión de Proyectos TI', ['SIS-304', 'ADM-301']],
            [8, 2, 'SIS-405', 'Auditoría de Sistemas', ['RED-302']],
            [8, 3, 'SIS-406', 'DevOps y Despliegue Continuo', ['SIS-402']],
            [8, 4, 'ELE-403', 'Electiva 4 (Ingeniería)', [], true, 4],
            [8, 5, 'INV-401', 'Seminario de Grado I', ['INV-301'], false, null, 6],
            [8, 6, 'INV-402', 'Proyecto de Grado II', ['INV-401'], false, null, 7],
        ];
        $this->seedCareerCurriculum($carSis, $facIng, $sisPlan, $makeSubj);

        $telPlan = [
            [1, 1, 'MAT-101', 'Cálculo I', []],
            [1, 2, 'ELC-101', 'Sistemas Digitales I', []],
            [1, 3, 'LIN-101', 'Inglés Técnico I', []],
            [1, 4, 'FIS-101', 'Física General I', ['MAT-101']],
            [1, 5, 'INV-101', 'Metodología de la Investigación', []],
            [1, 6, 'SIS-101', 'Introducción a la Programación', []],
            [2, 1, 'MAT-102', 'Álgebra Lineal', ['MAT-101']],
            [2, 2, 'ELC-102', 'Circuitos Eléctricos I', ['FIS-101']],
            [2, 3, 'LIN-102', 'Inglés Técnico II', ['LIN-101']],
            [2, 4, 'FIS-102', 'Física General II', ['FIS-101']],
            [2, 5, 'CPA-101', 'Contabilidad Básica', []],
            [2, 6, 'TEL-101', 'Fundamentos de Telecomunicaciones', ['ELC-101']],
            [3, 1, 'MAT-201', 'Cálculo II', ['MAT-101']],
            [3, 2, 'ELC-202', 'Circuitos Eléctricos II', ['ELC-102']],
            [3, 3, 'ELC-203', 'Electrónica Analógica I', ['ELC-102']],
            [3, 4, 'ELC-201', 'Arquitectura de Computadoras', ['ELC-101']],
            [3, 5, 'ADM-101', 'Administración General', []],
            [3, 6, 'LIN-201', 'Inglés Técnico III', ['LIN-102']],
            [4, 1, 'MAT-202', 'Probabilidad y Estadística', ['MAT-201']],
            [4, 2, 'TEL-201', 'Teoría Electromagnética', ['FIS-102', 'MAT-201']],
            [4, 3, 'TEL-202', 'Señales y Sistemas', ['MAT-201']],
            [4, 4, 'RED-201', 'Redes de Computadoras I', ['ELC-201']],
            [4, 5, 'ELC-204', 'Microcontroladores', ['ELC-203']],
            [4, 6, 'TEL-203', 'Líneas de Transmisión', ['TEL-201']],
            [5, 1, 'TEL-301', 'Sistemas de Comunicaciones I', ['TEL-202']],
            [5, 2, 'TEL-302', 'Antenas y Propagación', ['TEL-203']],
            [5, 3, 'RED-301', 'Redes de Computadoras II', ['RED-201']],
            [5, 4, 'TEL-303', 'Comunicaciones Ópticas', ['TEL-203']],
            [5, 5, 'ELC-301', 'Procesamiento Digital de Señales', ['TEL-202']],
            [5, 6, 'ADM-201', 'Deontología y Ética Profesional', []],
            [6, 1, 'TEL-304', 'Sistemas de Comunicaciones II', ['TEL-301']],
            [6, 2, 'TEL-305', 'Comunicaciones Móviles y 5G', ['TEL-302']],
            [6, 3, 'RED-302', 'Seguridad Informática', ['RED-301']],
            [6, 4, 'TEL-306', 'Comunicaciones Satelitales', ['TEL-302']],
            [6, 5, 'ADM-301', 'Emprendedurismo Tecnológico', ['ADM-101']],
            [6, 6, 'ELE-301', 'Electiva 1 (Ingeniería)', [], true, 1],
            [7, 1, 'TEL-401', 'Redes de Banda Ancha', ['RED-301', 'TEL-303']],
            [7, 2, 'TEL-402', 'Telefonía IP y Multimedia', ['RED-301']],
            [7, 3, 'TEL-403', 'Internet de las Cosas (IoT)', ['ELC-204', 'RED-301']],
            [7, 4, 'INV-301', 'Taller de Investigación Aplicada', ['INV-101']],
            [7, 5, 'ELE-401', 'Electiva 2 (Ingeniería)', [], true, 2],
            [7, 6, 'ELE-402', 'Electiva 3 (Ingeniería)', [], true, 3],
            [8, 1, 'TEL-404', 'Regulación de Telecomunicaciones', ['TEL-305']],
            [8, 2, 'TEL-405', 'Planificación de Infraestructura', ['TEL-401']],
            [8, 3, 'TEL-406', 'Gestión y Monitoreo de Redes', ['TEL-405']],
            [8, 4, 'ELE-403', 'Electiva 4 (Ingeniería)', [], true, 4],
            [8, 5, 'INV-401', 'Seminario de Grado I', ['INV-301'], false, null, 6],
            [8, 6, 'INV-402', 'Proyecto de Grado II', ['INV-401'], false, null, 7],
        ];
        $this->seedCareerCurriculum($carTel, $facIng, $telPlan, $makeSubj);

        // Ingeniería Industrial (comparte MAT-101, MAT-102, MAT-201, FIS-101, FIS-102, LIN-101, LIN-102, INV-101, CPA-101, ADM-101 en niveles 1-3!)
        $indCommon = [
            [1, 1, 'MAT-101', 'Cálculo I', []],
            [1, 2, 'IND-101', 'Introducción a la Ingeniería Industrial', []],
            [1, 3, 'LIN-101', 'Inglés Técnico I', []],
            [1, 4, 'FIS-101', 'Física General I', ['MAT-101']],
            [1, 5, 'INV-101', 'Metodología de la Investigación', []],
            [1, 6, 'SIS-101', 'Introducción a la Programación', []],
            [2, 1, 'MAT-102', 'Álgebra Lineal', ['MAT-101']],
            [2, 2, 'IND-102', 'Dibujo Industrial CAD', []],
            [2, 3, 'LIN-102', 'Inglés Técnico II', ['LIN-101']],
            [2, 4, 'FIS-102', 'Física General II', ['FIS-101']],
            [2, 5, 'CPA-101', 'Contabilidad Básica', []],
            [2, 6, 'IND-103', 'Química Industrial', []],
            [3, 1, 'MAT-201', 'Cálculo II', ['MAT-101']],
            [3, 2, 'IND-201', 'Estudio del Trabajo y Métodos', ['IND-101']],
            [3, 3, 'IND-202', 'Termodinámica Aplicada', ['FIS-102']],
            [3, 4, 'IND-203', 'Ciencia de los Materiales', ['IND-103']],
            [3, 5, 'ADM-101', 'Administración General', []],
            [3, 6, 'LIN-201', 'Inglés Técnico III', ['LIN-102']],
        ];
        $this->seedCareerWithGeneratedUpper($carInd, $facIng, $indCommon, 'IND', 'Ingeniería Industrial', ['MAT-202', 'MAT-301', 'ADM-201', 'ADM-301', 'INV-301', 'ELE-301', 'ELE-401', 'ELE-402', 'ELE-403', 'INV-401', 'INV-402'], $makeSubj);

        // =================== PENSUMS FACULTAD DE CIENCIAS EMPRESARIALES (comparten Niveles 1-3!) ===================
        $empCommon = [
            [1, 1, 'ADM-E101', 'Administración General I', []],
            [1, 2, 'CPA-E101', 'Contabilidad Básica I', []],
            [1, 3, 'MAT-E101', 'Matemática Financiera I', []],
            [1, 4, 'ECO-E101', 'Principios de Economía', []],
            [1, 5, 'LIN-E101', 'Inglés Empresarial I', []],
            [1, 6, 'INV-E101', 'Metodología de la Investigación (EMP)', []],
            [2, 1, 'ADM-E102', 'Administración General II', ['ADM-E101']],
            [2, 2, 'CPA-E102', 'Contabilidad Intermedia', ['CPA-E101']],
            [2, 3, 'MAT-E102', 'Estadística Empresarial I', ['MAT-E101']],
            [2, 4, 'ECO-E102', 'Microeconomía', ['ECO-E101']],
            [2, 5, 'LIN-E102', 'Inglés Empresarial II', ['LIN-E101']],
            [2, 6, 'DER-E101', 'Derecho Comercial y Laboral', []],
            [3, 1, 'FIN-E201', 'Finanzas Corporativas I', ['CPA-E102']],
            [3, 2, 'MKT-E201', 'Fundamentos de Marketing', ['ADM-E102']],
            [3, 3, 'ECO-E201', 'Macroeconomía', ['ECO-E102']],
            [3, 4, 'CPA-E201', 'Contabilidad de Costos I', ['CPA-E102']],
            [3, 5, 'MAT-E201', 'Estadística Empresarial II', ['MAT-E102']],
            [3, 6, 'ADM-E201', 'Comportamiento Organizacional', ['ADM-E102']],
        ];
        $this->seedCareerWithGeneratedUpper($carAdm, $facEmp, $empCommon, 'ADM-E', 'Gestión Empresarial', [], $makeSubj, 'EMP');
        $this->seedCareerWithGeneratedUpper($carCpa, $facEmp, $empCommon, 'CPA-E', 'Contaduría y Auditoría', [], $makeSubj, 'EMP');

        // =================== PENSUMS FACULTAD DE CIENCIAS JURÍDICAS Y SOCIALES (comparten Niveles 1-3!) ===================
        $jurCommon = [
            [1, 1, 'DER-J101', 'Introducción al Derecho y Estado', []],
            [1, 2, 'SOC-J101', 'Sociología General', []],
            [1, 3, 'FIL-J101', 'Filosofía y Lógica Jurídica', []],
            [1, 4, 'COM-J101', 'Expresión Oral y Redacción', []],
            [1, 5, 'LIN-J101', 'Inglés Técnico Social I', []],
            [1, 6, 'INV-J101', 'Metodología de la Investigación (JUR)', []],
            [2, 1, 'DER-J102', 'Derecho Constitucional I', ['DER-J101']],
            [2, 2, 'SOC-J102', 'Antropología y Sociedad Boliviana', ['SOC-J101']],
            [2, 3, 'PSI-J101', 'Fundamentos de Psicología General', []],
            [2, 4, 'COM-J102', 'Argumentación y Oratoria', ['COM-J101']],
            [2, 5, 'LIN-J102', 'Inglés Técnico Social II', ['LIN-J101']],
            [2, 6, 'FIL-J102', 'Ética y Deontología Social', ['FIL-J101']],
            [3, 1, 'DER-J201', 'Derechos Humanos y Garantías', ['DER-J102']],
            [3, 2, 'SOC-J201', 'Resolución Alternativa de Conflictos', ['SOC-J102']],
            [3, 3, 'PSI-J201', 'Psicología Social y Comunitaria', ['PSI-J101']],
            [3, 4, 'INV-J201', 'Investigación Cualitativa', ['INV-J101']],
            [3, 5, 'COM-J201', 'Comunicación Institucional', ['COM-J102']],
            [3, 6, 'FIL-J201', 'Epistemología de Ciencias Sociales', ['FIL-J102']],
        ];
        $this->seedCareerWithGeneratedUpper($carDer, $facJur, $jurCommon, 'DER-J', 'Ciencias Jurídicas', [], $makeSubj, 'JUR');
        $this->seedCareerWithGeneratedUpper($carPsi, $facJur, $jurCommon, 'PSI-J', 'Psicología Aplicada', [], $makeSubj, 'JUR');

        // =================== PENSUM FACULTAD DE GASTRONOMÍA ===================
        $gasCommon = [
            [1, 1, 'GAS-101', 'Técnicas Culinarias Básicas I', []],
            [1, 2, 'NUT-101', 'Higiene e Inocuidad Alimentaria', []],
            [1, 3, 'MAT-G101', 'Matemática Aplicada a Gastronomía', []],
            [1, 4, 'LIN-G101', 'Inglés Gastronómico I', []],
            [1, 5, 'INV-G101', 'Metodología de la Investigación (GAS)', []],
            [1, 6, 'GAS-102', 'Historia de la Gastronomía', []],
            [2, 1, 'GAS-103', 'Técnicas Culinarias Básicas II', ['GAS-101']],
            [2, 2, 'NUT-102', 'Nutrición y Dietética I', ['NUT-101']],
            [2, 3, 'GAS-104', 'Panadería Básica', ['GAS-101']],
            [2, 4, 'LIN-G102', 'Inglés Gastronómico II', ['LIN-G101']],
            [2, 5, 'CPA-G101', 'Contabilidad Gastronómica', ['MAT-G101']],
            [2, 6, 'GAS-105', 'Gastronomía Boliviana Regional', ['GAS-102']],
            [3, 1, 'GAS-201', 'Cocina Caliente I', ['GAS-103']],
            [3, 2, 'GAS-202', 'Pastelería y Repostería I', ['GAS-104']],
            [3, 3, 'NUT-201', 'Química de los Alimentos', ['NUT-102']],
            [3, 4, 'GAS-203', 'Garde Manger y Cocina Fría', ['GAS-103']],
            [3, 5, 'ADM-G101', 'Administración de Restaurantes', []],
            [3, 6, 'GAS-204', 'Cocina Internacional I', ['GAS-105']],
        ];
        $this->seedCareerWithGeneratedUpper($carGas, $facGas, $gasCommon, 'GAS', 'Alta Cocina y Gestión', [], $makeSubj, 'GAS');

        // 3. USUARIOS (Admin, Docentes de las 4 Facultades, Estudiantes de múltiples carreras + 1 con Doble Carrera)
        $admin = User::create(['name' => 'Lic. Carlos Mendoza', 'email' => 'admin@upds.edu.bo', 'password' => Hash::make('Admin123*'), 'role' => User::ROLE_ADMIN, 'code' => 'ADM-001', 'document_number' => '4589120 SC', 'phone' => '70012345']);

        $docIng1 = User::create(['name' => 'Ph.D. Luigi Antequera Tamari', 'email' => 'docente@upds.edu.bo', 'password' => Hash::make('Docente123*'), 'role' => User::ROLE_TEACHER, 'code' => 'DOC-101', 'faculty_id' => $facIng->id, 'phone' => '71023456']);
        $docIng1->faculties()->attach([$facIng->id => ['is_primary' => true]]);

        // Docente multi-facultad (habilitada en Ingeniería y Ciencias Empresariales)
        $docIng2 = User::create(['name' => 'Ing. Valeria Rojas Quiroga', 'email' => 'valeria.rojas@upds.edu.bo', 'password' => Hash::make('Docente123*'), 'role' => User::ROLE_TEACHER, 'code' => 'DOC-102', 'faculty_id' => $facIng->id, 'phone' => '72034567']);
        $docIng2->faculties()->attach([
            $facIng->id => ['is_primary' => true],
            $facEmp->id => ['is_primary' => false],
        ]);

        $docEmp1 = User::create(['name' => 'Mgr. Ricardo Paredes Luna', 'email' => 'ricardo.paredes@upds.edu.bo', 'password' => Hash::make('Docente123*'), 'role' => User::ROLE_TEACHER, 'code' => 'DOC-201', 'faculty_id' => $facEmp->id, 'phone' => '72145678']);
        $docEmp1->faculties()->attach([$facEmp->id => ['is_primary' => true]]);

        $docJur1 = User::create(['name' => 'Dra. Mónica Arteaga Soliz', 'email' => 'monica.arteaga@upds.edu.bo', 'password' => Hash::make('Docente123*'), 'role' => User::ROLE_TEACHER, 'code' => 'DOC-301', 'faculty_id' => $facJur->id, 'phone' => '72256789']);
        $docJur1->faculties()->attach([$facJur->id => ['is_primary' => true]]);

        $docGas1 = User::create(['name' => 'Chef Fernando Salazar Paredes', 'email' => 'chef.salazar@upds.edu.bo', 'password' => Hash::make('Docente123*'), 'role' => User::ROLE_TEACHER, 'code' => 'DOC-401', 'faculty_id' => $facGas->id, 'phone' => '73045678']);
        $docGas1->faculties()->attach([$facGas->id => ['is_primary' => true]]);

        // Estudiante con DOBLE CARRERA EN DISTINTAS FACULTADES: Ingeniería de Sistemas (ING) + Administración de Empresas (EMP)!
        $estDual = User::create(['name' => 'Alexis Morales Vargas', 'email' => 'estudiante@upds.edu.bo', 'password' => Hash::make('Estudiante123*'), 'role' => User::ROLE_STUDENT, 'code' => 'EST-202601', 'career_id' => $carSis->id, 'phone' => '76011223']);
        $estDual->careers()->attach([
            $carSis->id => ['is_primary' => true, 'is_active' => true],
            $carAdm->id => ['is_primary' => false, 'is_active' => true],
        ]);

        $estTel = User::create(['name' => 'Camila Torrez Justiniano', 'email' => 'camila.torrez@upds.edu.bo', 'password' => Hash::make('Estudiante123*'), 'role' => User::ROLE_STUDENT, 'code' => 'EST-202602', 'career_id' => $carTel->id, 'phone' => '76122334']);
        $estTel->careers()->attach($carTel->id, ['is_primary' => true, 'is_active' => true]);

        $estInd = User::create(['name' => 'Diego Fernández Roca', 'email' => 'diego.fernandez@upds.edu.bo', 'password' => Hash::make('Estudiante123*'), 'role' => User::ROLE_STUDENT, 'code' => 'EST-202605', 'career_id' => $carInd->id, 'phone' => '76455667']);
        $estInd->careers()->attach($carInd->id, ['is_primary' => true, 'is_active' => true]);

        $estSinMatricula = User::create(['name' => 'Mateo Gutiérrez Flores', 'email' => 'mateo.gutierrez@upds.edu.bo', 'password' => Hash::make('Estudiante123*'), 'role' => User::ROLE_STUDENT, 'code' => 'EST-202603', 'career_id' => $carSis->id, 'phone' => '76233445']);
        $estSinMatricula->careers()->attach($carSis->id, ['is_primary' => true, 'is_active' => true]);

        $estGas = User::create(['name' => 'Sofía Zambrana Méndez', 'email' => 'sofia.zambrana@upds.edu.bo', 'password' => Hash::make('Estudiante123*'), 'role' => User::ROLE_STUDENT, 'code' => 'EST-202604', 'career_id' => $carGas->id, 'phone' => '76344556']);
        $estGas->careers()->attach($carGas->id, ['is_primary' => true, 'is_active' => true]);

        $estCpa = User::create(['name' => 'Lucía Chávez Vaca', 'email' => 'lucia.chavez@upds.edu.bo', 'password' => Hash::make('Estudiante123*'), 'role' => User::ROLE_STUDENT, 'code' => 'EST-202606', 'career_id' => $carCpa->id, 'phone' => '76566778']);
        $estCpa->careers()->attach($carCpa->id, ['is_primary' => true, 'is_active' => true]);

        $estDer = User::create(['name' => 'Sebastián Ortiz Paz', 'email' => 'sebastian.ortiz@upds.edu.bo', 'password' => Hash::make('Estudiante123*'), 'role' => User::ROLE_STUDENT, 'code' => 'EST-202607', 'career_id' => $carDer->id, 'phone' => '76677889']);
        $estDer->careers()->attach($carDer->id, ['is_primary' => true, 'is_active' => true]);

        // 4. MATRÍCULAS SEMESTRALES (incluye matrícula activa para AMBAS carreras de Alexis!)
        foreach ([
            [$estDual, $carSis, true],
            [$estDual, $carAdm, true], // 2da carrera en otra facultad activa!
            [$estTel, $carTel, true],
            [$estInd, $carInd, true],
            [$estSinMatricula, $carSis, false], // Inactiva para probar mensaje Caja/Contabilidad
            [$estGas, $carGas, true],
            [$estCpa, $carCpa, true],
            [$estDer, $carDer, true],
        ] as [$st, $car, $active]) {
            Tuition::create([
                'student_id' => $st->id,
                'career_id' => $car->id,
                'semester_id' => $currentSemester->id,
                'is_active' => $active,
                'max_subjects' => 6,
                'activated_by' => $active ? $admin->id : null,
                'activated_at' => $active ? now()->subDays(8) : null,
            ]);
        }

        // 5. HISTORIAL PREVIO (Nivel 1 para Alexis, Camila y Diego -> desbloquea MAT-102 y SIS-102!)
        $prevMonths = array_keys($prevSemester->months());
        foreach ([
            [$subjects['MAT-101'], $prevMonths[0], 'manana', '07:30', '11:30', 101, $docIng1, [[$estDual, $carSis, 84], [$estTel, $carTel, 78], [$estInd, $carInd, 81]]],
            [$subjects['SIS-101'], $prevMonths[1], 'manana', '07:30', '11:30', 102, $docIng1, [[$estDual, $carSis, 91], [$estTel, $carTel, 82], [$estInd, $carInd, 75]]],
            [$subjects['LIN-101'], $prevMonths[2], 'tarde', '14:00', '18:00', 103, $docIng2, [[$estDual, $carSis, 76], [$estTel, $carTel, 88]]],
            [$subjects['FIS-101'], $prevMonths[3], 'manana', '07:30', '11:30', 101, $docIng2, [[$estDual, $carSis, 44], [$estTel, $carTel, 65]]],
            [$subjects['INV-101'], $prevMonths[4], 'noche', '18:45', '22:15', 104, $docIng2, [[$estDual, $carSis, 85], [$estTel, $carTel, 90]]],
        ] as [$subj, $m, $sh, $stTime, $endTime, $room, $teacher, $enrList]) {
            $start = sprintf('%04d-%02d-01', $prevSemester->year, $m);
            $end = \Illuminate\Support\Carbon::parse($start)->endOfMonth()->format('Y-m-d');
            $off = Offering::create([
                'subject_id' => $subj->id,
                'semester_id' => $prevSemester->id,
                'month' => $m,
                'start_date' => $start,
                'end_date' => $end,
                'shift' => $sh,
                'start_time' => $stTime,
                'end_time' => $endTime,
                'max_capacity' => 50,
                'teacher_id' => $teacher->id,
                'classroom' => $room,
                'closed_at' => $end . ' 20:00:00',
                'closed_by' => $teacher->id,
            ]);
            foreach ($enrList as [$st, $car, $sc]) {
                Enrollment::create([
                    'student_id' => $st->id,
                    'career_id' => $car->id,
                    'offering_id' => $off->id,
                    'subject_id' => $subj->id,
                    'semester_id' => $prevSemester->id,
                    'attempt_number' => 1,
                    'final_score' => $sc,
                    'status' => $sc >= 51 ? Enrollment::STATUS_PASSED : Enrollment::STATUS_FAILED,
                    'enrolled_at' => $start . ' 09:00:00',
                    'closed_at' => $end . ' 20:00:00',
                ]);
            }
        }

        // 6. OFERTAS VIGENTES DEL MES ACTUAL CON HORARIOS CONCRETOS EN MAÑANA / TARDE / NOCHE
        $curMonth = now()->month;
        $todayStart = now()->format('Y-m-d');
        $monthEnd = now()->copy()->addDays(27)->format('Y-m-d');

        // Oferta ING #1: SIS-102 (POO) — Mañana 07:30-11:30 — Docente Luigi
        $offPoo = Offering::create([
            'subject_id' => $subjects['SIS-102']->id,
            'semester_id' => $currentSemester->id,
            'month' => $curMonth,
            'start_date' => now()->subDay()->format('Y-m-d'),
            'end_date' => $monthEnd,
            'shift' => 'manana',
            'start_time' => '07:30',
            'end_time' => '11:30',
            'days_label' => 'Lunes a Viernes',
            'max_capacity' => 50,
            'teacher_id' => $docIng1->id,
            'classroom' => 201,
        ]);

        // Oferta ING #2 (MATERIA COMPARTIDA NIVEL 2!): MAT-102 Álgebra Lineal — Tarde 14:00-18:00 — Docente Luigi
        // Compañeros de 3 carreras distintas (Sistemas, Telecomunicaciones e Industrial) en la misma aula!
        $offAlg = Offering::create([
            'subject_id' => $subjects['MAT-102']->id,
            'semester_id' => $currentSemester->id,
            'month' => $curMonth,
            'start_date' => $todayStart,
            'end_date' => $monthEnd,
            'shift' => 'tarde',
            'start_time' => '14:00',
            'end_time' => '18:00',
            'days_label' => 'Lunes a Viernes',
            'max_capacity' => 50,
            'teacher_id' => $docIng1->id,
            'classroom' => 201,
        ]);

        // Oferta ING #3: CPA-101 Contabilidad Básica — Noche 18:45-22:15 — Docente Valeria
        Offering::create([
            'subject_id' => $subjects['CPA-101']->id,
            'semester_id' => $currentSemester->id,
            'month' => $curMonth,
            'start_date' => $todayStart,
            'end_date' => $monthEnd,
            'shift' => 'noche',
            'start_time' => '18:45',
            'end_time' => '22:15',
            'days_label' => 'Lunes a Viernes',
            'max_capacity' => 45,
            'teacher_id' => $docIng2->id,
            'classroom' => 205,
        ]);

        // Oferta EMP #1 (OTRA FACULTAD, MISMO HORARIO MAÑANA -> Choca con SIS-102 de Alexis en Doble Carrera!)
        $offAdmManana = Offering::create([
            'subject_id' => $subjects['ADM-E101']->id,
            'semester_id' => $currentSemester->id,
            'month' => $curMonth,
            'start_date' => $todayStart,
            'end_date' => $monthEnd,
            'shift' => 'manana',
            'start_time' => '07:30',
            'end_time' => '11:30',
            'days_label' => 'Lunes a Viernes',
            'max_capacity' => 50,
            'teacher_id' => $docEmp1->id,
            'classroom' => 401,
        ]);

        // Oferta EMP #2 (OTRA FACULTAD, TURNO NOCHE -> SIN choque horario para Alexis en Doble Carrera!)
        Offering::create([
            'subject_id' => $subjects['ECO-E101']->id,
            'semester_id' => $currentSemester->id,
            'month' => $curMonth,
            'start_date' => $todayStart,
            'end_date' => $monthEnd,
            'shift' => 'noche',
            'start_time' => '18:45',
            'end_time' => '22:15',
            'days_label' => 'Lunes a Viernes',
            'max_capacity' => 50,
            'teacher_id' => $docEmp1->id,
            'classroom' => 402,
        ]);

        // Oferta JUR #1: DER-J101 (compartida por Derecho y Psicología en Nivel 1!)
        $offDer = Offering::create([
            'subject_id' => $subjects['DER-J101']->id,
            'semester_id' => $currentSemester->id,
            'month' => $curMonth,
            'start_date' => $todayStart,
            'end_date' => $monthEnd,
            'shift' => 'tarde',
            'start_time' => '14:00',
            'end_time' => '18:00',
            'days_label' => 'Lunes a Viernes',
            'max_capacity' => 50,
            'teacher_id' => $docJur1->id,
            'classroom' => 501,
        ]);

        // Oferta GAS #1: GAS-101
        $offGas = Offering::create([
            'subject_id' => $subjects['GAS-101']->id,
            'semester_id' => $currentSemester->id,
            'month' => $curMonth,
            'start_date' => $todayStart,
            'end_date' => $monthEnd,
            'shift' => 'manana',
            'start_time' => '07:30',
            'end_time' => '11:30',
            'days_label' => 'Lunes a Viernes',
            'max_capacity' => 30,
            'teacher_id' => $docGas1->id,
            'classroom' => 301,
        ]);

        // Inscripciones actuales
        $enrAlexisPoo = Enrollment::create(['student_id' => $estDual->id, 'career_id' => $carSis->id, 'offering_id' => $offPoo->id, 'subject_id' => $offPoo->subject_id, 'semester_id' => $currentSemester->id, 'attempt_number' => 1, 'status' => Enrollment::STATUS_IN_PROGRESS, 'enrolled_at' => now()->subDay()]);

        // En MAT-102 (Tarde 14:00-18:00), compañeros de 2 carreras distintas ya inscritos (Camila TEL + Diego IND) — y Alexis (SIS) también puede inscribirse!
        $enrCamilaAlg = Enrollment::create(['student_id' => $estTel->id, 'career_id' => $carTel->id, 'offering_id' => $offAlg->id, 'subject_id' => $offAlg->subject_id, 'semester_id' => $currentSemester->id, 'attempt_number' => 1, 'status' => Enrollment::STATUS_IN_PROGRESS, 'enrolled_at' => now()]);
        $enrDiegoAlg = Enrollment::create(['student_id' => $estInd->id, 'career_id' => $carInd->id, 'offering_id' => $offAlg->id, 'subject_id' => $offAlg->subject_id, 'semester_id' => $currentSemester->id, 'attempt_number' => 1, 'status' => Enrollment::STATUS_IN_PROGRESS, 'enrolled_at' => now()]);

        Enrollment::create(['student_id' => $estCpa->id, 'career_id' => $carCpa->id, 'offering_id' => $offAdmManana->id, 'subject_id' => $offAdmManana->subject_id, 'semester_id' => $currentSemester->id, 'attempt_number' => 1, 'status' => Enrollment::STATUS_IN_PROGRESS, 'enrolled_at' => now()]);
        Enrollment::create(['student_id' => $estDer->id, 'career_id' => $carDer->id, 'offering_id' => $offDer->id, 'subject_id' => $offDer->subject_id, 'semester_id' => $currentSemester->id, 'attempt_number' => 1, 'status' => Enrollment::STATUS_IN_PROGRESS, 'enrolled_at' => now()]);
        Enrollment::create(['student_id' => $estGas->id, 'career_id' => $carGas->id, 'offering_id' => $offGas->id, 'subject_id' => $offGas->subject_id, 'semester_id' => $currentSemester->id, 'attempt_number' => 1, 'status' => Enrollment::STATUS_IN_PROGRESS, 'enrolled_at' => now()]);

        // Tareas de SIS-102 (100 pts) — mostrando actividades YA PRESENTADAS por el estudiante listas para que el docente anote la nota!
        $t1 = OfferingTask::create(['offering_id' => $offPoo->id, 'title' => 'Práctica 1: Clases y Encapsulamiento', 'description' => 'Modelado de clases en PHP.', 'max_points' => 30, 'due_date' => now()->addDays(7)->format('Y-m-d'), 'created_by' => $docIng1->id]);
        $t2 = OfferingTask::create(['offering_id' => $offPoo->id, 'title' => 'Evaluación Parcial: Polimorfismo', 'description' => 'Aplicación de interfaces y SOLID.', 'max_points' => 30, 'due_date' => now()->addDays(15)->format('Y-m-d'), 'created_by' => $docIng1->id]);
        $t3 = OfferingTask::create(['offering_id' => $offPoo->id, 'title' => 'Proyecto Integrador de Módulo', 'description' => 'Entrega práctica final.', 'max_points' => 40, 'due_date' => $monthEnd, 'created_by' => $docIng1->id]);

        TaskGrade::create(['offering_task_id' => $t1->id, 'enrollment_id' => $enrAlexisPoo->id, 'submitted_at' => now()->subHours(18), 'submission_note' => 'Práctica entregada en clase', 'score' => 27, 'comment' => 'Excelente estructura orientada a objetos.', 'graded_by' => $docIng1->id, 'graded_at' => now()->subHours(10)]);
        TaskGrade::create(['offering_task_id' => $t2->id, 'enrollment_id' => $enrAlexisPoo->id, 'submitted_at' => now()->subHours(8), 'submission_note' => 'Examen parcial presentado', 'score' => 26, 'comment' => 'Buen uso de interfaces.', 'graded_by' => $docIng1->id, 'graded_at' => now()->subHours(4)]);
        // t3: PRESENTADA por el estudiante pero SIN CALIFICAR todavía (lista para que el docente anote la nota!)
        TaskGrade::create(['offering_task_id' => $t3->id, 'enrollment_id' => $enrAlexisPoo->id, 'submitted_at' => now()->subHour(), 'submission_note' => 'Proyecto final presentado hoy', 'score' => null, 'comment' => null]);

        // Tareas de MAT-102 (compartida por Telecomunicaciones e Industrial)
        $m1 = OfferingTask::create(['offering_id' => $offAlg->id, 'title' => 'Guía de Matrices y Determinantes', 'max_points' => 40, 'due_date' => now()->addDays(10)->format('Y-m-d'), 'created_by' => $docIng1->id]);
        OfferingTask::create(['offering_id' => $offAlg->id, 'title' => 'Evaluación Final de Espacios Vectoriales', 'max_points' => 60, 'due_date' => $monthEnd, 'created_by' => $docIng1->id]);
        TaskGrade::create(['offering_task_id' => $m1->id, 'enrollment_id' => $enrCamilaAlg->id, 'submitted_at' => now()->subHours(3), 'submission_note' => 'Guía resuelta', 'score' => 36, 'comment' => 'Correcto', 'graded_by' => $docIng1->id, 'graded_at' => now()->subHour()]);
        TaskGrade::create(['offering_task_id' => $m1->id, 'enrollment_id' => $enrDiegoAlg->id, 'submitted_at' => now()->subHours(2), 'submission_note' => 'Entregado en aula', 'score' => null]);

        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'sistema_inicializado',
            'description' => "Sistema modular configurado con 4 facultades, 8 carreras (48 materias c/u), horarios por turno y soporte de doble carrera.",
        ]);
    }

    private function seedCareerWithGeneratedUpper(
        Career $career,
        Faculty $faculty,
        array $lowerLevels,
        string $prefix,
        string $areaTitle,
        array $sharedUpperCodes,
        callable $makeSubj,
        ?string $facTag = null
    ): void {
        $plan = $lowerLevels;
        $seq = 1;
        for ($lvl = 4; $lvl <= 8; $lvl++) {
            for ($m = 1; $m <= 6; $m++) {
                if ($lvl === 8 && $m === 5) {
                    $code = $facTag ? "INV-{$facTag}401" : 'INV-401';
                    $plan[] = [$lvl, $m, $code, "Seminario de Grado I ({$career->code})", [], false, null, 6];
                } elseif ($lvl === 8 && $m === 6) {
                    $code = $facTag ? "INV-{$facTag}402" : 'INV-402';
                    $plan[] = [$lvl, $m, $code, "Proyecto de Grado II ({$career->code})", [$facTag ? "INV-{$facTag}401" : 'INV-401'], false, null, 7];
                } elseif (($lvl === 6 && $m === 6) || ($lvl === 7 && $m >= 5) || ($lvl === 8 && $m === 4)) {
                    $elNum = match ("{$lvl}-{$m}") {
                        '6-6' => 1, '7-5' => 2, '7-6' => 3, default => 4,
                    };
                    $eCode = $facTag ? "ELE-{$facTag}{$lvl}0{$elNum}" : "ELE-{$lvl}0{$elNum}";
                    $plan[] = [$lvl, $m, $eCode, "Electiva {$elNum} ({$faculty->code})", [], true, $elNum];
                } else {
                    $cleanPrefix = explode('-', $prefix)[0];
                    $subCode = "{$cleanPrefix}-{$career->code}{$lvl}0{$m}";
                    $plan[] = [$lvl, $m, $subCode, "{$areaTitle} {$seq} — Nivel {$lvl}", []];
                    $seq++;
                }
            }
        }

        $this->seedCareerCurriculum($career, $faculty, $plan, $makeSubj);
    }

    private function seedCareerCurriculum(Career $career, Faculty $faculty, array $plan, callable $makeSubj): void
    {
        $itemsByCode = [];
        $order = 1;

        foreach ($plan as $row) {
            $subject = $makeSubj($faculty, $row[2], $row[3], 4);
            $item = CurriculumItem::create([
                'career_id' => $career->id,
                'subject_id' => $subject->id,
                'level' => $row[0],
                'month_in_level' => $row[1],
                'order_in_curriculum' => $order++,
                'is_elective' => $row[5] ?? false,
                'elective_number' => $row[6] ?? null,
                'requires_full_level' => $row[7] ?? null,
            ]);
            $itemsByCode[$row[2]] = [$item, $row[4]];
        }

        foreach ($itemsByCode as [$item, $prereqCodes]) {
            foreach ($prereqCodes as $pCode) {
                $prereqSubject = Subject::where('code', $pCode)->first();
                if ($prereqSubject) {
                    $item->prerequisites()->syncWithoutDetaching([$prereqSubject->id]);
                }
            }
        }
    }
}
