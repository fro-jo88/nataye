<?php

namespace App\Policies;

use App\Models\ParentModel;
use App\Models\User;

class ParentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function view(User $user, ParentModel $parent): bool
    {
        return $user->isAdmin() || 
               ($user->isParent() && $user->parentProfile->id === $parent->id);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, ParentModel $parent): bool
    {
        return $user->isAdmin() || 
               ($user->isParent() && $user->parentProfile->id === $parent->id);
    }

    public function delete(User $user, ParentModel $parent): bool
    {
        return $user->isAdmin();
    }
}
