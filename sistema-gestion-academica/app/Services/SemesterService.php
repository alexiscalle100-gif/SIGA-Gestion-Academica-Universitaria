<?php

namespace App\Services;

use App\Models\Semester;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SemesterService
{
    /**
     * Genera automáticamente los semestres fijos (Ene–Jun y Jul–Dic) para el año dado y el siguiente.
     */
    public function ensureSemestersForYear(?int $year = null): void
    {
        $baseYear = $year ?? now()->year;
        foreach ([$baseYear - 1, $baseYear, $baseYear + 1] as $y) {
            Semester::firstOrCreate(
                ['year' => $y, 'period' => 1],
                [
                    'code' => "{$y}-I",
                    'start_date' => "{$y}-01-01",
                    'end_date' => "{$y}-06-30",
                ]
            );

            Semester::firstOrCreate(
                ['year' => $y, 'period' => 2],
                [
                    'code' => "{$y}-II",
                    'start_date' => "{$y}-07-01",
                    'end_date' => "{$y}-12-31",
                ]
            );
        }
    }

    public function current(?Carbon $referenceDate = null): Semester
    {
        $date = $referenceDate ?? now();
        $this->ensureSemestersForYear($date->year);
        $period = $date->month <= 6 ? 1 : 2;

        return Semester::where('year', $date->year)
            ->where('period', $period)
            ->firstOrFail();
    }

    public function allOrdered(): Collection
    {
        $this->ensureSemestersForYear();

        return Semester::orderByDesc('year')->orderByDesc('period')->get();
    }
}
