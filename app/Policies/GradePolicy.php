<?php

namespace App\Policies;

use App\Models\Grade;
use App\Models\User;

class GradePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Grade $grade): bool
    {
        if ($user->isAdmin() || $user->isTeacher()) {
            return true;
        }

        if ($user->isStudent() && $user->student->id === $grade->student_id) {
            return true;
        }

        if ($user->isParent()) {
            return $user->parent->students->contains($grade->student_id);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function update(User $user, Grade $grade): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function delete(User $user, Grade $grade): bool
    {
        return $user->isAdmin();
    }
}
