<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'nullable|exists:users,id',
            'admission_no' => 'nullable|string|max:50',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'enrollment_date' => 'nullable|date',
            'current_class_id' => 'nullable|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'address' => 'nullable|string|max:255',
            'photo_path' => 'nullable|string|max:255',
            'extra' => 'nullable|array',
        ];
    }
}
