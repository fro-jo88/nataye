<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_code' => $this->student_code,
            'admission_no' => $this->admission_no,
            'name' => $this->full_name,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'age' => $this->age,
            'enrollment_date' => $this->enrollment_date?->format('Y-m-d'),
            'class' => $this->whenLoaded('currentClass', fn() => [
                'id' => $this->currentClass->id,
                'name' => $this->currentClass->name,
                'code' => $this->currentClass->code,
            ]),
            'section' => $this->whenLoaded('section', fn() => [
                'id' => $this->section->id,
                'name' => $this->section->name,
            ]),
            'user' => $this->whenLoaded('user', fn() => [
                'id' => $this->user->id,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
                'status' => $this->user->status,
            ]),
            'parents' => $this->whenLoaded('parents', fn() => 
                $this->parents->map(fn($parent) => [
                    'id' => $parent->id,
                    'name' => $parent->name,
                    'phone' => $parent->phone,
                    'email' => $parent->email,
                    'relation' => $parent->relation,
                    'is_primary' => $parent->pivot->is_primary,
                ])
            ),
            'address' => $this->address,
            'photo_path' => $this->photo_path,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
