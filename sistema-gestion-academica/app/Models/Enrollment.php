<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    use HasFactory;

    public const PASSING_SCORE = 51;

    public const STATUS_IN_PROGRESS = 'en_curso';
    public const STATUS_PASSED = 'aprobada';
    public const STATUS_FAILED = 'reprobada';
    public const STATUS_ANNULLED = 'anulada';

    protected $fillable = [
        'student_id',
        'career_id',
        'offering_id',
        'subject_id',
        'semester_id',
        'attempt_number',
        'final_score',
        'status',
        'enrolled_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'attempt_number' => 'integer',
            'final_score' => 'integer',
            'enrolled_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
    }

    public function offering(): BelongsTo
    {
        return $this->belongsTo(Offering::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function taskGrades(): HasMany
    {
        return $this->hasMany(TaskGrade::class);
    }

    public function gradeAdjustments(): HasMany
    {
        return $this->hasMany(GradeAdjustment::class)->latest();
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PASSED => 'Aprobada',
            self::STATUS_FAILED => 'Reprobada',
            self::STATUS_ANNULLED => 'Anulada',
            default => 'En curso',
        };
    }

    /**
     * Suma entera acumulada de tareas calificadas hasta el momento (0..100).
     */
    public function accumulatedScore(): int
    {
        if ($this->closed_at !== null && $this->final_score !== null) {
            return (int) $this->final_score;
        }

        return (int) $this->taskGrades->whereNotNull('score')->sum('score');
    }
}
