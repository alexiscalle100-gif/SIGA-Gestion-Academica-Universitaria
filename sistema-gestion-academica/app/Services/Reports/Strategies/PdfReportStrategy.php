<?php

namespace App\Services\Reports\Strategies;

use App\Services\Reports\Contracts\ReportExporterStrategy;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class PdfReportStrategy implements ReportExporterStrategy
{
    public function export(array $reportData): Response
    {
        $pdf = Pdf::loadView('reports.pdf-template', [
            'report' => $reportData,
            'generatedAt' => now()->format('d/m/Y H:i'),
        ])->setPaper('letter', count($reportData['columns']) > 6 ? 'landscape' : 'portrait');

        return $pdf->download($reportData['filename'].'.pdf');
    }
}
