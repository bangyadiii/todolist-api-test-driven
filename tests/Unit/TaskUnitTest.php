<?php

namespace Tests\Unit;

use App\Models\TodoList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\CreatesApplication;

class TaskUnitTest extends TestCase
{
    use CreatesApplication, RefreshDatabase;
    /**
     * Test task relationship.
     *
     * @return void
     */
    public function test_todolist_method_return_todolist_relations()
    {
        $list = $this->createTodo();
        $task = $this->createTask(["todo_list_id" => $list->id]);

        $list->fresh();

        $this->assertInstanceOf(TodoList::class, $task->todolist);
        $this->assertEquals($list->id, $task->todolist->id);
    }
}
