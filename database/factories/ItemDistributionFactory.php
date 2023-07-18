<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Staff;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ItemDistributionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'staff_id' => Staff::inRandomOrder()->first()->id,
            'item_id' => Item::inRandomOrder()->first()->id,
            'quantity' => fake()->numberBetween(1, 5),
            'reference_no' => strtoupper(Str::random(12)),
            'remark' => fake()->paragraph(3),
            'authorize_staff_id' => Staff::inRandomOrder()->first()->id,
            'status' => fake()->randomElement(
                get_request_status_options("validate")
            )
        ];
    }
}
