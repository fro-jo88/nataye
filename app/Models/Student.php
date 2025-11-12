<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_code',
        'admission_no',
        'gender',
        'date_of_birth',
        'enrollment_date',
        'current_class_id',
        'section_id',
        'address',
        'photo_path',
        'extra',
    ];

    protected $casts = [
        'extra' => 'array',
        'date_of_birth' => 'date',
        'enrollment_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($student) {
            if (empty($student->student_code)) {
                $student->student_code = 'STD' . str_pad(Student::max('id') + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currentClass(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'current_class_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(ParentModel::class, 'student_parent')
            ->withPivot('is_primary', 'linked_at')
            ->withTimestamps();
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function examAttempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function getFullNameAttribute(): ?string
    {
        return $this->user ? $this->user->full_name : null;
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth 
            ? $this->date_of_birth->diffInYears(now()) 
            : null;
    }

    public function scopeByClass($query, int $classId)
    {
        return $query->where('current_class_id', $classId);
    }

    public function scopeBySection($query, int $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    public function scopeActive($query)
    {
        return $query->whereHas('user', function ($q) {
            $q->where('status', 'active');
        });
    }
}
