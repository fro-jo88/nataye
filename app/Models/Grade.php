<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'exam_id',
        'subject_id',
        'marks_obtained',
        'grade_letter',
        'remarks',
    ];

    protected $casts = [
        'marks_obtained' => 'float',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($grade) {
            if ($grade->marks_obtained !== null && !$grade->grade_letter) {
                $grade->grade_letter = $grade->calculateGradeLetter();
            }
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function getPercentageAttribute(): float
    {
        if (!$this->exam || !$this->exam->total_marks) {
            return 0;
        }

        return ($this->marks_obtained / $this->exam->total_marks) * 100;
    }

    public function calculateGradeLetter(): string
    {
        $percentage = $this->percentage;

        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 85 => 'A',
            $percentage >= 80 => 'A-',
            $percentage >= 75 => 'B+',
            $percentage >= 70 => 'B',
            $percentage >= 65 => 'B-',
            $percentage >= 60 => 'C+',
            $percentage >= 55 => 'C',
            $percentage >= 50 => 'C-',
            $percentage >= 45 => 'D',
            default => 'F',
        };
    }

    public function hasPassed(): bool
    {
        return $this->exam && $this->marks_obtained >= $this->exam->passing_marks;
    }

    public function scopeByStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByExam($query, int $examId)
    {
        return $query->where('exam_id', $examId);
    }

    public function scopeBySubject($query, int $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopePassed($query)
    {
        return $query->whereHas('exam', function ($q) {
            $q->whereColumn('grades.marks_obtained', '>=', 'exams.passing_marks');
        });
    }
}
