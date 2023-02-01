<?php

namespace Tests\Feature;

use App\Models\TodoList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\CreatesApplication;
use Tests\TestCase;

class TodoListTest extends TestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
        $this->createAuthUser();
        Sanctum::actingAs($this->authUser);
        $this->list = $this->createTodo(['user_id' => $this->authUser->id]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_fetch_current_user_todo_lists()
    {
        // action
        $otherUser = User::factory()->create();
        $this->createTodo(["user_id" => $otherUser->id]);
        $response = $this->getJson(route("api.todolist.index"));

        // assert
        $response->assertOk();
        $data = $response->json();

        dd($response->json());
        // Assert that the response only contains one todo list item 
        $this->assertCount(1, $data["data"]);

        // Assert that the user id of the todo list item matches the authenticated user's id 
        $this->assertEquals($this->authUser->id, $data["data"][0]["attributes"]["user_id"]);
    }

    public function test_not_found_when_show_todo_detail_with_invalid_id()
    {
        $this->withExceptionHandling();
        $this->get(route("api.todolist.show", "unknown"))
            ->assertNotFound();
    }

    public function test_show_todo_details()
    {
        $response = $this->get(route("api.todolist.show", $this->list->id))
            ->assertOk();

        $data = $response->json("data");
        $this->assertEquals($data["attributes"]["title"], $this->list->title);
        $this->assertEquals($data["attributes"]["description"], $this->list->description);
    }

    public function test_post_new_todo_list_with_valid_payload()
    {
        $todoList = TodoList::factory()->make();
        $response = $this->postJson(route("api.todolist.store", [
            "title" => $todoList->title,
            "description" => $todoList->description
        ]))
            ->assertCreated();

        $data = $response->json("data");

        $this->assertDatabaseHas("todo_lists", ["title" => $data["attributes"]["title"]]);
        $this->assertEquals($todoList->title, $data["attributes"]["title"]);
    }

    public function test_post_new_todo_list_with_invalid_payload()
    {
        $this->withExceptionHandling();
        $todoList = TodoList::factory()->make();

        $response = $this->postJson(route("api.todolist.store", [
            "description" => $todoList->description
        ]))
            ->assertUnprocessable();

        $response->assertJsonValidationErrors(["title"]);
    }

    public function test_delete_todo_list_with_invalid_id()
    {
        $this->withExceptionHandling();
        $this->deleteJson(route("api.todolist.destroy", "unknown"))
            ->assertNotFound();
    }

    public function test_delete_todo_list()
    {

        $this->deleteJson(route("api.todolist.destroy", $this->list->id))
            ->assertNoContent();
        $this->assertDatabaseMissing("todo_lists", ["id" => $this->list->id]);
    }

    public function test_update_todo_list_with_invalid_id()
    {
        $this->withExceptionHandling();
        $response = $this->putJson(\route("api.todolist.update", "unknown"), [
            "title" => "update success"
        ])
            ->assertNotFound();
    }

    public function test_update_todo_list_with_invalid_payload()
    {
        $this->withExceptionHandling();
        $response = $this->putJson(\route("api.todolist.update", $this->list->id), [
            "title" => 123,
            "description"  => "<script> const a = document.querySelector('#id'); a.addEventListener('click', ()=> console.log();); </script>"
        ])
            ->assertUnprocessable();
        $response->assertJsonValidationErrors("title");
    }

    public function test_update_todo_list_with_valid_payload()
    {
        $payload = [
            "title" => "update success"
        ];
        $response = $this->putJson(\route("api.todolist.update", $this->list->id), $payload)
            ->assertOk();

        $this->assertDatabaseHas(
            "todo_lists",
            [
                "id" => $this->list->id,
                "title" => $payload["title"]
            ]
        );
        $arr = $response->json("data");

        $response->assertJson([
            "data" => [
                "id" => $this->list->id,
                "attributes" => [
                    "title" => $arr["attributes"]["title"],
                    "description" => $arr["attributes"]["description"],
                ],
            ],
        ]);
    }

    public function test_if_todolist_deleted_and_then_task_will_be_deleted()
    {
        $response = $this->deleteJson(\route("api.todolist.destroy", $this->list->id));
        $response->assertNoContent();
        $this->assertDatabaseMissing("todo_lists", ["id" => $this->list->id]);
        $this->assertDatabaseMissing("tasks", ["todo_list_id" => $this->list->id]);
    }
}
