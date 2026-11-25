<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Career extends Model
{
    use HasFactory;

    public const SHIFTS = [
        'manana' => 'Mañana',
        'tarde' => 'Tarde',
        'noche' => 'Noche',
    ];

    /**
     * Horarios oficiales predeterminados por turno (Lunes a Viernes).
     */
    public const SHIFT_SCHEDULES = [
        'manana' => ['start' => '07:30', 'end' => '11:30', 'min' => '07:00', 'max' => '12:30', 'label' => 'Mañana (07:30 - 11:30)'],
        'tarde' => ['start' => '14:00', 'end' => '18:00', 'min' => '13:00', 'max' => '18:30', 'label' => 'Tarde (14:00 - 18:00)'],
        'noche' => ['start' => '18:45', 'end' => '22:15', 'min' => '18:30', 'max' => '22:30', 'label' => 'Noche (18:45 - 22:15)'],
    ];

    protected $fillable = [
        'faculty_id',
        'code',
        'name',
        'shifts',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'shifts' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function curriculumItems(): HasMany
    {
        return $this->hasMany(CurriculumItem::class)->orderBy('level')->orderBy('month_in_level');
    }

    public function students(): HasMany
    {
        return $this->hasMany(User::class)->where('role', User::ROLE_STUDENT);
    }

    public function tuitions(): HasMany
    {
        return $this->hasMany(Tuition::class);
    }

    public function hasShift(string $shift): bool
    {
        return in_array($shift, $this->shifts ?? [], true);
    }

    public function shiftLabels(): array
    {
        return array_map(fn($s) => self::SHIFTS[$s] ?? ucfirst($s), $this->shifts ?? []);
    }

    public function hasAssociatedData(): bool
    {
        return $this->curriculumItems()->exists()
            || $this->students()->exists()
            || $this->tuitions()->exists();
    }
}
