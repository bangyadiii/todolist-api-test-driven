<?php

namespace Tests\Feature;

use App\Models\Label;
use App\Models\User;
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
        $this->label = $this->createLabel([
            "user_id" => $this->authUser->id
        ]);
    }
    public function test_unauthorized_user_has_no_access()
    {
        $response = $this->getJson(\route("api.labels.index"))
            ->assertUnauthorized();
    }

    public function test_fetch_all_label()
    {
        $this->actingAs($this->authUser);
        $user = User::factory()->create();
        $this->createLabel(["user_id" => $user->id]);
        $response = $this->getJson(\route("api.labels.index"))
            ->assertOk();
        $response->assertJsonCount(1);
    }

    public function test_user_cannot_create_new_label_with_invalid_payload()
    {
        $payload  = [
            "title" => true,
            "color" => "aslad",
        ];

        $response = $this->actingAs($this->authUser)->postJson(route("api.labels.store"), $payload)
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

        $this->actingAs($this->authUser);
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
        $this->actingAs($this->authUser);
        $response = $this->deleteJson(route("api.labels.destroy", "unknown"))
            ->assertNotFound();
    }

    public function test_user_can_delete_label()
    {

        $this->actingAs($this->authUser);
        $response = $this->deleteJson(route("api.labels.destroy", $this->label->id))
            ->assertOk();

        $this->assertDatabaseMissing("labels", [
            "id" => $this->label->id
        ]);
    }

    public function test_user_can_update_label()
    {
        $payload = [
            "title" => "update title"
        ];

        $this->actingAs($this->authUser);
        $response = $this->putJson(route("api.labels.update", $this->label->id), $payload)
            ->assertOk();

        $this->assertDatabaseHas("labels", [
            "id" => $this->label->id,
            "title" =>  $payload["title"]
        ]);
    }

    public function test_user_cannot_update_label_with_invalid_payload()
    {
        $payload = [
            "title" => "",
            "color" => "misal"
        ];

        $this->actingAs($this->authUser);
        $response = $this->putJson(route("api.labels.update", $this->label->id), $payload)
            ->assertUnprocessable();

        $this->assertDatabaseMissing("labels", [
            "id" => $this->label->id,
            "title" =>  $payload["title"]
        ]);
    }
}
