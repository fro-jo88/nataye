<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;

class ExamPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Exam $exam): bool
    {
        if ($user->isAdmin() || $user->isTeacher()) {
            return true;
        }

        if ($user->isStudent()) {
            return $exam->isPublished() && 
                   $exam->class_id === $user->student->current_class_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function update(User $user, Exam $exam): bool
    {
        return $user->isAdmin() || $exam->created_by === $user->id;
    }

    public function delete(User $user, Exam $exam): bool
    {
        return $user->isAdmin();
    }
}
