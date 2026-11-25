<?php

namespace App\Services\Reports\Contracts;

use Symfony\Component\HttpFoundation\Response;

interface ReportExporterStrategy
{
    /**
     * @param  array{
     *   title: string,
     *   subtitle: string,
     *   filename: string,
     *   meta: array<string, string>,
     *   columns: array<int, string>,
     *   rows: array<int, array<int, string|int|float|null>>,
     *   summary: array<string, string>
     * }  $reportData
     */
    public function export(array $reportData): Response;
}
