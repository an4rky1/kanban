<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\Column;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $board = Board::factory()->for($user)->create([
            'title' => 'My Kanban Board',
            'slug' => 'my-kanban-board',
        ]);

        $columns = [
            Column::factory()->for($board)->create(['title' => 'To Do', 'position' => 0]),
            Column::factory()->for($board)->create(['title' => 'In Progress', 'position' => 1]),
            Column::factory()->for($board)->create(['title' => 'Review', 'position' => 2]),
            Column::factory()->for($board)->create(['title' => 'Done', 'position' => 3]),
        ];

        foreach ($columns as $index => $column) {
            Task::factory()->count(4)->for($column)->create([
                'position' => $index * 10,
            ]);
        }
    }
}
