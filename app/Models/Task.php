<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'column_id',
        'position',
        'color',
    ];

    public function column(): BelongsTo
    {
        return $this->belongsTo(Column::class);
    }
}
