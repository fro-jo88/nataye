<?php

namespace App\Listeners;

use App\Services\AuditLogger;
use Illuminate\Auth\Events\Failed;

class LogFailedLogin
{
    public function __construct(
        protected AuditLogger $auditLogger
    ) {}

    public function handle(Failed $event): void
    {
        $this->auditLogger->log(
            null,
            'login_failed',
            'User',
            null,
            [
                'email' => $event->credentials['email'] ?? null,
                'phone' => $event->credentials['phone'] ?? null,
            ],
            request()->ip(),
            request()->userAgent()
        );
    }
}
