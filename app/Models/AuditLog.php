<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'actor_user_id',
        'action',
        'model_type',
        'model_id',
        'before',
        'after',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'before' => 'array',
        'after' => 'array',
        'created_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($log) {
            $log->created_at = now();
        });
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public function getChangesAttribute(): array
    {
        if (!$this->before || !$this->after) {
            return [];
        }

        $changes = [];
        foreach ($this->after as $key => $value) {
            if (!isset($this->before[$key]) || $this->before[$key] !== $value) {
                $changes[$key] = [
                    'old' => $this->before[$key] ?? null,
                    'new' => $value,
                ];
            }
        }

        return $changes;
    }

    public function scopeByActor($query, int $userId)
    {
        return $query->where('actor_user_id', $userId);
    }

    public function scopeByModel($query, string $modelType, ?int $modelId = null)
    {
        $query = $query->where('model_type', $modelType);
        
        if ($modelId !== null) {
            $query->where('model_id', $modelId);
        }
        
        return $query;
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
