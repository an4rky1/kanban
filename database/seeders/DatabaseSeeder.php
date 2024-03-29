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
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => 'password']
        );

        $board = Board::firstOrCreate(
            ['slug' => 'my-kanban-board'],
            [
                'user_id' => $user->id,
                'title' => 'My Kanban Board',
                'description' => null,
            ]
        );

        $columnsData = [
            ['title' => 'To Do', 'position' => 0],
            ['title' => 'In Progress', 'position' => 1],
            ['title' => 'Review', 'position' => 2],
            ['title' => 'Done', 'position' => 3],
        ];

        $tasks = [
            'To Do' => [
                ['title' => 'Design homepage layout', 'description' => 'Create wireframe and final design in Figma', 'color' => 'neon-yellow'],
                ['title' => 'Set up CI/CD pipeline', 'description' => 'GitHub Actions for automated deployment', 'color' => 'neon-pink'],
                ['title' => 'Write API documentation', 'description' => 'OpenAPI specification for all endpoints', 'color' => 'neon-blue'],
                ['title' => 'Code review PR #42', 'description' => '', 'color' => 'neon-green'],
            ],
            'In Progress' => [
                ['title' => 'Implement OAuth authorization', 'description' => 'Google and GitHub providers', 'color' => 'neon-purple'],
                ['title' => 'Optimize database queries', 'description' => 'Add indexes and eager loading', 'color' => 'neon-orange'],
                ['title' => 'Integrate WebSocket notifications', 'description' => 'Laravel Reverb for real-time updates', 'color' => 'neon-yellow'],
                ['title' => 'Build responsive layout', 'description' => 'Mobile-first approach with Tailwind', 'color' => 'neon-pink'],
            ],
            'Review' => [
                ['title' => 'Write unit tests for models', 'description' => 'Minimum 80% coverage', 'color' => 'neon-blue'],
                ['title' => 'Add error logging', 'description' => 'Sentry integration', 'color' => 'neon-green'],
                ['title' => 'Set up Redis caching', 'description' => 'Cache API responses for 5 minutes', 'color' => 'neon-purple'],
                ['title' => 'Check accessibility (a11y)', 'description' => 'ARIA attributes and keyboard navigation', 'color' => 'neon-orange'],
            ],
            'Done' => [
                ['title' => 'Create database migrations', 'description' => 'Tables: users, boards, columns, tasks', 'color' => 'neon-yellow'],
                ['title' => 'Configure Tailwind theme', 'description' => 'Neon palette and custom shadows', 'color' => 'neon-pink'],
                ['title' => 'Deploy staging environment', 'description' => 'Docker + VPS', 'color' => 'neon-blue'],
                ['title' => 'Integrate Laravel Reverb', 'description' => 'WebSockets for real-time updates', 'color' => 'neon-green'],
            ],
        ];

        foreach ($columnsData as $colData) {
            $column = Column::firstOrCreate(
                ['board_id' => $board->id, 'title' => $colData['title']],
                ['position' => $colData['position']]
            );

            $columnTasks = $tasks[$colData['title']] ?? [];
            foreach ($columnTasks as $index => $taskData) {
                Task::firstOrCreate(
                    ['column_id' => $column->id, 'title' => $taskData['title']],
                    [
                        'description' => $taskData['description'],
                        'color' => $taskData['color'],
                        'position' => $index * 10,
                    ]
                );
            }
        }
    }
}
