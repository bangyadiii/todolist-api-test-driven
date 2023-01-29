<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TodoList>
 */
class TodoListFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $ids = User::pluck('id');
        return [
            "title" => $this->faker->sentence(2),
            "description" => $this->faker->sentence(),
            "user_id" => $this->faker->randomElement($ids)
        ];
    }
}
