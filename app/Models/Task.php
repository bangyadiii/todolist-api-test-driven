<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        "title", "todo_list_id"
    ];

    public function todolist()
    {
        return $this->belongsTo(TodoList::class, "todo_list_id");
    }
}
