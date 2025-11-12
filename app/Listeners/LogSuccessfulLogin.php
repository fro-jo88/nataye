<?php

namespace App\Listeners;

use App\Services\AuditLogger;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function __construct(
        protected AuditLogger $auditLogger
    ) {}

    public function handle(Login $event): void
    {
        $this->auditLogger->log(
            $event->user,
            'login_success',
            'User',
            $event->user->id,
            null,
            request()->ip(),
            request()->userAgent()
        );

        $event->user->update(['last_login_at' => now()]);
    }
}
