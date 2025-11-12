<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->jobTitle(),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->sentence(),
        ];
    }
}
