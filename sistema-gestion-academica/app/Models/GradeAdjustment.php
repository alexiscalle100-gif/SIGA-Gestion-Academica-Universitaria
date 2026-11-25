<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'adjusted_by',
        'previous_score',
        'new_score',
        'previous_status',
        'new_status',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'previous_score' => 'integer',
            'new_score' => 'integer',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function adjuster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }
}
