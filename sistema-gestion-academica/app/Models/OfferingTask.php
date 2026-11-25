<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfferingTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'offering_id',
        'title',
        'description',
        'max_points',
        'due_date',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'max_points' => 'integer',
            'due_date' => 'date',
        ];
    }

    public function offering(): BelongsTo
    {
        return $this->belongsTo(Offering::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(TaskGrade::class);
    }
}
