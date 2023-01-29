<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\CreatesApplication;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use CreatesApplication, RefreshDatabase;
    /**
     * Login test 
     *
     * @return void
     */

    protected function setUp(): void
    {
        parent::setUp();
        $this->createAuthUser();
    }

    public function test_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->postJson(route("api.auth.login"), [
            "email" => "unknown_email@gmail.com",
            "password" => "wrong_password"
        ]);

        $response->assertUnauthorized();
    }
    public function test_user_can_login_with_valid_credentials()
    {
        $response = $this->postJson(route("api.auth.login"), [
            "email" => $this->authUser->email,
            "password" => "password"
        ]);

        $response->assertOk();

        $token = $response->json()["data"]["access_token"];
        $this->assertNotEmpty($token);
        $this->assertDatabaseHas("personal_access_tokens", ['tokenable_id' => $this->authUser->id]);
    }
}
