<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    /**
     * Colores visuales por prefijo de área (solo ayuda visual, no afecta la lógica).
     */
    public const AREA_COLORS = [
        'SIS' => ['label' => 'Sistemas', 'bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'border' => 'border-blue-300', 'hex' => '#2563EB'],
        'MAT' => ['label' => 'Matemáticas', 'bg' => 'bg-indigo-100', 'text' => 'text-indigo-800', 'border' => 'border-indigo-300', 'hex' => '#4F46E5'],
        'ELC' => ['label' => 'Electrónica', 'bg' => 'bg-cyan-100', 'text' => 'text-cyan-800', 'border' => 'border-cyan-300', 'hex' => '#0891B2'],
        'RED' => ['label' => 'Redes', 'bg' => 'bg-teal-100', 'text' => 'text-teal-800', 'border' => 'border-teal-300', 'hex' => '#0D9488'],
        'LIN' => ['label' => 'Idiomas', 'bg' => 'bg-violet-100', 'text' => 'text-violet-800', 'border' => 'border-violet-300', 'hex' => '#7C3AED'],
        'FIS' => ['label' => 'Física', 'bg' => 'bg-sky-100', 'text' => 'text-sky-800', 'border' => 'border-sky-300', 'hex' => '#0284C7'],
        'INV' => ['label' => 'Investigación', 'bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'border' => 'border-purple-300', 'hex' => '#9333EA'],
        'CPA' => ['label' => 'Contabilidad', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'border' => 'border-emerald-300', 'hex' => '#059669'],
        'ADM' => ['label' => 'Administración', 'bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'border' => 'border-amber-300', 'hex' => '#D97706'],
        'ELE' => ['label' => 'Electiva', 'bg' => 'bg-fuchsia-100', 'text' => 'text-fuchsia-800', 'border' => 'border-fuchsia-300', 'hex' => '#C026D3'],
        'GAS' => ['label' => 'Gastronomía', 'bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'border' => 'border-orange-300', 'hex' => '#EA580C'],
        'NUT' => ['label' => 'Nutrición', 'bg' => 'bg-lime-100', 'text' => 'text-lime-800', 'border' => 'border-lime-300', 'hex' => '#65A30D'],
        'TEL' => ['label' => 'Telecomunicaciones', 'bg' => 'bg-rose-100', 'text' => 'text-rose-800', 'border' => 'border-rose-300', 'hex' => '#E11D48'],
        'IND' => ['label' => 'Industrial', 'bg' => 'bg-slate-200', 'text' => 'text-slate-800', 'border' => 'border-slate-400', 'hex' => '#334155'],
        'ECO' => ['label' => 'Economía', 'bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-300', 'hex' => '#15803D'],
        'FIN' => ['label' => 'Finanzas', 'bg' => 'bg-teal-100', 'text' => 'text-teal-900', 'border' => 'border-teal-400', 'hex' => '#115E59'],
        'MKT' => ['label' => 'Marketing', 'bg' => 'bg-pink-100', 'text' => 'text-pink-800', 'border' => 'border-pink-300', 'hex' => '#BE185D'],
        'DER' => ['label' => 'Derecho', 'bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-300', 'hex' => '#B91C1C'],
        'PSI' => ['label' => 'Psicología', 'bg' => 'bg-purple-100', 'text' => 'text-purple-900', 'border' => 'border-purple-300', 'hex' => '#6B21A8'],
        'SOC' => ['label' => 'Sociales', 'bg' => 'bg-stone-200', 'text' => 'text-stone-800', 'border' => 'border-stone-400', 'hex' => '#57534E'],
        'FIL' => ['label' => 'Filosofía', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'border' => 'border-yellow-300', 'hex' => '#A16207'],
        'COM' => ['label' => 'Comunicación', 'bg' => 'bg-sky-100', 'text' => 'text-sky-900', 'border' => 'border-sky-300', 'hex' => '#0369A1'],
    ];

    protected $fillable = [
        'faculty_id',
        'code',
        'area_prefix',
        'name',
        'credits',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'credits' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function curriculumItems(): HasMany
    {
        return $this->hasMany(CurriculumItem::class);
    }

    public function offerings(): HasMany
    {
        return $this->hasMany(Offering::class);
    }

    public function areaStyle(): array
    {
        return self::AREA_COLORS[$this->area_prefix] ?? [
            'label' => $this->area_prefix,
            'bg' => 'bg-slate-100',
            'text' => 'text-slate-700',
            'border' => 'border-slate-300',
            'hex' => '#475569',
        ];
    }

    /**
     * Turnos válidos (intersección o unión habilitada de carreras que usan la materia).
     * Una oferta debe usar un turno habilitado en al menos las carreras que usan la materia.
     */
    public function allowedShifts(): array
    {
        $careers = $this->curriculumItems()->with('career')->get()->pluck('career')->filter()->unique('id');
        if ($careers->isEmpty()) {
            return array_keys(Career::SHIFTS);
        }

        $shifts = null;
        foreach ($careers as $career) {
            $cShifts = $career->shifts ?? [];
            $shifts = $shifts === null ? $cShifts : array_values(array_intersect($shifts, $cShifts));
        }

        return ! empty($shifts) ? $shifts : array_values(array_unique($careers->flatMap(fn ($c) => $c->shifts ?? [])->all()));
    }
}
