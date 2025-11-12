<?php

namespace App\Services;

use App\Models\AttendanceSession;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;

class AttendanceService
{
    public function __construct(
        protected AuditLogger $auditLogger
    ) {}

    /**
     * Create or open an attendance session
     */
    public function openSession(
        int $classId,
        ?int $sectionId,
        ?int $subjectId,
        ?int $teacherId,
        \DateTime $date
    ): AttendanceSession {
        return AttendanceSession::firstOrCreate([
            'class_id' => $classId,
            'section_id' => $sectionId,
            'subject_id' => $subjectId,
            'date' => $date->format('Y-m-d'),
        ], [
            'teacher_id' => $teacherId,
            'status' => 'open',
        ]);
    }

    /**
     * Mark attendance for multiple students
     */
    public function markBulkAttendance(
        AttendanceSession $session,
        array $attendanceData,
        User $recordedBy
    ): array {
        if ($session->isLocked()) {
            throw new \Exception('Attendance session is locked');
        }

        $results = [];

        foreach ($attendanceData as $data) {
            $attendance = Attendance::updateOrCreate(
                [
                    'attendance_session_id' => $session->id,
                    'student_id' => $data['student_id'],
                ],
                [
                    'status' => $data['status'],
                    'recorded_by' => $recordedBy->id,
                    'notes' => $data['notes'] ?? null,
                    'recorded_at' => now(),
                ]
            );

            $results[] = $attendance;
        }

        // Log the action
        $this->auditLogger->log(
            $recordedBy,
            'mark_attendance',
            'AttendanceSession',
            $session->id,
            ['count' => count($results)]
        );

        return $results;
    }

    /**
     * Lock an attendance session
     */
    public function lockSession(AttendanceSession $session, User $user): void
    {
        $session->lock();

        $this->auditLogger->log(
            $user,
            'lock_attendance_session',
            'AttendanceSession',
            $session->id
        );
    }

    /**
     * Get attendance statistics for a student
     */
    public function getStudentAttendanceStats(Student $student, ?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        $query = $student->attendances()
            ->with('session');

        if ($startDate) {
            $query->whereHas('session', function ($q) use ($startDate) {
                $q->where('date', '>=', $startDate->format('Y-m-d'));
            });
        }

        if ($endDate) {
            $query->whereHas('session', function ($q) use ($endDate) {
                $q->where('date', '<=', $endDate->format('Y-m-d'));
            });
        }

        $attendances = $query->get();

        return [
            'total' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'excused' => $attendances->where('status', 'excused')->count(),
            'percentage' => $attendances->count() > 0 
                ? round(($attendances->where('status', 'present')->count() / $attendances->count()) * 100, 2)
                : 0,
        ];
    }

    /**
     * Get attendance report for a class
     */
    public function getClassAttendanceReport(
        int $classId,
        ?int $sectionId,
        \DateTime $startDate,
        \DateTime $endDate
    ): Collection {
        $sessions = AttendanceSession::where('class_id', $classId)
            ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
            ->byDateRange($startDate, $endDate)
            ->with(['attendances.student'])
            ->get();

        return $sessions;
    }
}
