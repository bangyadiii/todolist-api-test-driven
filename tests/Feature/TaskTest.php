<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Fecthing all task of the todo list 
     *
     * @return void
     */
    public function test_fetch_all_task_of_the_todo_list()
    {
        $task = Task::factory()->count(10)->create();

        $response = $this->getJson(\route("api.task.index"));

        $response->assertOk();
        $response->assertJsonCount(10);
        $this->assertEquals($response->json()[0]["title"], $task->get(0)->title);
    }

    public function test_store_a_new_task()
    {
        $data = [
            "title" => "new task"
        ];
        $response = $this->postJson(\route("api.task.store"), $data);

        $response->assertCreated();
        $response->assertJson($data);
        $this->assertNotNull($response->json()["id"]);
    }
}
