<?php

namespace App\Repositories\Contracts;

use App\Models\Offering;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface OfferingRepositoryInterface
{
    public function getFiltered(?int $facultyId = null, ?int $semesterId = null, ?int $month = null, ?string $shift = null): Collection;

    public function getForTeacher(User $teacher): Collection;

    public function findDetailed(int $id): Offering;
}
