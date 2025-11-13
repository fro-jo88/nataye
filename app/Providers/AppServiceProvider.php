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

        if ($this->app->environment('local', 'testing') && class_exists(\Laravel\Dusk\DuskServiceProvider::class)) {
            $this->app->register(\Laravel\Dusk\DuskServiceProvider::class);
        }
    }

    public function boot(): void
    {
        Schema::defaultStringLength(255);
    }
}
