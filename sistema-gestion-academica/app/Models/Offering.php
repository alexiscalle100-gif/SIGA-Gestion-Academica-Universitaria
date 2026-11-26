<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Offering extends Model
{
    use HasFactory;

    public const MAX_ROOM_CAPACITY = 50;

    public const ENROLLMENT_WINDOW_DAYS = 4;

    public const STATUS_ENABLED = 'habilitada';

    public const STATUS_IN_PROGRESS = 'en_curso';

    public const STATUS_CLOSED = 'cerrada';

    public const STATUS_DISABLED = 'deshabilitada';

    protected $fillable = [
        'subject_id',
        'semester_id',
        'month',
        'start_date',
        'end_date',
        'shift',
        'start_time',
        'end_time',
        'days_label',
        'max_capacity',
        'teacher_id',
        'classroom',
        'is_disabled',
        'closed_at',
        'closed_by',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'max_capacity' => 'integer',
            'classroom' => 'integer',
            'is_disabled' => 'boolean',
            'closed_at' => 'datetime',
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function activeEnrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class)->where('status', '!=', Enrollment::STATUS_ANNULLED);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(OfferingTask::class)->orderBy('due_date')->orderBy('id');
    }

    public function computedStatus(?Carbon $referenceDate = null): string
    {
        if ($this->is_disabled) {
            return self::STATUS_DISABLED;
        }

        if ($this->closed_at !== null) {
            return self::STATUS_CLOSED;
        }

        $today = ($referenceDate ?? now())->copy()->startOfDay();
        if ($today->gt($this->end_date)) {
            return self::STATUS_CLOSED;
        }
        if ($today->gte($this->start_date)) {
            return self::STATUS_IN_PROGRESS;
        }

        return self::STATUS_ENABLED;
    }

    public function statusLabel(?Carbon $referenceDate = null): string
    {
        return match ($this->computedStatus($referenceDate)) {
            self::STATUS_ENABLED => 'Habilitada',
            self::STATUS_IN_PROGRESS => 'En curso',
            self::STATUS_CLOSED => 'Cerrada',
            self::STATUS_DISABLED => 'Deshabilitada',
            default => 'Desconocido',
        };
    }

    public function shiftLabel(): string
    {
        return Career::SHIFTS[$this->shift] ?? ucfirst($this->shift);
    }

    public function teacherLabel(): string
    {
        return $this->teacher?->name ?? 'Docente por asignar';
    }

    public function classroomLabel(): string
    {
        return $this->classroom ? ('Aula '.$this->classroom) : 'Aula por asignar';
    }

    public function scheduleText(): string
    {
        $start = substr((string) ($this->start_time ?: (Career::SHIFT_SCHEDULES[$this->shift]['start'] ?? '07:30')), 0, 5);
        $end = substr((string) ($this->end_time ?: (Career::SHIFT_SCHEDULES[$this->shift]['end'] ?? '11:30')), 0, 5);
        $days = $this->days_label ?: 'Lunes a Viernes';

        return "{$this->shiftLabel()} ({$start} - {$end} · {$days})";
    }

    public function timeRange(): string
    {
        $start = substr((string) ($this->start_time ?: (Career::SHIFT_SCHEDULES[$this->shift]['start'] ?? '07:30')), 0, 5);
        $end = substr((string) ($this->end_time ?: (Career::SHIFT_SCHEDULES[$this->shift]['end'] ?? '11:30')), 0, 5);

        return "{$start} - {$end}";
    }

    public function resolvedStartTime(): string
    {
        return substr((string) ($this->start_time ?: (Career::SHIFT_SCHEDULES[$this->shift]['start'] ?? '07:30')), 0, 5);
    }

    public function resolvedEndTime(): string
    {
        return substr((string) ($this->end_time ?: (Career::SHIFT_SCHEDULES[$this->shift]['end'] ?? '11:30')), 0, 5);
    }

    public function overlapsTimeWith(?string $otherStart, ?string $otherEnd): bool
    {
        if (! $otherStart || ! $otherEnd) {
            return false;
        }

        $s1 = $this->resolvedStartTime();
        $e1 = $this->resolvedEndTime();
        $s2 = substr($otherStart, 0, 5);
        $e2 = substr($otherEnd, 0, 5);

        return $s1 < $e2 && $s2 < $e1;
    }

    public function monthLabel(): string
    {
        return Semester::ALL_MONTHS[$this->month] ?? ('Mes '.$this->month);
    }

    public function isWithinEnrollmentWindow(?Carbon $referenceDate = null): bool
    {
        if ($this->is_disabled || $this->closed_at !== null) {
            return false;
        }

        $today = ($referenceDate ?? now())->copy()->startOfDay();
        $deadline = $this->start_date->copy()->addDays(self::ENROLLMENT_WINDOW_DAYS);

        return $today->lte($deadline);
    }

    public function enrollmentDeadline(): Carbon
    {
        return $this->start_date->copy()->addDays(self::ENROLLMENT_WINDOW_DAYS);
    }

    public function availableSlots(): int
    {
        $enrolled = $this->active_enrollments_count ?? $this->activeEnrollments()->count();

        return max(0, $this->max_capacity - $enrolled);
    }

    public function totalTaskPoints(): int
    {
        return (int) ($this->tasks_sum_max_points ?? $this->tasks()->sum('max_points'));
    }

    public function isManuallyClosed(): bool
    {
        return $this->closed_at !== null;
    }

    public function canBeDeleted(): bool
    {
        $count = $this->enrollments_count ?? $this->enrollments()->count();

        return $count === 0;
    }
}
