<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Label>
 */
class LabelFactory extends Factory
{

    public function definition(): array
    {
        $ids = User::pluck("id");
        return [
            "title" => $this->faker->sentence(1),
            "color" => $this->faker->hexColor(),
            "user_id" => $this->faker->randomElement($ids)
        ];
    }
}
