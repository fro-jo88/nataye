<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassModel extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'class_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'current_class_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'class_id');
    }

    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class, 'class_id');
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'class_id');
    }

    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class, 'class_id');
    }

    public function getStudentCountAttribute(): int
    {
        return $this->students()->count();
    }

    public function scopeWithStudentCount($query)
    {
        return $query->withCount('students');
    }
}
