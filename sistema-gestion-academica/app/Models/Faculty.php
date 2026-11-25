<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Faculty extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function careers(): HasMany
    {
        return $this->hasMany(Career::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function teachers(): HasMany
    {
        return $this->hasMany(User::class)->where('role', User::ROLE_TEACHER);
    }

    public function hasAssociatedData(): bool
    {
        return $this->careers()->exists()
            || $this->subjects()->exists()
            || $this->teachers()->exists();
    }
}
