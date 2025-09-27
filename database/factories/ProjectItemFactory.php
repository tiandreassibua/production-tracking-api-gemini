<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectItemFactory extends Factory
{
    public function definition(): array
    {
        $items = ['Kitchen Set', 'Lemari Tanam', 'Meja Rias', 'Partisi Ruangan', 'Backdrop TV'];
        return [
            'name' => fake()->randomElement($items) . ' ' . fake()->colorName(),
            'description' => 'Dibuat dengan bahan multiplek dan finishing HPL.',
            'progress' => 0,
            'status' => 'Pending',
        ];
    }
}
