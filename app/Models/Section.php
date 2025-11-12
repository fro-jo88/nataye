<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'name',
        'capacity',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->class ? "{$this->class->name} - {$this->name}" : $this->name;
    }

    public function getStudentCountAttribute(): int
    {
        return $this->students()->count();
    }

    public function hasCapacity(): bool
    {
        return $this->capacity === 0 || $this->student_count < $this->capacity;
    }

    public function scopeByClass($query, int $classId)
    {
        return $query->where('class_id', $classId);
    }
}
