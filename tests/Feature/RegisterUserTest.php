<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\CreatesApplication;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use CreatesApplication, RefreshDatabase;
    /**
     * User can create and register their account
     *
     * @return void
     */
    public function test_register_new_account()
    {
        $payload = [
            "username" => "iday123",
            "email" => "triadi@gmail.com",
            "password" => "password123",
            "password_confirmation" => "password123",
        ];

        $response = $this->postJson(\route("api.auth.register"), $payload);

        $response->assertCreated();
        $response->assertJsonMissingPath("password");
        $this->assertDatabaseHas("users", ["username" => $payload["username"], "email" => $payload["email"]]);
        $this->assertDatabaseMissing("users", ["password" => $payload["password"]]);
    }
}
