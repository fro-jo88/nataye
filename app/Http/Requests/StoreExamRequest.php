<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|unique:exams,code',
            'description' => 'nullable|string',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'start_datetime' => 'nullable|date',
            'end_datetime' => 'nullable|date|after:start_datetime',
            'total_marks' => 'required|numeric|min:0',
            'passing_marks' => 'required|numeric|min:0',
            'is_online' => 'boolean',
            'status' => 'in:draft,published,closed',
        ];
    }
}
