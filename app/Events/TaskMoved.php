<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskMoved implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Task $task,
        public int $fromColumnId,
        public int $toColumnId,
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('board.' . $this->task->column->board_id);
    }

    public function broadcastAs(): string
    {
        return 'task.moved';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->task->id,
            'title' => $this->task->title,
            'description' => $this->task->description,
            'column_id' => $this->toColumnId,
            'from_column_id' => $this->fromColumnId,
            'position' => $this->task->position,
            'color' => $this->task->color,
        ];
    }
}
