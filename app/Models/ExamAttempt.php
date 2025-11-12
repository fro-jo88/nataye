<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'student_id',
        'started_at',
        'submitted_at',
        'status',
        'total_score',
        'answers',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'answers' => 'array',
        'total_score' => 'float',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isGraded(): bool
    {
        return $this->status === 'graded';
    }

    public function submit(): void
    {
        $this->update([
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);
    }

    public function grade(float $score): void
    {
        $this->update([
            'total_score' => $score,
            'status' => 'graded',
        ]);
    }

    public function getDurationAttribute(): ?int
    {
        if (!$this->submitted_at) {
            return null;
        }

        return $this->started_at->diffInMinutes($this->submitted_at);
    }

    public function getPercentageAttribute(): ?float
    {
        if (!$this->total_score || !$this->exam->total_marks) {
            return null;
        }

        return ($this->total_score / $this->exam->total_marks) * 100;
    }

    public function scopeByStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByExam($query, int $examId)
    {
        return $query->where('exam_id', $examId);
    }

    public function scopeGraded($query)
    {
        return $query->where('status', 'graded');
    }
}
