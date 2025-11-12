<?php

namespace Database\Factories;

use App\Models\ClassModel;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'code' => 'EXM' . fake()->unique()->numerify('####'),
            'description' => fake()->paragraph(),
            'class_id' => ClassModel::factory(),
            'section_id' => Section::factory(),
            'start_datetime' => fake()->dateTimeBetween('now', '+1 month'),
            'end_datetime' => fake()->dateTimeBetween('+1 month', '+2 months'),
            'total_marks' => fake()->numberBetween(50, 100),
            'passing_marks' => fake()->numberBetween(30, 50),
            'is_online' => fake()->boolean(),
            'created_by' => User::factory(),
            'status' => fake()->randomElement(['draft', 'published', 'closed']),
        ];
    }
}
