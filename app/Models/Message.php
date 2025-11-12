<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'thread_id',
        'subject',
        'body',
        'is_read',
        'attachments',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'attachments' => 'array',
    ];

    public function from(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function to(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update(['is_read' => true]);
        }
    }

    public function markAsUnread(): void
    {
        if ($this->is_read) {
            $this->update(['is_read' => false]);
        }
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeByThread($query, int $threadId)
    {
        return $query->where('thread_id', $threadId);
    }

    public function scopeBetweenUsers($query, int $user1Id, int $user2Id)
    {
        return $query->where(function ($q) use ($user1Id, $user2Id) {
            $q->where('from_user_id', $user1Id)->where('to_user_id', $user2Id);
        })->orWhere(function ($q) use ($user1Id, $user2Id) {
            $q->where('from_user_id', $user2Id)->where('to_user_id', $user1Id);
        });
    }
}
