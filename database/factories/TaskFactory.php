<?php

namespace Database\Factories;

use App\Models\TodoList;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $list = TodoList::pluck('id')->toArray();
        return [
            "title" => $this->faker->word(2),
            "todo_list_id" => $this->faker->randomElement($list),
        ];
    }
}
