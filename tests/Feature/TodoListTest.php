<?php

namespace Tests\Feature;

use App\Models\TodoList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\CreatesApplication;
use Tests\TestCase;

class TodoListTest extends TestCase
{
    use CreatesApplication, RefreshDatabase;
    private $list;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
        $this->list = TodoList::factory()->create();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_todo_list()
    {
        // action
        $response = $this->get(route("api.todolist.index"));

        // assert
        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json()));
    }

    public function test_not_found_when_show_todo_detail_with_invalid_id()
    {
        $this->withExceptionHandling();
        $response = $this->get(route("api.todolist.show", "unknown"))
            ->assertNotFound();
    }

    public function test_show_todo_details()
    {
        $response = $this->get(route("api.todolist.show", $this->list->id))
            ->assertOk()
            ->json();

        $this->assertEquals($response["title"], $this->list->title);
        $this->assertEquals($response["description"], $this->list->description);
    }

    public function test_post_new_todo_list_with_valid_payload()
    {
        $todoList = TodoList::factory()->make();
        $response = $this->postJson(route("api.todolist.store", [
            "title" => $todoList->title,
            "description" => $todoList->description
        ]))
            ->assertCreated()
            ->json();

        $this->assertDatabaseHas("todo_lists", ["title" => $response["title"]]);
        $this->assertEquals($todoList->title, $response["title"]);
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
        $this->deleteJson(route("api.todolist.destroy", 120000000))
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
        $response = $this->putJson(\route("api.todolist.update", $this->list->id), [
            "title" => "update success"
        ])
            ->assertOk();
        $this->assertDatabaseHas(
            "todo_lists",
            [
                "id" => $this->list->id,
                "title" => "update success"
            ]
        );
        $arr = $response->json();

        $response->assertJson([
            "id" => $this->list->id,
            "title" => $arr["title"],
            "description" => $arr["description"],
        ]);
    }
}
