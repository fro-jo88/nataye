<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register services
        $this->app->singleton(\App\Services\IdentityResolver::class);
        $this->app->singleton(\App\Services\AuditLogger::class);
    }

    public function boot(): void
    {
        Schema::defaultStringLength(255);
    }
}
