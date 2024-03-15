<?php

namespace Database\Factories;

use App\Models\Board;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Column>
 */
class ColumnFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->randomElement(['To Do', 'In Progress', 'Review', 'Done']),
            'board_id' => Board::factory(),
            'position' => fake()->numberBetween(0, 100),
        ];
    }
}
