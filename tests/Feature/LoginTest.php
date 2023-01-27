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
    private User $user;
    /**
     * Login test 
     *
     * @return void
     */

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser();
    }

    public function test_user_can_login_with_valid_credentials()
    {
        $response = $this->postJson(route("api.auth.login"), [
            "email" => $this->user->email,
            "password" => "password"
        ]);

        $response->assertOk();
        $this->assertAuthenticatedAs($this->user, "api");
    }
}
