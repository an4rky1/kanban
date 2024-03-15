<div
    x-data="kanbanData"
    class="min-h-screen bg-brutal-bg"
>
    <header class="bg-neon-yellow border-b-4 border-brutal-border shadow-brutal-sm">
        <div class="max-w-[1920px] mx-auto px-6 py-5 flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-brutal-dark uppercase tracking-wider">
                    {{ $board->title }}
                </h1>
                @if ($board->description)
                    <p class="mt-1 text-base text-brutal-dark/70 font-medium">{{ $board->description }}</p>
                @endif
            </div>

            <button
                type="button"
                wire:click="openCreateColumn"
                class="bg-brutal-dark text-brutal-bg font-bold px-5 py-3 border-4 border-brutal-border shadow-brutal hover:shadow-brutal-hover hover:-translate-y-0.5 transition-all uppercase tracking-wider text-sm"
            >
                + Add Column
            </button>
        </div>
    </header>

    <div class="max-w-[1920px] mx-auto px-6 py-8">
        <div class="flex gap-6 overflow-x-auto pb-6">
            @foreach ($board->columns as $column)
                <div
                    class="flex-shrink-0 w-80"
                    x-on:dragover.prevent="onDragOver($event, {{ $column->id }})"
                    x-on:drop.prevent="onDrop($event, {{ $column->id }})"
                >
                    <div
                        class="bg-neon-yellow border-4 border-brutal-border shadow-brutal rounded-none p-4 min-h-[300px] transition-colors"
                        :class="{ 'bg-neon-green/30': dropTarget === {{ $column->id }} }"
                    >
                        <div class="flex items-center justify-between mb-4 pb-3 border-b-2 border-brutal-border/20">
                            <div class="flex items-center gap-3">
                                <h2 class="text-lg font-bold text-brutal-dark uppercase tracking-wide">
                                    {{ $column->title }}
                                </h2>
                                <span class="bg-brutal-dark text-brutal-bg font-bold text-xs px-2 py-1 border-2 border-brutal-border rounded-full">
                                    {{ $column->tasks->count() }}
                                </span>
                            </div>
                            <div class="flex items-center gap-1">
                                <button
                                    type="button"
                                    wire:click="openCreateTask({{ $column->id }})"
                                    class="bg-neon-green border-2 border-brutal-border shadow-brutal-sm font-bold text-lg w-8 h-8 flex items-center justify-center hover:shadow-brutal hover:-translate-y-0.5 transition-all"
                                    title="Add task"
                                >
                                    +
                                </button>
                                <button
                                    type="button"
                                    wire:click="openDeleteColumn({{ $column->id }})"
                                    class="bg-neon-pink border-2 border-brutal-border shadow-brutal-sm font-bold text-sm w-8 h-8 flex items-center justify-center hover:shadow-brutal hover:-translate-y-0.5 transition-all"
                                    title="Delete column"
                                >
                                    x
                                </button>
                            </div>
                        </div>

                        <div class="space-y-3 min-h-[100px]">
                            @foreach ($column->tasks as $task)
                                <div
                                    draggable="true"
                                    data-task-id="{{ $task->id }}"
                                    x-on:dragstart="onDragStart($event, {{ $task->id }}, {{ $column->id }})"
                                    x-on:dragend="onDragEnd()"
                                    class="task-card bg-{{ $task->color }} border-4 border-brutal-border shadow-brutal-sm p-4 cursor-grab active:cursor-grabbing hover:shadow-brutal hover:-translate-y-0.5 transition-all relative group"
                                    :class="{ 'opacity-40 rotate-2': dragging && dragId === {{ $task->id }} }"
                                >
                                    <button
                                        type="button"
                                        wire:click="openDeleteTask({{ $task->id }})"
                                        class="absolute top-2 right-2 bg-red-600 text-white border-2 border-brutal-border shadow-brutal-sm font-bold text-xs w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 hover:shadow-brutal hover:-translate-y-0.5 transition-all"
                                        title="Delete task"
                                    >
                                        x
                                    </button>

                                    <h3 class="font-bold text-brutal-dark mb-1 pr-6 text-sm leading-snug">
                                        {{ $task->title }}
                                    </h3>
                                    @if ($task->description)
                                        <p class="text-xs text-brutal-dark/70 line-clamp-2 leading-relaxed">
                                            {{ Str::limit($task->description, 100) }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach

                            @if ($column->tasks->isEmpty())
                                <div
                                    class="border-4 border-dashed border-brutal-border/40 p-6 text-center text-brutal-dark/40 rounded-none"
                                    x-show="dragging"
                                >
                                    <p class="font-bold text-sm uppercase">Drop here</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Column Modal --}}
    <div
        x-show="{{ $showColumnModal ? 'true' : 'false' }}"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60"
    >
        @if ($columnModalMode === 'create')
            <div class="bg-neon-yellow border-4 border-brutal-border shadow-brutal-lg p-6 w-full max-w-md mx-4">
                <h2 class="text-2xl font-bold text-brutal-dark mb-6 uppercase tracking-wider border-b-4 border-brutal-border pb-3">New Column</h2>
                <form wire:submit="createColumn">
                    <div class="mb-6">
                        <label class="block font-bold text-brutal-dark mb-2 uppercase text-sm tracking-wide">Title</label>
                        <input type="text" wire:model="newColumnTitle" placeholder="e.g. In Progress" class="w-full bg-brutal-card border-4 border-brutal-border p-3 font-bold text-brutal-dark placeholder:text-brutal-dark/30 focus:outline-none focus:shadow-brutal transition-shadow" autofocus />
                        @error('newColumnTitle')<p class="mt-2 text-sm font-bold text-red-600 bg-red-100 border-2 border-red-600 p-2">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 bg-neon-green border-4 border-brutal-border shadow-brutal font-bold uppercase py-3 tracking-wider hover:shadow-brutal-hover hover:-translate-y-0.5 transition-all">Create</button>
                        <button type="button" wire:click="closeColumnModal" class="flex-1 bg-neon-pink border-4 border-brutal-border shadow-brutal font-bold uppercase py-3 tracking-wider hover:shadow-brutal-hover hover:-translate-y-0.5 transition-all">Cancel</button>
                    </div>
                </form>
            </div>
        @else
            <div class="bg-neon-pink border-4 border-brutal-border shadow-brutal-lg p-6 w-full max-w-md mx-4">
                <h2 class="text-2xl font-bold text-brutal-dark mb-4 uppercase tracking-wider border-b-4 border-brutal-border pb-3">Delete Column</h2>
                <p class="font-bold text-brutal-dark mb-6 text-base leading-relaxed">Are you sure you want to delete "<strong>{{ $this->editingColumnTitle }}</strong>"? All tasks will be deleted.</p>
                <div class="flex gap-3">
                    <button type="button" wire:click="deleteColumn" class="flex-1 bg-red-600 text-white border-4 border-brutal-border shadow-brutal font-bold uppercase py-3 tracking-wider hover:shadow-brutal-hover hover:-translate-y-0.5 transition-all">Delete</button>
                    <button type="button" wire:click="closeColumnModal" class="flex-1 bg-brutal-card border-4 border-brutal-border shadow-brutal font-bold uppercase py-3 tracking-wider hover:shadow-brutal-hover hover:-translate-y-0.5 transition-all">Cancel</button>
                </div>
            </div>
        @endif
    </div>

    {{-- Task Modal --}}
    <div
        x-show="{{ $showTaskModal ? 'true' : 'false' }}"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60"
    >
        @if ($taskModalMode === 'create')
            <div class="bg-neon-yellow border-4 border-brutal-border shadow-brutal-lg p-6 w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto">
                <h2 class="text-2xl font-bold text-brutal-dark mb-6 uppercase tracking-wider border-b-4 border-brutal-border pb-3">New Task</h2>
                <form wire:submit="createTask">
                    <div class="mb-4">
                        <label class="block font-bold text-brutal-dark mb-2 uppercase text-sm tracking-wide">Title</label>
                        <input type="text" wire:model="newTaskTitle" placeholder="e.g. Fix login bug" class="w-full bg-brutal-card border-4 border-brutal-border p-3 font-bold text-brutal-dark placeholder:text-brutal-dark/30 focus:outline-none focus:shadow-brutal transition-shadow" autofocus />
                        @error('newTaskTitle')<p class="mt-2 text-sm font-bold text-red-600 bg-red-100 border-2 border-red-600 p-2">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="block font-bold text-brutal-dark mb-2 uppercase text-sm tracking-wide">Description</label>
                        <textarea wire:model="newTaskDescription" placeholder="Optional details..." rows="3" class="w-full bg-brutal-card border-4 border-brutal-border p-3 font-bold text-brutal-dark placeholder:text-brutal-dark/30 focus:outline-none focus:shadow-brutal transition-shadow resize-none"></textarea>
                    </div>
                    <div class="mb-6">
                        <label class="block font-bold text-brutal-dark mb-2 uppercase text-sm tracking-wide">Color</label>
                        <div class="flex gap-2 flex-wrap">
                            @foreach (['neon-yellow','neon-pink','neon-blue','neon-green','neon-purple','neon-orange'] as $c)
                                <button type="button" wire:click="$set('newTaskColor','{{ $c }}')" class="w-10 h-10 border-4 border-brutal-border shadow-brutal-sm hover:shadow-brutal hover:-translate-y-0.5 transition-all bg-{{ $c }}" :class="{ 'ring-4 ring-brutal-dark ring-offset-2': '{{ $newTaskColor }}' === '{{ $c }}' }"></button>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 bg-neon-green border-4 border-brutal-border shadow-brutal font-bold uppercase py-3 tracking-wider hover:shadow-brutal-hover hover:-translate-y-0.5 transition-all">Create</button>
                        <button type="button" wire:click="closeTaskModal" class="flex-1 bg-neon-pink border-4 border-brutal-border shadow-brutal font-bold uppercase py-3 tracking-wider hover:shadow-brutal-hover hover:-translate-y-0.5 transition-all">Cancel</button>
                    </div>
                </form>
            </div>
        @else
            <div class="bg-neon-pink border-4 border-brutal-border shadow-brutal-lg p-6 w-full max-w-md mx-4">
                <h2 class="text-2xl font-bold text-brutal-dark mb-4 uppercase tracking-wider border-b-4 border-brutal-border pb-3">Delete Task</h2>
                <p class="font-bold text-brutal-dark mb-6 text-base leading-relaxed">Are you sure you want to delete "<strong>{{ $this->editingTaskTitle }}</strong>"?</p>
                <div class="flex gap-3">
                    <button type="button" wire:click="deleteTask" class="flex-1 bg-red-600 text-white border-4 border-brutal-border shadow-brutal font-bold uppercase py-3 tracking-wider hover:shadow-brutal-hover hover:-translate-y-0.5 transition-all">Delete</button>
                    <button type="button" wire:click="closeTaskModal" class="flex-1 bg-brutal-card border-4 border-brutal-border shadow-brutal font-bold uppercase py-3 tracking-wider hover:shadow-brutal-hover hover:-translate-y-0.5 transition-all">Cancel</button>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('kanbanData', () => ({
        dragId: null,
        fromCol: null,
        dropTarget: null,
        dragging: false,

        onDragStart(event, taskId, columnId) {
            this.dragId = taskId;
            this.fromCol = columnId;
            this.dragging = true;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', taskId + ':' + columnId);
        },

        onDragEnd() {
            this.dragId = null;
            this.fromCol = null;
            this.dropTarget = null;
            this.dragging = false;
        },

        onDragOver(event, columnId) {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';
            this.dropTarget = columnId;
        },

        onDrop(event, targetColumnId) {
            event.preventDefault();
            this.dropTarget = null;

            const data = event.dataTransfer.getData('text/plain');
            if (!data || !data.includes(':')) return;

            const parts = data.split(':');
            const taskId = parseInt(parts[0]);
            const fromColumnId = parseInt(parts[1]);
            if (!taskId) return;

            const card = event.target.closest('.task-card');
            const targetTaskId = card ? parseInt(card.dataset.taskId) : null;

            this.$wire.moveTask(taskId, fromColumnId, targetColumnId, targetTaskId);
            this.onDragEnd();
        }
    }));
});
</script>
