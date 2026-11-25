<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tuition extends Model
{
    use HasFactory;

    public const DEFAULT_MAX_SUBJECTS = 6;

    protected $fillable = [
        'student_id',
        'career_id',
        'semester_id',
        'is_active',
        'max_subjects',
        'activated_by',
        'activated_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'max_subjects' => 'integer',
            'activated_at' => 'datetime',
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

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function activator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'activated_by');
    }
}
