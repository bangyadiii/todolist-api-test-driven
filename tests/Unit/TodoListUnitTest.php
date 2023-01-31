<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesApplication;
use Tests\TestCase;

class TodoListUnitTest extends TestCase
{
    use CreatesApplication, RefreshDatabase;

    public function test_if_todolist_deleted_then_associated_tasks_will_be_deleted()
    {
        $this->createAuthUser();

        // Create two todo lists and associated tasks. 
        $list = $this->createTodo(["user_id" => $this->authUser->id]);  // List 1 
        $task = $this->createTask(["todo_list_id" => $list->id]);      // Task 1

        $list2 = $this->createTodo();                                 // List 2 
        $task2 = $this->createTask(["todo_list_id" => $list2->id]);    // Task 2

        // Delete list 1. 
        $list->delete();

        // Assert that list 1 and its associated task have been deleted. 
        $this->assertDatabaseMissing("todo_lists", ["id" => $list->id]);   // List 1 deleted. 
        $this->assertDatabaseMissing("tasks", ["id" => $task->id, "todo_list_id" => $list->id]);   // Task 1 deleted. 

        // Assert that list 2 and its associated task have not been deleted. 
        $this->assertDatabaseHas("tasks", ["id" => $task2->id]);   // Task 2 still exists.  

    }
}
