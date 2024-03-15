<?php

namespace Database\Factories;

use App\Models\Column;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'column_id' => Column::factory(),
            'position' => fake()->numberBetween(0, 100),
            'color' => fake()->randomElement([
                'neon-yellow',
                'neon-pink',
                'neon-blue',
                'neon-green',
                'neon-purple',
                'neon-orange',
            ]),
        ];
    }
}
