<?php

use App\Livewire\KanbanBoard;
use App\Models\Board;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $board = Board::first();

    if (!$board) {
        abort(404, 'No boards found');
    }

    return redirect()->route('board.kanban', $board->slug);
})->name('home');

Route::get('/board/{board:slug}', KanbanBoard::class)->name('board.kanban');
