<?php

namespace App\Policies;

use App\Models\AttendanceSession;
use App\Models\User;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function view(User $user, AttendanceSession $session): bool
    {
        if ($user->isAdmin() || $user->isTeacher()) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function update(User $user, AttendanceSession $session): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher() && $session->teacher_id === $user->teacher->id) {
            return true;
        }

        return false;
    }

    public function delete(User $user, AttendanceSession $session): bool
    {
        return $user->isAdmin();
    }
}
