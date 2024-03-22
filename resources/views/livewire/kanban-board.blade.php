<div
    x-data="{
        showColModal: @entangle('showColumnModal'),
        showTaskModal: @entangle('showTaskModal'),
        dragId: null,
        fromCol: null,
        dropTarget: null,
        dragging: false,

        dragStart(e, taskId, colId) {
            this.dragId = taskId;
            this.fromCol = colId;
            this.dragging = true;
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', taskId + ':' + colId);
        },

        dragEnd() {
            this.dragId = null;
            this.fromCol = null;
            this.dropTarget = null;
            this.dragging = false;
        },

        dragOver(e, colId) {
            e.preventDefault();
            this.dropTarget = colId;
        },

        drop(e, targetColId) {
            e.preventDefault();
            this.dropTarget = null;
            const raw = e.dataTransfer.getData('text/plain');
            if (!raw || !raw.includes(':')) return;
            const [tid, fid] = raw.split(':').map(Number);
            if (!tid) return;
            const card = e.target.closest('.task-card');
            const targetTid = card ? parseInt(card.dataset.taskId) : null;
            @this.call('moveTask', tid, fid, targetColId, targetTid);
            this.dragEnd();
        }
    }"
    class="min-h-screen"
>
    {{-- Header --}}
    <header class="bg-neon-yellow border-b-4 border-brutal-border shadow-brutal-sm">
        <div class="max-w-7xl mx-auto px-6 py-5 flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-brutal-dark uppercase tracking-wider">
                    {{ $board->title }}
                </h1>
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

    {{-- Board --}}
    <div class="max-w-7xl mx-auto px-6 py-10">
        <div class="flex gap-6 justify-center flex-wrap">
            @foreach ($board->columns as $column)
                <div
                    class="w-80"
                    x-on:dragover.prevent="dragOver($event, {{ $column->id }})"
                    x-on:drop.prevent="drop($event, {{ $column->id }})"
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
                                    x-on:dragstart="dragStart($event, {{ $task->id }}, {{ $column->id }})"
                                    x-on:dragend="dragEnd()"
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
        x-show="showColModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60"
    >
        @if ($columnModalMode === 'create')
            <div class="bg-neon-yellow border-4 border-brutal-border shadow-brutal-lg p-6 w-full max-w-md mx-4">
                <h2 class="text-2xl font-bold text-brutal-dark mb-6 uppercase tracking-wider border-b-4 border-brutal-border pb-3">New Column</h2>
                <form wire:submit="createColumn">
                    <div class="mb-6">
                        <label class="block font-bold text-brutal-dark mb-2 uppercase text-sm tracking-wide">Title</label>
                        <input type="text" wire:model.live="newColumnTitle" placeholder="e.g. In Progress" class="w-full bg-brutal-card border-4 border-brutal-border p-3 font-bold text-brutal-dark placeholder:text-brutal-dark/30 focus:outline-none focus:shadow-brutal transition-shadow" autofocus />
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
                <p class="font-bold text-brutal-dark mb-6 text-base leading-relaxed">Are you sure you want to delete "<strong>{{ $this->getEditingColumnTitle() }}</strong>"? All tasks will be deleted.</p>
                <div class="flex gap-3">
                    <button type="button" wire:click="deleteColumn" class="flex-1 bg-red-600 text-white border-4 border-brutal-border shadow-brutal font-bold uppercase py-3 tracking-wider hover:shadow-brutal-hover hover:-translate-y-0.5 transition-all">Delete</button>
                    <button type="button" wire:click="closeColumnModal" class="flex-1 bg-brutal-card border-4 border-brutal-border shadow-brutal font-bold uppercase py-3 tracking-wider hover:shadow-brutal-hover hover:-translate-y-0.5 transition-all">Cancel</button>
                </div>
            </div>
        @endif
    </div>

    {{-- Task Modal --}}
    <div
        x-show="showTaskModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60"
    >
        @if ($taskModalMode === 'create')
            <div class="bg-neon-yellow border-4 border-brutal-border shadow-brutal-lg p-6 w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto">
                <h2 class="text-2xl font-bold text-brutal-dark mb-6 uppercase tracking-wider border-b-4 border-brutal-border pb-3">New Task</h2>
                <form wire:submit="createTask">
                    <div class="mb-4">
                        <label class="block font-bold text-brutal-dark mb-2 uppercase text-sm tracking-wide">Title</label>
                        <input type="text" wire:model.live="newTaskTitle" placeholder="e.g. Fix auth bug" class="w-full bg-brutal-card border-4 border-brutal-border p-3 font-bold text-brutal-dark placeholder:text-brutal-dark/30 focus:outline-none focus:shadow-brutal transition-shadow" autofocus />
                        @error('newTaskTitle')<p class="mt-2 text-sm font-bold text-red-600 bg-red-100 border-2 border-red-600 p-2">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="block font-bold text-brutal-dark mb-2 uppercase text-sm tracking-wide">Description</label>
                        <textarea wire:model.live="newTaskDescription" placeholder="Optional..." rows="3" class="w-full bg-brutal-card border-4 border-brutal-border p-3 font-bold text-brutal-dark placeholder:text-brutal-dark/30 focus:outline-none focus:shadow-brutal transition-shadow resize-none"></textarea>
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
                <p class="font-bold text-brutal-dark mb-6 text-base leading-relaxed">Are you sure you want to delete "<strong>{{ $this->getEditingTaskTitle() }}</strong>"?</p>
                <div class="flex gap-3">
                    <button type="button" wire:click="deleteTask" class="flex-1 bg-red-600 text-white border-4 border-brutal-border shadow-brutal font-bold uppercase py-3 tracking-wider hover:shadow-brutal-hover hover:-translate-y-0.5 transition-all">Delete</button>
                    <button type="button" wire:click="closeTaskModal" class="flex-1 bg-brutal-card border-4 border-brutal-border shadow-brutal font-bold uppercase py-3 tracking-wider hover:shadow-brutal-hover hover:-translate-y-0.5 transition-all">Cancel</button>
                </div>
            </div>
        @endif
    </div>
</div>
