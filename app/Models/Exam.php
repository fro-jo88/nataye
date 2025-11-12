<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'code',
        'description',
        'class_id',
        'section_id',
        'start_datetime',
        'end_datetime',
        'total_marks',
        'is_online',
        'passing_marks',
        'created_by',
        'status',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'total_marks' => 'float',
        'passing_marks' => 'float',
        'is_online' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($exam) {
            if (empty($exam->code)) {
                $exam->code = 'EXM' . strtoupper(substr(uniqid(), -8));
            }
        });
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuestionBank::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function isActive(): bool
    {
        return $this->isPublished() && 
               now()->between($this->start_datetime, $this->end_datetime);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'published')
                    ->where('start_datetime', '<=', now())
                    ->where('end_datetime', '>=', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'published')
                    ->where('start_datetime', '>', now());
    }

    public function scopeByClass($query, int $classId)
    {
        return $query->where('class_id', $classId);
    }
}
