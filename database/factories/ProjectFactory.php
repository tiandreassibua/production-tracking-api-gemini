<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Proyek Interior ' . fake()->city(),
            'description' => fake()->paragraph(),
            'start_date' => fake()->dateTimeBetween('-1 month', '+1 week'),
            'due_date' => fake()->dateTimeBetween('+2 months', '+6 months'),
            'progress' => 0,
        ];
    }
}
