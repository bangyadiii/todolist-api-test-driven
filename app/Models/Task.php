<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        "title", "todo_list_id"
    ];

    public function todolist(): BelongsTo
    {
        return $this->belongsTo(TodoList::class, "todo_list_id");
    }
}
