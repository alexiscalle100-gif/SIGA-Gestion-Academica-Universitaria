<?php

namespace App\Support;

use LogicException;

/**
 * Patrón de Diseño Creacional: Singleton.
 * Centraliza en una única instancia en memoria los parámetros académicos institucionales
 * y los umbrales de KPIs de calidad ISO/IEC 25010 del sistema SIGA.
 */
final class AcademicConfigSingleton
{
    private static ?self $instance = null;

    /** @var array<string, mixed> */
    private array $settings;

    private function __construct()
    {
        $this->settings = [
            'institution_name' => 'Universidad Privada Domingo Savio',
            'system_name' => 'SIGA Académico Modular',
            'passing_score' => 51,
            'max_score' => 100,
            'max_room_capacity' => 50,
            'semester_subject_quota' => 6,
            'enrollment_window_days' => 4,
            'max_careers_per_student' => 2,
            'shift_schedules' => [
                'manana' => ['start' => '07:30', 'end' => '11:30', 'label' => 'Mañana (07:30 - 11:30)'],
                'tarde' => ['start' => '14:00', 'end' => '18:00', 'label' => 'Tarde (14:00 - 18:00)'],
                'noche' => ['start' => '18:45', 'end' => '22:15', 'label' => 'Noche (18:45 - 22:15)'],
            ],
            'kpi_targets' => [
                'functional_pass_rate_pct' => 95.0,
                'code_coverage_min_pct' => 80.0,
                'max_failed_tests' => 0,
                'invalid_input_rejection_pct' => 100.0,
                'max_http_response_ms' => 500.0,
                'rbac_security_compliance_pct' => 100.0,
                'conflict_prevention_accuracy_pct' => 100.0,
                'iso_25010_min_score' => 4.5,
            ],
        ];
    }

    private function __clone()
    {
    }

    public function __wakeup(): void
    {
        throw new LogicException('No se permite deserializar una instancia Singleton de AcademicConfigSingleton.');
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->settings[$key] ?? $default;
    }

    public function passingScore(): int
    {
        return (int) $this->settings['passing_score'];
    }

    public function maxRoomCapacity(): int
    {
        return (int) $this->settings['max_room_capacity'];
    }

    public function semesterSubjectQuota(): int
    {
        return (int) $this->settings['semester_subject_quota'];
    }

    public function enrollmentWindowDays(): int
    {
        return (int) $this->settings['enrollment_window_days'];
    }

    public function maxCareersPerStudent(): int
    {
        return (int) $this->settings['max_careers_per_student'];
    }

    /** @return array<string, array{start: string, end: string, label: string}> */
    public function shiftSchedules(): array
    {
        return $this->settings['shift_schedules'];
    }

    /** @return array<string, float|int> */
    public function kpiTargets(): array
    {
        return $this->settings['kpi_targets'];
    }

    /**
     * Calcula los 4 KPIs de calidad de software (ISO/IEC 25010) y evalúa su criterio de aceptación:
     * 1. Tasa de pruebas aprobadas >= 95%
     * 2. Cobertura de líneas/módulos >= 80%
     * 3. Pruebas fallidas == 0
     * 4. Validación de entradas inválidas == 100%
     *
     * @return array<string, array{valor: float|int, criterio: string, cumple: bool, iso_25010: string}>
     */
    public function calculateQualityKpis(
        int $executedTests,
        int $passedTests,
        int $failedTests,
        int $coveredUnits,
        int $executableUnits,
        int $rejectedInvalidInputs,
        int $testedInvalidInputs
    ): array {
        $passRate = $executedTests > 0 ? round(($passedTests / $executedTests) * 100, 2) : 0.0;
        $coverageRate = $executableUnits > 0 ? round(($coveredUnits / $executableUnits) * 100, 2) : 0.0;
        $invalidRejectionRate = $testedInvalidInputs > 0 ? round(($rejectedInvalidInputs / $testedInvalidInputs) * 100, 2) : 0.0;

        $targets = $this->kpiTargets();

        return [
            'tasa_pruebas_aprobadas' => [
                'valor' => $passRate,
                'criterio' => '≥ ' . $targets['functional_pass_rate_pct'] . ' %',
                'cumple' => $passRate >= $targets['functional_pass_rate_pct'],
                'iso_25010' => 'Adecuación Funcional / Fiabilidad',
            ],
            'cobertura_codigo' => [
                'valor' => $coverageRate,
                'criterio' => '≥ ' . $targets['code_coverage_min_pct'] . ' %',
                'cumple' => $coverageRate >= $targets['code_coverage_min_pct'],
                'iso_25010' => 'Mantenibilidad (Capacidad de prueba)',
            ],
            'pruebas_fallidas' => [
                'valor' => $failedTests,
                'criterio' => '= ' . $targets['max_failed_tests'],
                'cumple' => $failedTests === (int) $targets['max_failed_tests'],
                'iso_25010' => 'Fiabilidad (Madurez)',
            ],
            'validacion_entradas_invalidas' => [
                'valor' => $invalidRejectionRate,
                'criterio' => '= ' . $targets['invalid_input_rejection_pct'] . ' %',
                'cumple' => $invalidRejectionRate >= $targets['invalid_input_rejection_pct'],
                'iso_25010' => 'Adecuación Funcional / Seguridad',
            ],
        ];
    }
}
