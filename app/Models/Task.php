<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    use HasFactory;
    public const NOT_STARTED = "not_started";
    public const STARTED = "started";
    public const PENDING = "pending";
    public const CANCELLED = "cancelled";

    protected $fillable = [
        "title", "todo_list_id", "status"
    ];

    public function todolist(): BelongsTo
    {
        return $this->belongsTo(TodoList::class, "todo_list_id");
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class, "label_task");
    }
}
