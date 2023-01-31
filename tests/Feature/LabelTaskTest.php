<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\CreatesApplication;
use Tests\TestCase;

class LabelTaskTest extends TestCase
{
    use RefreshDatabase, CreatesApplication;
    protected $label;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createAuthUser();
        $this->list = $this->createTodo();
        $this->task = $this->createTask();
        $this->label = $this->createLabel();
    }
    public function test_cannot_attach_when_authenticated()
    {
        $response = $this->postJson(\route("api.tasklabel.store", [
            "taskId" => $this->task->id,
            "labelId" => $this->label->id,
        ]))->assertUnauthorized();
    }

    public function test_user_cannot_attach_labels_to_the_non_existing_task()
    {
        Sanctum::actingAs($this->authUser);
        $response = $this->postJson(\route("api.tasklabel.store", [
            "taskId" => "unknown",
            "labelId" => $this->label->id,
        ]))->assertNotFound();
    }

    public function test_user_cannot_attach_labels_to_the_task_with_non_exist_label()
    {
        Sanctum::actingAs($this->authUser);
        $response = $this->postJson(\route("api.tasklabel.store", [
            "taskId" => $this->label->id,
            "labelId" => "unknown",
        ]))->assertNotFound();
    }

    public function test_user_can_attach_labels_to_the_task()
    {
        Sanctum::actingAs($this->authUser);
        $response = $this->post(\route("api.tasklabel.store", [
            "taskId" => $this->task->id,
            "labelId" => $this->label->id,
        ]))->assertOk();

        $this->assertDatabaseHas("label_tasks", [
            "task_id" => $this->task->id,
            "label_id" => $this->label->id
        ]);
    }

    public function test_user_cannot_detach_labels_to_the_non_existing_task()
    {
        Sanctum::actingAs($this->authUser);
        $response = $this->deleteJson(\route("api.tasklabel.destroy", [
            "taskId" => "unknown",
            "labelId" => $this->label->id,
        ]))->assertNotFound();
    }

    public function test_user_can_detach_labels_to_the_task()
    {
        Sanctum::actingAs($this->authUser);
        $response = $this->deleteJson(\route("api.tasklabel.destroy", [
            "taskId" => $this->task->id,
            "labelId" => $this->label->id,
        ]))->assertOk();

        $this->assertDatabaseMissing("label_tasks", [
            "task_id" => $this->task->id,
            "label_id" => $this->label->id
        ]);
    }
}
