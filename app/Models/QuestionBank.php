<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'author_id',
        'type',
        'question_text',
        'options',
        'answer',
        'marks',
        'meta',
    ];

    protected $casts = [
        'options' => 'array',
        'meta' => 'array',
        'marks' => 'float',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function isMcq(): bool
    {
        return $this->type === 'mcq';
    }

    public function isShort(): bool
    {
        return $this->type === 'short';
    }

    public function isEssay(): bool
    {
        return $this->type === 'essay';
    }

    public function checkAnswer(string $studentAnswer): bool
    {
        if (!$this->isMcq()) {
            return false; // Manual grading required
        }

        return trim(strtolower($studentAnswer)) === trim(strtolower($this->answer));
    }

    public function scopeByExam($query, int $examId)
    {
        return $query->where('exam_id', $examId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
