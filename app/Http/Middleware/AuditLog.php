<?php

namespace App\Http\Middleware;

use App\Services\AuditLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditLog
{
    public function __construct(
        protected AuditLogger $auditLogger
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Log sensitive actions
        if ($request->user() && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $this->auditLogger->log(
                $request->user(),
                $request->method() . ' ' . $request->path(),
                null,
                null,
                $request->all(),
                $request->ip(),
                $request->userAgent()
            );
        }

        return $response;
    }
}
