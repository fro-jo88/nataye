<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExamRequest;
use App\Http\Resources\ExamResource;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Services\ExamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function __construct(
        protected ExamService $examService
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        $query = Exam::with(['classroom', 'section', 'creator']);

        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->user()->isStudent()) {
            $query->where('class_id', $request->user()->student->current_class_id)
                  ->where('section_id', $request->user()->student->section_id)
                  ->published();
        }

        $exams = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => ExamResource::collection($exams)->response()->getData(true),
            'errors' => []
        ]);
    }

    public function store(StoreExamRequest $request): JsonResponse
    {
        $this->authorize('create', Exam::class);

        $exam = $this->examService->createExam(
            $request->validated(),
            $request->user()->id
        );

        return response()->json([
            'status' => 'success',
            'code' => 201,
            'data' => new ExamResource($exam->load(['classroom', 'section'])),
            'errors' => []
        ], 201);
    }

    public function show(Exam $exam): JsonResponse
    {
        $this->authorize('view', $exam);

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => new ExamResource($exam->load(['classroom', 'section', 'questions'])),
            'errors' => []
        ]);
    }

    public function addQuestions(Request $request, Exam $exam): JsonResponse
    {
        $this->authorize('update', $exam);

        $validated = $request->validate([
            'questions' => 'required|array',
            'questions.*.type' => 'required|in:mcq,short,essay',
            'questions.*.question_text' => 'required|string',
            'questions.*.options' => 'required_if:questions.*.type,mcq|array',
            'questions.*.answer' => 'required|string',
            'questions.*.marks' => 'required|numeric|min:0',
        ]);

        $questions = $this->examService->addQuestions(
            $exam,
            $validated['questions'],
            $request->user()->id
        );

        return response()->json([
            'status' => 'success',
            'code' => 201,
            'data' => [
                'message' => 'Questions added successfully',
                'count' => count($questions),
            ],
            'errors' => []
        ], 201);
    }

    public function attempt(Request $request, Exam $exam): JsonResponse
    {
        if (!$request->user()->isStudent()) {
            return response()->json([
                'status' => 'error',
                'code' => 403,
                'message' => 'Only students can attempt exams',
                'errors' => []
            ], 403);
        }

        try {
            $attempt = $this->examService->startAttempt($exam, $request->user()->student);

            return response()->json([
                'status' => 'success',
                'code' => 201,
                'data' => [
                    'attempt_id' => $attempt->id,
                    'started_at' => $attempt->started_at,
                    'questions' => $exam->questions,
                ],
                'errors' => []
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => $e->getMessage(),
                'errors' => []
            ], 400);
        }
    }

    public function submit(Request $request, Exam $exam): JsonResponse
    {
        $validated = $request->validate([
            'attempt_id' => 'required|exists:exam_attempts,id',
            'answers' => 'required|array',
        ]);

        $attempt = ExamAttempt::findOrFail($validated['attempt_id']);

        if ($attempt->student_id !== $request->user()->student->id) {
            return response()->json([
                'status' => 'error',
                'code' => 403,
                'message' => 'Unauthorized',
                'errors' => []
            ], 403);
        }

        try {
            $attempt = $this->examService->submitAttempt($attempt, $validated['answers']);

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'data' => [
                    'message' => 'Exam submitted successfully',
                    'attempt' => $attempt,
                ],
                'errors' => []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => $e->getMessage(),
                'errors' => []
            ], 400);
        }
    }

    public function grade(Request $request, Exam $exam): JsonResponse
    {
        $this->authorize('update', $exam);

        $validated = $request->validate([
            'attempt_id' => 'required|exists:exam_attempts,id',
            'total_score' => 'required|numeric|min:0',
            'question_scores' => 'nullable|array',
        ]);

        $attempt = ExamAttempt::findOrFail($validated['attempt_id']);
        
        $grade = $this->examService->gradeAttempt(
            $attempt,
            $validated['total_score'],
            $validated['question_scores'] ?? null
        );

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => [
                'message' => 'Exam graded successfully',
                'grade' => $grade,
            ],
            'errors' => []
        ]);
    }
}
