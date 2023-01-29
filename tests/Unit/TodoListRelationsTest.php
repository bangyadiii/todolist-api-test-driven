<?php

namespace Tests\Unit;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;
use Tests\CreatesApplication;

class TodoListRelationsTest extends TestCase
{
    use CreatesApplication, RefreshDatabase;
    /**
     * Test task relationship.
     *
     * @return void
     */
    public function test_todolist_method_return_task_relations()
    {

        $this->createAuthUser();
        $list = $this->createTodo(["user_id" => $this->authUser->id]);
        $task = $this->createTask(["todo_list_id" => $list->id]);
        $list->fresh();
        $this->assertInstanceOf(Collection::class, $list->tasks);
        $this->assertInstanceOf(Task::class, $list->tasks->first());
        $this->assertEquals($list->tasks->first()->id, $task->id);
    }
}
