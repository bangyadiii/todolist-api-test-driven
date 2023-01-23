<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private $task;

    public function setUp(): void
    {
        parent::setUp();
        $this->task = $this->createTask();
    }

    /**
     * Fecthing all task of the todo list 
     *
     * @return void
     */
    public function test_fetch_all_task_of_the_todo_list()
    {
        $response = $this->getJson(\route("api.task.index"));

        $response->assertOk();
        $response->assertJsonCount(1);
        $this->assertEquals($response->json()[0]["title"], $this->task->title);
    }

    public function test_store_a_new_task_with_invalid_data()
    {
        $payload = [
            "title" => false
        ];
        $response = $this->postJson(\route("api.task.store"), $payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(["title"]);
        $this->assertDatabaseMissing("tasks", $payload);
    }

    public function test_store_a_new_task_with_valid_data()
    {
        $payload = [
            "title" => "new task"
        ];
        $response = $this->postJson(\route("api.task.store"), $payload);

        $response->assertCreated();
        $response->assertJson($payload);

        $this->assertDatabaseHas("tasks", $payload);
        $this->assertNotNull($response->json()["id"]);
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

    private function createTask($args = [])
    {
        if (isset($args)) {
            return Task::factory()->create($args);
        }

        return Task::factory()->create();
    }
}
