<?php

namespace App\Repositories;

use App\Models\Offering;
use App\Models\User;
use App\Repositories\Contracts\OfferingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class OfferingRepository implements OfferingRepositoryInterface
{
    public function getFiltered(?int $facultyId = null, ?int $semesterId = null, ?int $month = null, ?string $shift = null): Collection
    {
        return Offering::query()
            ->with(['subject.faculty', 'semester', 'teacher'])
            ->withCount(['enrollments', 'activeEnrollments'])
            ->when($facultyId, fn ($q) => $q->whereHas('subject', fn ($sq) => $sq->where('faculty_id', $facultyId)))
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->when($month, fn ($q) => $q->where('month', $month))
            ->when($shift, fn ($q) => $q->where('shift', $shift))
            ->orderByDesc('semester_id')
            ->orderBy('month')
            ->orderBy('shift')
            ->get();
    }

    public function getForTeacher(User $teacher): Collection
    {
        return Offering::query()
            ->where('teacher_id', $teacher->id)
            ->with(['subject.faculty', 'semester'])
            ->withCount('activeEnrollments')
            ->withSum('tasks', 'max_points')
            ->orderByDesc('semester_id')
            ->orderByDesc('month')
            ->get();
    }

    public function findDetailed(int $id): Offering
    {
        return Offering::query()
            ->with([
                'subject.faculty',
                'semester',
                'teacher',
                'tasks',
                'activeEnrollments.student.career.faculty',
                'activeEnrollments.career.faculty',
                'activeEnrollments.taskGrades',
                'activeEnrollments.gradeAdjustments.adjuster',
            ])
            ->withCount(['enrollments', 'activeEnrollments'])
            ->findOrFail($id);
    }
}
