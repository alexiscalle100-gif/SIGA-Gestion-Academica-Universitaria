<?php

namespace App\Services\Reports;

use App\Services\Reports\Contracts\ReportExporterStrategy;
use App\Services\Reports\Strategies\ExcelReportStrategy;
use App\Services\Reports\Strategies\PdfReportStrategy;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contexto del Patrón Strategy para la exportación desacoplada de reportes (PDF / Excel).
 */
class ReportExportContext
{
    private ReportExporterStrategy $strategy;

    public function setStrategy(ReportExporterStrategy $strategy): self
    {
        $this->strategy = $strategy;

        return $this;
    }

    public function resolveFormat(string $format): self
    {
        $this->strategy = match (strtolower($format)) {
            'pdf' => new PdfReportStrategy(),
            'excel', 'xlsx' => new ExcelReportStrategy(),
            default => throw new InvalidArgumentException("Formato de reporte no soportado: {$format}"),
        };

        return $this;
    }

    public function export(array $reportData): Response
    {
        return $this->strategy->export($reportData);
    }
}
