<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'offering_task_id',
        'enrollment_id',
        'submitted_at',
        'submission_note',
        'score',
        'comment',
        'graded_by',
        'graded_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'score' => 'integer',
            'graded_at' => 'datetime',
        ];
    }

    public function isSubmitted(): bool
    {
        return $this->submitted_at !== null;
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(OfferingTask::class, 'offering_task_id');
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function isUngraded(): bool
    {
        return $this->score === null;
    }
}
