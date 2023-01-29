<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesApplication;
use Tests\TestCase;

class TodoListUnitTest extends TestCase
{
    use CreatesApplication, RefreshDatabase;

    public function test_if_todolist_deleted_and_then_task_will_be_deleted()
    {
        $this->createAuthUser();
        $list = $this->createTodo(["user_id" => $this->authUser->id]);
        $list2 = $this->createTodo();
        $task = $this->createTask(["todo_list_id" => $list->id]);
        $task2 = $this->createTask(["todo_list_id" => $list2->id]);
        $list->delete();

        $this->assertDatabaseMissing("todo_lists", ["id" => $list->id]);
        $this->assertDatabaseMissing("tasks", ["id" => $task->id, "todo_list_id" => $list->id]);
        $this->assertDatabaseHas("tasks", ["id" => $task2->id]);
    }
}
