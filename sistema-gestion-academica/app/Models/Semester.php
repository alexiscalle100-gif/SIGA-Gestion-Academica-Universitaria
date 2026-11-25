<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semester extends Model
{
    use HasFactory;

    public const MONTHS_BY_PERIOD = [
        1 => [1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio'],
        2 => [7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'],
    ];

    public const ALL_MONTHS = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
        7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
    ];

    protected $fillable = [
        'year',
        'period',
        'code',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'period' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function tuitions(): HasMany
    {
        return $this->hasMany(Tuition::class);
    }

    public function offerings(): HasMany
    {
        return $this->hasMany(Offering::class);
    }

    public function months(): array
    {
        return self::MONTHS_BY_PERIOD[$this->period] ?? [];
    }

    public function periodLabel(): string
    {
        return $this->period === 1 ? 'Enero – Junio' : 'Julio – Diciembre';
    }
}
