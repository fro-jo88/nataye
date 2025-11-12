<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClassFactory extends Factory
{
    protected $model = \App\Models\ClassModel::class;

    public function definition(): array
    {
        return [
            'name' => 'Grade ' . fake()->numberBetween(1, 12),
            'code' => 'G' . fake()->unique()->numberBetween(1, 12),
            'description' => fake()->sentence(),
        ];
    }
}
