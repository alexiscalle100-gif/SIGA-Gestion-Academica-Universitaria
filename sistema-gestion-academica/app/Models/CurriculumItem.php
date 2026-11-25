<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CurriculumItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'career_id',
        'subject_id',
        'level',
        'month_in_level',
        'order_in_curriculum',
        'is_elective',
        'elective_number',
        'requires_full_level',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'month_in_level' => 'integer',
            'order_in_curriculum' => 'integer',
            'is_elective' => 'boolean',
            'elective_number' => 'integer',
            'requires_full_level' => 'integer',
        ];
    }

    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(
            Subject::class,
            'curriculum_prerequisites',
            'curriculum_item_id',
            'prerequisite_subject_id'
        );
    }
}
