<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date->format('Y-m-d'),
            'status' => $this->status,
            'class' => $this->whenLoaded('class', fn() => [
                'id' => $this->class->id,
                'name' => $this->class->name,
            ]),
            'section' => $this->whenLoaded('section', fn() => [
                'id' => $this->section->id,
                'name' => $this->section->name,
            ]),
            'subject' => $this->whenLoaded('subject', fn() => [
                'id' => $this->subject->id,
                'name' => $this->subject->name,
            ]),
            'teacher' => $this->whenLoaded('teacher', fn() => [
                'id' => $this->teacher->id,
                'name' => $this->teacher->full_name,
            ]),
            'attendances' => $this->whenLoaded('attendances', fn() =>
                $this->attendances->map(fn($att) => [
                    'student_id' => $att->student_id,
                    'student_name' => $att->student->full_name,
                    'status' => $att->status,
                    'notes' => $att->notes,
                    'recorded_at' => $att->recorded_at->toIso8601String(),
                ])
            ),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
