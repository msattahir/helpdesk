<?php

namespace Database\Factories;

use App\Models\Staff;
use App\Models\Ddd;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Support>
 */
class HelpdeskSupportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ddd_id' => Ddd::inRandomOrder()->first()->id,
            'remark' => fake()->paragraph(3),
            'authorize_staff_id' => Staff::inRandomOrder()->first()->id,
            'status' => fake()->randomElement(
                get_request_status_options("validate")
            )
        ];
    }
}
