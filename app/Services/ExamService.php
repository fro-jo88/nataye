<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\QuestionBank;
use App\Models\Student;
use App\Models\Grade;

class ExamService
{
    public function __construct(
        protected AuditLogger $auditLogger
    ) {}

    /**
     * Create a new exam
     */
    public function createExam(array $data, int $createdBy): Exam
    {
        $exam = Exam::create(array_merge($data, [
            'created_by' => $createdBy,
        ]));

        $this->auditLogger->logCreate($exam);

        return $exam;
    }

    /**
     * Add questions to an exam
     */
    public function addQuestions(Exam $exam, array $questions, int $authorId): array
    {
        $created = [];

        foreach ($questions as $questionData) {
            $question = $exam->questions()->create(array_merge($questionData, [
                'author_id' => $authorId,
            ]));

            $created[] = $question;
        }

        return $created;
    }

    /**
     * Start an exam attempt for a student
     */
    public function startAttempt(Exam $exam, Student $student): ExamAttempt
    {
        if (!$exam->isActive()) {
            throw new \Exception('Exam is not currently active');
        }

        // Check if student already has an attempt
        $existingAttempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->first();

        if ($existingAttempt) {
            throw new \Exception('Student has already attempted this exam');
        }

        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        $this->auditLogger->logCreate($attempt);

        return $attempt;
    }

    /**
     * Submit exam attempt
     */
    public function submitAttempt(ExamAttempt $attempt, array $answers): ExamAttempt
    {
        $attempt->update([
            'answers' => $answers,
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        // Auto-grade MCQ questions
        $this->autoGradeMcq($attempt);

        $this->auditLogger->logUpdate($attempt, $attempt->getOriginal());

        return $attempt->fresh();
    }

    /**
     * Auto-grade MCQ questions
     */
    protected function autoGradeMcq(ExamAttempt $attempt): void
    {
        $questions = $attempt->exam->questions()->where('type', 'mcq')->get();
        $totalScore = 0;

        foreach ($questions as $question) {
            $studentAnswer = $attempt->answers[$question->id] ?? null;
            
            if ($studentAnswer && $question->checkAnswer($studentAnswer)) {
                $totalScore += $question->marks;
            }
        }

        // Only update score if all questions are MCQ (auto-gradable)
        $allMcq = $attempt->exam->questions()->where('type', '!=', 'mcq')->count() === 0;

        if ($allMcq) {
            $attempt->grade($totalScore);
        }
    }

    /**
     * Manually grade an exam attempt
     */
    public function gradeAttempt(
        ExamAttempt $attempt,
        float $totalScore,
        ?array $questionScores = null
    ): Grade {
        $attempt->grade($totalScore);

        // Create or update grade record
        $grade = Grade::updateOrCreate(
            [
                'student_id' => $attempt->student_id,
                'exam_id' => $attempt->exam_id,
                'subject_id' => $attempt->exam->classroom->subjects()->first()?->id, // Simplified
            ],
            [
                'marks_obtained' => $totalScore,
            ]
        );

        $this->auditLogger->logUpdate($attempt, $attempt->getOriginal());

        return $grade;
    }

    /**
     * Get exam statistics
     */
    public function getExamStatistics(Exam $exam): array
    {
        $attempts = $exam->attempts()->graded()->get();

        if ($attempts->isEmpty()) {
            return [
                'total_attempts' => 0,
                'graded' => 0,
                'average_score' => 0,
                'highest_score' => 0,
                'lowest_score' => 0,
                'pass_rate' => 0,
            ];
        }

        $scores = $attempts->pluck('total_score');

        return [
            'total_attempts' => $exam->attempts()->count(),
            'graded' => $attempts->count(),
            'average_score' => $scores->avg(),
            'highest_score' => $scores->max(),
            'lowest_score' => $scores->min(),
            'pass_rate' => $attempts->filter(fn($a) => $a->total_score >= $exam->passing_marks)->count() / $attempts->count() * 100,
        ];
    }
}
