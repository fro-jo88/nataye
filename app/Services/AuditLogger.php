<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    /**
     * Log an audit entry
     */
    public function log(
        ?User $actor,
        string $action,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $data = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): AuditLog {
        return AuditLog::create([
            'actor_user_id' => $actor?->id,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'before' => null,
            'after' => $this->redactSensitiveData($data),
            'ip_address' => $ipAddress ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
        ]);
    }

    /**
     * Log model creation
     */
    public function logCreate(Model $model, ?User $actor = null): AuditLog
    {
        return $this->log(
            $actor ?? auth()->user(),
            'create',
            get_class($model),
            $model->id,
            $model->toArray()
        );
    }

    /**
     * Log model update
     */
    public function logUpdate(Model $model, array $original, ?User $actor = null): AuditLog
    {
        return AuditLog::create([
            'actor_user_id' => ($actor ?? auth()->user())?->id,
            'action' => 'update',
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'before' => $this->redactSensitiveData($original),
            'after' => $this->redactSensitiveData($model->toArray()),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Log model deletion
     */
    public function logDelete(Model $model, ?User $actor = null): AuditLog
    {
        return $this->log(
            $actor ?? auth()->user(),
            'delete',
            get_class($model),
            $model->id,
            $model->toArray()
        );
    }

    /**
     * Redact sensitive data from audit logs
     */
    protected function redactSensitiveData(?array $data): ?array
    {
        if (!$data) {
            return null;
        }

        $sensitiveFields = [
            'password',
            'password_confirmation',
            'current_password',
            'remember_token',
            'api_token',
            'token',
        ];

        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '[REDACTED]';
            }
        }

        return $data;
    }

    /**
     * Get audit trail for a model
     */
    public function getAuditTrail(string $modelType, int $modelId): \Illuminate\Database\Eloquent\Collection
    {
        return AuditLog::byModel($modelType, $modelId)
            ->with('actor')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Get recent audit logs for a user
     */
    public function getUserAuditLogs(User $user, int $days = 30): \Illuminate\Database\Eloquent\Collection
    {
        return AuditLog::byActor($user->id)
            ->where('created_at', '>=', now()->subDays($days))
            ->orderByDesc('created_at')
            ->get();
    }
}
