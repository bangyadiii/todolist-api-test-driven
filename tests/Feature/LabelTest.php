<?php

namespace Tests\Feature;

use App\Models\Label;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\CreatesApplication;
use Tests\TestCase;

class LabelTest extends TestCase
{
    use RefreshDatabase, CreatesApplication;

    private Label $label;
    protected function setUp(): void
    {
        parent::setUp();
        $this->createAuthUser();
        $this->label = $this->createLabel();
        Sanctum::actingAs($this->authUser);
    }

    public function test_user_cannot_create_new_label_with_invalid_payload()
    {
        $payload  = [
            "title" => true,
            "color" => "aslad",
        ];

        $response = $this->postJson(route("api.labels.store"), $payload)
            ->assertUnprocessable();
        $data = $response->json();

        $this->assertDatabaseMissing("labels", [
            "title" => $payload["title"],
            "color" => $payload["color"],
        ]);
    }

    public function test_user_can_create_new_label()
    {
        $label = Label::factory()->raw();
        $response = $this->postJson(route("api.labels.store"), $label)
            ->assertCreated();

        $data = $response->json();
        $this->assertDatabaseHas("labels", [
            "title" => $data["title"],
            "color" => $data["color"],
            "id" => $data["id"]
        ]);
    }

    public function test_user_cannot_delete_label_with_non_exist_label()
    {
        $response = $this->deleteJson(route("api.labels.destroy", "unknown"))
            ->assertNotFound();
    }

    public function test_user_can_delete_label()
    {
        $response = $this->deleteJson(route("api.labels.destroy", $this->label->id))
            ->assertOk();

        $this->assertDatabaseMissing("labels", [
            "id" => $this->label->id
        ]);
    }
}
