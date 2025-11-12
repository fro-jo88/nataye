<?php

namespace Database\Factories;

use App\Models\ClassModel;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'student_code' => 'STD' . fake()->unique()->numerify('######'),
            'admission_no' => 'ADM' . fake()->unique()->numerify('######'),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'date_of_birth' => fake()->dateTimeBetween('-15 years', '-5 years'),
            'enrollment_date' => fake()->dateTimeBetween('-3 years', 'now'),
            'current_class_id' => ClassModel::factory(),
            'section_id' => Section::factory(),
            'address' => fake()->address(),
            'extra' => [],
        ];
    }
}
