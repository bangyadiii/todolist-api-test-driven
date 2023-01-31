<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TodoList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\CreatesApplication;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase, CreatesApplication;


    protected function setUp(): void
    {
        parent::setUp();
        $this->createAuthUser();
        Sanctum::actingAs($this->authUser);
        $this->list = $this->createTodo();
        $this->task = $this->createTask();
    }

    /**
     * Fecthing all task of the todo list 
     *
     * @return void
     */
    public function test_fetch_all_task_of_the_todo_list()
    {
        $todo = $this->createTodo();
        $task = $this->createTask(['todo_list_id' => $todo->id]);

        $response = $this->getJson(\route("api.todolist.task.index", $this->list->id));

        $response->assertOk();
        $data = $response->json();

        $response->assertJsonCount(1);
        $this->assertNotNull($data[0]["todolist"]);
        $this->assertEquals($data[0]["title"], $this->task->title);
        $this->assertEquals($task->todo_list_id, $todo->id);
    }

    public function test_store_a_new_task_with_invalid_data()
    {
        $payload = [
            "title" => false,
        ];
        $response = $this->postJson(\route("api.todolist.task.store", $this->list->id), $payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(["title"]);
        $this->assertDatabaseMissing("tasks", $payload);
    }

    public function test_store_a_new_task_with_empty_todolist()
    {
        $payload = [
            "title" => false,
        ];
        $response = $this->postJson(\route("api.todolist.task.store", "unknown"), $payload);

        $response->assertNotFound();
    }

    public function test_store_a_new_task_with_valid_data()
    {
        $list = $this->createTodo();
        $payload = [
            "title" => "new task",
        ];
        $response = $this->postJson(\route("api.todolist.task.store", $list->id), $payload);
        $response->assertCreated();
        $response->assertJson($payload);

        $this->assertDatabaseHas("tasks", $payload);
        $this->assertNotNull($response->json()["id"]);
    }

    public function test_update_task_with_non_exist_task()
    {
        $payload = [
            "title" => "updated title",
            "status"  => Task::CANCELLED
        ];
        $response = $this->putJson(\route("api.task.update",  'unknown'), $payload);

        $response->assertNotFound();
    }

    public function test_update_task_with_invalid_payload()
    {
        $payload = [
            "title" => false,
            "status"  => "not started yet"
        ];
        $response = $this->putJson(\route("api.task.update",  $this->task->id), $payload);

        $response->assertUnprocessable();
        $data = $response->json();
        $response->assertJsonValidationErrors(["title", "status"]);
    }


    public function test_update_task_with_valid_payload()
    {
        $payload = [
            "title" => "updated title",
            "status"  => Task::CANCELLED
        ];
        $response = $this->putJson(\route("api.task.update",  $this->task->id), $payload);

        $response->assertOk();
        $data = $response->json();
        $response->assertSimilarJson($data, $payload);
        $this->assertDatabaseHas("tasks", $payload);
    }

   
    public function test_delete_task_with_non_exist_task()
    {
        $response = $this->deleteJson(\route("api.task.destroy",  "unknown"));

        $response->assertNotFound();
    }

    public function test_delete_task_with_exist_task()
    {
        $response = $this->deleteJson(\route("api.task.destroy",  $this->task->id));

        $response->assertNoContent();

        $this->assertDatabaseMissing("tasks", ["id" => $this->task->id]);
    }
}
