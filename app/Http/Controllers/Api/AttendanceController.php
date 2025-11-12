<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MarkAttendanceRequest;
use App\Http\Resources\AttendanceSessionResource;
use App\Models\AttendanceSession;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {
        $this->middleware('auth:sanctum');
    }

    public function createSession(Request $request): JsonResponse
    {
        $this->authorize('create', AttendanceSession::class);

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'date' => 'required|date',
        ]);

        $session = $this->attendanceService->openSession(
            $validated['class_id'],
            $validated['section_id'] ?? null,
            $validated['subject_id'] ?? null,
            $request->user()->teacher?->id,
            new \DateTime($validated['date'])
        );

        return response()->json([
            'status' => 'success',
            'code' => 201,
            'data' => new AttendanceSessionResource($session->load(['class', 'section', 'subject'])),
            'errors' => []
        ], 201);
    }

    public function markAttendance(MarkAttendanceRequest $request, AttendanceSession $session): JsonResponse
    {
        $this->authorize('update', $session);

        try {
            $attendances = $this->attendanceService->markBulkAttendance(
                $session,
                $request->validated()['attendances'],
                $request->user()
            );

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'data' => [
                    'message' => 'Attendance marked successfully',
                    'count' => count($attendances),
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

    public function lockSession(AttendanceSession $session, Request $request): JsonResponse
    {
        $this->authorize('update', $session);

        $this->attendanceService->lockSession($session, $request->user());

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => ['message' => 'Attendance session locked successfully'],
            'errors' => []
        ]);
    }

    public function getReports(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $report = $this->attendanceService->getClassAttendanceReport(
            $validated['class_id'],
            $validated['section_id'] ?? null,
            new \DateTime($validated['start_date']),
            new \DateTime($validated['end_date'])
        );

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => AttendanceSessionResource::collection($report),
            'errors' => []
        ]);
    }
}
