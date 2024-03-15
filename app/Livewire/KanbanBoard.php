<?php

namespace App\Livewire;

use App\Events\TaskMoved;
use App\Models\Board;
use App\Models\Column;
use App\Models\Task;
use Livewire\Component;

class KanbanBoard extends Component
{
    public Board $board;

    public bool $showColumnModal = false;
    public string $columnModalMode = 'create';
    public ?int $columnToDelete = null;
    public string $newColumnTitle = '';

    public bool $showTaskModal = false;
    public string $taskModalMode = 'create';
    public ?int $taskColumnId = null;
    public ?int $taskToDelete = null;
    public string $newTaskTitle = '';
    public ?string $newTaskDescription = null;
    public string $newTaskColor = 'neon-yellow';

    public function mount(Board $board): void
    {
        $this->board = $board->load('columns.tasks');
    }

    public function openCreateColumn(): void
    {
        $this->columnModalMode = 'create';
        $this->newColumnTitle = '';
        $this->showColumnModal = true;
    }

    public function openDeleteColumn(int $columnId): void
    {
        $this->columnModalMode = 'delete';
        $this->columnToDelete = $columnId;
        $this->showColumnModal = true;
    }

    public function closeColumnModal(): void
    {
        $this->showColumnModal = false;
        $this->columnToDelete = null;
        $this->newColumnTitle = '';
    }

    public function createColumn(): void
    {
        $this->validate(['newColumnTitle' => 'required|string|max:255']);

        $maxPosition = $this->board->columns()->max('position') ?? 0;
        $this->board->columns()->create([
            'title' => $this->newColumnTitle,
            'position' => $maxPosition + 10,
        ]);

        $this->closeColumnModal();
        $this->board->refresh();
    }

    public function deleteColumn(): void
    {
        if ($this->columnToDelete) {
            Column::find($this->columnToDelete)?->delete();
            $this->closeColumnModal();
            $this->board->refresh();
        }
    }

    public function openCreateTask(int $columnId): void
    {
        $this->taskModalMode = 'create';
        $this->taskColumnId = $columnId;
        $this->newTaskTitle = '';
        $this->newTaskDescription = null;
        $this->newTaskColor = 'neon-yellow';
        $this->showTaskModal = true;
    }

    public function openDeleteTask(int $taskId): void
    {
        $this->taskModalMode = 'delete';
        $this->taskToDelete = $taskId;
        $this->showTaskModal = true;
    }

    public function closeTaskModal(): void
    {
        $this->showTaskModal = false;
        $this->taskColumnId = null;
        $this->taskToDelete = null;
        $this->newTaskTitle = '';
        $this->newTaskDescription = null;
    }

    public function createTask(): void
    {
        $this->validate([
            'newTaskTitle' => 'required|string|max:255',
            'newTaskDescription' => 'nullable|string',
            'newTaskColor' => 'required|string',
        ]);

        if (!$this->taskColumnId) {
            return;
        }

        $column = Column::find($this->taskColumnId);
        if (!$column) {
            return;
        }

        $maxPosition = $column->tasks()->max('position') ?? 0;
        $column->tasks()->create([
            'title' => $this->newTaskTitle,
            'description' => $this->newTaskDescription,
            'position' => $maxPosition + 10,
            'color' => $this->newTaskColor,
        ]);

        $this->closeTaskModal();
        $this->board->refresh();
    }

    public function deleteTask(): void
    {
        if ($this->taskToDelete) {
            Task::find($this->taskToDelete)?->delete();
            $this->closeTaskModal();
            $this->board->refresh();
        }
    }

    public function moveTask(int $taskId, int $fromColumnId, int $toColumnId, ?int $targetTaskId = null): void
    {
        $task = Task::find($taskId);
        if (!$task) {
            return;
        }

        $targetColumn = Column::find($toColumnId);
        if (!$targetColumn) {
            return;
        }

        $tasksInColumn = Task::where('column_id', $toColumnId)
            ->where('id', '!=', $task->id)
            ->orderBy('position')
            ->get();

        if ($targetTaskId) {
            $targetPosition = $tasksInColumn->firstWhere('id', $targetTaskId)?->position ?? 0;
            $task->update([
                'column_id' => $toColumnId,
                'position' => $targetPosition,
            ]);

            Task::where('column_id', $toColumnId)
                ->where('id', '!=', $task->id)
                ->where('position', '>=', $targetPosition)
                ->increment('position');
        } else {
            $maxPosition = $tasksInColumn->max('position') ?? 0;
            $task->update([
                'column_id' => $toColumnId,
                'position' => $maxPosition + 10,
            ]);
        }

        $this->normalizePositions($toColumnId);
        $this->normalizePositions($fromColumnId);

        $task->refresh();
        event(new TaskMoved($task, $fromColumnId, $toColumnId));
        $this->board->refresh();
    }

    protected function normalizePositions(int $columnId): void
    {
        $tasks = Task::where('column_id', $columnId)
            ->orderBy('position')
            ->get();

        foreach ($tasks as $index => $task) {
            $task->timestamps = false;
            $task->update(['position' => $index * 10]);
            $task->timestamps = true;
        }
    }

    public function getEditingColumnTitle(): ?string
    {
        if ($this->columnToDelete) {
            return Column::find($this->columnToDelete)?->title;
        }
        return null;
    }

    public function getEditingTaskTitle(): ?string
    {
        if ($this->taskToDelete) {
            return Task::find($this->taskToDelete)?->title;
        }
        return null;
    }

    public function render()
    {
        $this->board->load('columns.tasks');

        return view('livewire.kanban-board');
    }
}
