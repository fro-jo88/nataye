<?php

namespace Database\Factories;

use App\Models\ClassModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class SectionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'class_id' => ClassModel::factory(),
            'name' => fake()->randomElement(['A', 'B', 'C', 'D']),
            'capacity' => fake()->numberBetween(20, 40),
        ];
    }
}
