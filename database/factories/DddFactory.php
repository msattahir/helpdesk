<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DDD>
 */
class DddFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => strtoupper(fake()->unique()->word),
            'short' => strtoupper(Str::random(4)),
            'category' => fake()->randomElement(
                get_ddd_category_options("validate")
            ),
            'floor' => fake()->randomElement(
                get_floor_options("validate")
            )
        ];
    }
}
