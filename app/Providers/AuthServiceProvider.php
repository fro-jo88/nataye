<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\Student::class => \App\Policies\StudentPolicy::class,
        \App\Models\Teacher::class => \App\Policies\TeacherPolicy::class,
        \App\Models\ParentModel::class => \App\Policies\ParentPolicy::class,
        \App\Models\Exam::class => \App\Policies\ExamPolicy::class,
        \App\Models\Grade::class => \App\Policies\GradePolicy::class,
        \App\Models\AttendanceSession::class => \App\Policies\AttendancePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Super admin gate
        Gate::before(function ($user, $ability) {
            return $user->isAdmin() ? true : null;
        });
    }
}
