<?php

namespace Database\Factories;

use App\Models\DDD;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Staff>
 */
class StaffFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'staff_no' => fake()->randomNumber(5),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'ddd_id' => DDD::inRandomOrder()->first()->id,
            'role' => fake()->randomElement(
                get_account_role_options("validate")
            ),
            'status' => fake()->randomElement(
                get_account_status_options("validate")
            ),
            'password' => bcrypt('secret')
        ];
    }
}
