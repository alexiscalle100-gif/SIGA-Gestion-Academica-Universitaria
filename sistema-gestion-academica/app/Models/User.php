<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'administrador';

    public const ROLE_TEACHER = 'docente';

    public const ROLE_STUDENT = 'estudiante';

    public const ROLES = [
        self::ROLE_ADMIN => 'Administrador',
        self::ROLE_TEACHER => 'Docente',
        self::ROLE_STUDENT => 'Estudiante',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'code',
        'document_number',
        'phone',
        'faculty_id',
        'career_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isTeacher(): bool
    {
        return $this->role === self::ROLE_TEACHER;
    }

    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? ucfirst($this->role);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Facultades asignadas al docente (un docente puede dictar en múltiples facultades).
     */
    public function faculties(): BelongsToMany
    {
        return $this->belongsToMany(Faculty::class, 'teacher_faculties', 'teacher_id', 'faculty_id')
            ->withPivot(['is_primary'])
            ->withTimestamps();
    }

    public function assignedFaculties()
    {
        $list = $this->faculties()->orderByPivot('is_primary', 'desc')->orderBy('name')->get();
        if ($list->isEmpty() && $this->faculty) {
            return collect([$this->faculty]);
        }

        return $list;
    }

    public function belongsToFaculty(int $facultyId): bool
    {
        if ((int) $this->faculty_id === $facultyId) {
            return true;
        }

        return $this->faculties()->where('faculties.id', $facultyId)->exists();
    }

    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
    }

    /**
     * Carreras del estudiante (soporta 1 o doble carrera incluso en distintas facultades).
     */
    public function careers(): BelongsToMany
    {
        return $this->belongsToMany(Career::class, 'student_careers', 'student_id', 'career_id')
            ->withPivot(['is_primary', 'is_active', 'enrolled_at'])
            ->withTimestamps();
    }

    public function activeCareers()
    {
        $list = $this->careers()->wherePivot('is_active', true)->with('faculty')->orderByPivot('is_primary', 'desc')->get();
        if ($list->isEmpty() && $this->career) {
            return collect([$this->career->loadMissing('faculty')]);
        }

        return $list;
    }

    public function offeringsTaught(): HasMany
    {
        return $this->hasMany(Offering::class, 'teacher_id');
    }

    public function tuitions(): HasMany
    {
        return $this->hasMany(Tuition::class, 'student_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function appNotifications(): HasMany
    {
        return $this->hasMany(AppNotification::class, 'user_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    public function unreadNotificationsCount(): int
    {
        return $this->appNotifications()->whereNull('read_at')->count();
    }
}
