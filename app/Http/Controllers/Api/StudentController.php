<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use App\Services\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function __construct(
        protected AuditLogger $auditLogger
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        $query = Student::with(['user', 'currentClass', 'section']);

        if ($request->has('class_id')) {
            $query->where('current_class_id', $request->class_id);
        }

        if ($request->has('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('student_code', 'like', "%{$search}%")
                  ->orWhere('admission_no', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $students = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => StudentResource::collection($students)->response()->getData(true),
            'errors' => []
        ]);
    }

    public function store(StoreStudentRequest $request): JsonResponse
    {
        $this->authorize('create', Student::class);

        $student = Student::create($request->validated());
        $this->auditLogger->logCreate($student);

        return response()->json([
            'status' => 'success',
            'code' => 201,
            'data' => new StudentResource($student->load(['user', 'currentClass', 'section'])),
            'errors' => []
        ], 201);
    }

    public function show(Student $student): JsonResponse
    {
        $this->authorize('view', $student);

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => new StudentResource($student->load(['user', 'currentClass', 'section', 'parents'])),
            'errors' => []
        ]);
    }

    public function update(UpdateStudentRequest $request, Student $student): JsonResponse
    {
        $this->authorize('update', $student);

        $original = $student->toArray();
        $student->update($request->validated());
        
        $this->auditLogger->logUpdate($student, $original);

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => new StudentResource($student->load(['user', 'currentClass', 'section'])),
            'errors' => []
        ]);
    }

    public function destroy(Student $student): JsonResponse
    {
        $this->authorize('delete', $student);

        $this->auditLogger->logDelete($student);
        $student->delete();

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => ['message' => 'Student deleted successfully'],
            'errors' => []
        ]);
    }

    public function linkParent(Request $request, Student $student): JsonResponse
    {
        $this->authorize('update', $student);

        $validated = $request->validate([
            'parent_id' => 'sometimes|exists:parents,id',
            'parent_phone' => 'sometimes|string',
            'parent_email' => 'sometimes|email',
            'parent_link_code' => 'sometimes|string',
            'is_primary' => 'boolean',
        ]);

        $identityResolver = app(\App\Services\IdentityResolver::class);

        // Handle different linking methods
        if (isset($validated['parent_link_code'])) {
            $verifiedStudent = $identityResolver->verifyLinkCode($validated['parent_link_code']);
            if (!$verifiedStudent || $verifiedStudent->id !== $student->id) {
                return response()->json([
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Invalid or expired link code',
                    'errors' => []
                ], 400);
            }
        }

        if (isset($validated['parent_id'])) {
            $parent = \App\Models\ParentModel::findOrFail($validated['parent_id']);
        } elseif (isset($validated['parent_phone']) || isset($validated['parent_email'])) {
            $identifier = $validated['parent_phone'] ?? $validated['parent_email'];
            $parent = $identityResolver->findParentByPhoneOrEmail($identifier);
            
            if (!$parent) {
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Parent not found with provided identifier',
                    'errors' => []
                ], 404);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Please provide parent_id, parent_phone, parent_email, or parent_link_code',
                'errors' => []
            ], 400);
        }

        $identityResolver->linkParentToStudent(
            $parent,
            $student,
            $validated['is_primary'] ?? false,
            'manual'
        );

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => [
                'message' => 'Parent linked successfully',
                'student' => new StudentResource($student->load('parents')),
            ],
            'errors' => []
        ]);
    }
}
