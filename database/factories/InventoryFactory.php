<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventory>
 */
class InventoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => Item::inRandomOrder()->first()->id,
            'quantity' => fake()->numberBetween(10, 200),
            'staff_id' => Staff::inRandomOrder()->first()->id,
            'alter_staff_id' => Staff::inRandomOrder()->first()->id
        ];
    }
}
