<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'code' => $this->code,
            'description' => $this->description,
            'status' => $this->status,
            'is_online' => $this->is_online,
            'start_datetime' => $this->start_datetime?->toIso8601String(),
            'end_datetime' => $this->end_datetime?->toIso8601String(),
            'total_marks' => $this->total_marks,
            'passing_marks' => $this->passing_marks,
            'is_active' => $this->isActive(),
            'class' => $this->whenLoaded('class', fn() => [
                'id' => $this->class->id,
                'name' => $this->class->name,
            ]),
            'section' => $this->whenLoaded('section', fn() => [
                'id' => $this->section->id,
                'name' => $this->section->name,
            ]),
            'questions' => $this->whenLoaded('questions', fn() =>
                $this->questions->map(fn($q) => [
                    'id' => $q->id,
                    'type' => $q->type,
                    'question_text' => $q->question_text,
                    'options' => $q->options,
                    'marks' => $q->marks,
                ])
            ),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
