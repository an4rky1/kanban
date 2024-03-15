<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Board>
 */
class BoardFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->words(2, true);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->optional()->sentence(),
            'user_id' => User::factory(),
        ];
    }
}
