<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_no',
        'hire_date',
        'qualification',
        'extra',
    ];

    protected $casts = [
        'extra' => 'array',
        'hire_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($teacher) {
            if (empty($teacher->employee_no)) {
                $teacher->employee_no = 'EMP' . str_pad(Teacher::max('id') + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }

    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'class_subject_teacher')
            ->withPivot('class_id', 'section_id')
            ->withTimestamps();
    }

    public function getFullNameAttribute(): ?string
    {
        return $this->user ? $this->user->full_name : null;
    }

    public function scopeActive($query)
    {
        return $query->whereHas('user', function ($q) {
            $q->where('status', 'active');
        });
    }
}
