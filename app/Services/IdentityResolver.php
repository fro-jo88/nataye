<?php

namespace App\Services;

use App\Models\Student;
use App\Models\ParentModel;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;

class IdentityResolver
{
    protected PhoneNumberUtil $phoneUtil;

    public function __construct()
    {
        $this->phoneUtil = PhoneNumberUtil::getInstance();
    }

    /**
     * Normalize phone number to E.164 format
     */
    public function normalizePhone(string $phone, string $country = 'US'): ?string
    {
        try {
            $numberProto = $this->phoneUtil->parse($phone, $country);
            return $this->phoneUtil->format($numberProto, PhoneNumberFormat::E164);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Find students matching parent phone number
     */
    public function findStudentsByParentPhone(string $phone): array
    {
        $normalizedPhone = $this->normalizePhone($phone);
        if (!$normalizedPhone) {
            return [];
        }

        // Search in student extra data for guardian phone
        return Student::whereRaw("JSON_EXTRACT(extra, '$.guardian_phone') = ?", [$normalizedPhone])
            ->orWhereRaw("JSON_EXTRACT(extra, '$.guardian_phone_normalized') = ?", [$normalizedPhone])
            ->with(['currentClass', 'section', 'user'])
            ->get()
            ->toArray();
    }

    /**
     * Find students matching parent email
     */
    public function findStudentsByParentEmail(string $email): array
    {
        return Student::whereRaw("JSON_EXTRACT(extra, '$.guardian_email') = ?", [$email])
            ->with(['currentClass', 'section', 'user'])
            ->get()
            ->toArray();
    }

    /**
     * Find parent by phone or email
     */
    public function findParentByPhoneOrEmail(string $phoneOrEmail): ?ParentModel
    {
        // Try phone first
        if (filter_var($phoneOrEmail, FILTER_VALIDATE_EMAIL)) {
            return ParentModel::where('email', $phoneOrEmail)->first();
        } else {
            $normalizedPhone = $this->normalizePhone($phoneOrEmail);
            if ($normalizedPhone) {
                return ParentModel::where('phone', $normalizedPhone)->first();
            }
        }

        return null;
    }

    /**
     * Match parent to students with confidence scores
     */
    public function matchParentToStudents(ParentModel $parent): array
    {
        $matches = [];

        // Match by phone
        if ($parent->phone) {
            $studentsByPhone = $this->findStudentsByParentPhone($parent->phone);
            foreach ($studentsByPhone as $student) {
                $matches[$student['id']] = [
                    'student' => $student,
                    'match_method' => 'phone',
                    'confidence' => 'high',
                ];
            }
        }

        // Match by email
        if ($parent->email) {
            $studentsByEmail = $this->findStudentsByParentEmail($parent->email);
            foreach ($studentsByEmail as $student) {
                if (!isset($matches[$student['id']])) {
                    $matches[$student['id']] = [
                        'student' => $student,
                        'match_method' => 'email',
                        'confidence' => 'medium',
                    ];
                }
            }
        }

        return array_values($matches);
    }

    /**
     * Generate a secure link code for manual parent-student linking
     */
    public function generateLinkCode(Student $student): string
    {
        $code = strtoupper(substr(md5(uniqid($student->id, true)), 0, 8));
        
        // Store in student extra data with expiry
        $extra = $student->extra ?? [];
        $extra['link_code'] = $code;
        $extra['link_code_expires'] = now()->addDays(7)->toDateTimeString();
        $student->update(['extra' => $extra]);

        return $code;
    }

    /**
     * Verify and consume link code
     */
    public function verifyLinkCode(string $code): ?Student
    {
        $student = Student::whereRaw("JSON_EXTRACT(extra, '$.link_code') = ?", [$code])->first();

        if (!$student) {
            return null;
        }

        $extra = $student->extra ?? [];
        $expiresAt = $extra['link_code_expires'] ?? null;

        if (!$expiresAt || now()->isAfter($expiresAt)) {
            return null;
        }

        return $student;
    }

    /**
     * Link parent to student with audit trail
     */
    public function linkParentToStudent(
        ParentModel $parent,
        Student $student,
        bool $isPrimary = false,
        ?string $method = null
    ): void {
        $parent->linkToStudent($student, $isPrimary);

        // Log the linking action
        app(AuditLogger::class)->log(
            auth()->user(),
            'parent_student_link',
            'ParentStudent',
            $student->id,
            [
                'parent_id' => $parent->id,
                'student_id' => $student->id,
                'is_primary' => $isPrimary,
                'method' => $method,
            ]
        );
    }
}
