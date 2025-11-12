<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_datetime',
        'end_datetime',
        'location',
        'target_audience',
        'created_by',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'target_audience' => 'array',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isUpcoming(): bool
    {
        return $this->start_datetime->isFuture();
    }

    public function isOngoing(): bool
    {
        return now()->between($this->start_datetime, $this->end_datetime);
    }

    public function isPast(): bool
    {
        return $this->end_datetime->isPast();
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_datetime', '>', now());
    }

    public function scopeOngoing($query)
    {
        return $query->where('start_datetime', '<=', now())
                    ->where('end_datetime', '>=', now());
    }

    public function scopePast($query)
    {
        return $query->where('end_datetime', '<', now());
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_datetime', [$startDate, $endDate]);
    }
}
