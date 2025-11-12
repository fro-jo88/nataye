<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'body',
        'target_roles',
        'target_user_ids',
        'meta',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'target_roles' => 'array',
        'target_user_ids' => 'array',
        'meta' => 'array',
        'sent_at' => 'datetime',
    ];

    public function isQueued(): bool
    {
        return $this->status === 'queued';
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    public function scopeQueued($query)
    {
        return $query->where('status', 'queued');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
