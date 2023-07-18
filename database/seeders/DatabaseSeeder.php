<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\DDD;
use App\Models\Item;
use App\Models\Staff;
use App\Models\Inventory;
use App\Models\ItemRequest;
use App\Models\HelpdeskRequest;
use App\Models\HelpdeskSupport;
use Illuminate\Database\Seeder;
use App\Models\ItemDistribution;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Item::factory(100)->create();
        // Ddd::factory(30)->create();
        // Staff::factory(20)->create();

        // Inventory::factory(150)->create();
        // HelpdeskRequest::factory(200)->create();
        // HelpdeskSupport::factory(100)->create();
        // ItemRequest::factory(200)->create();
        // ItemDistribution::factory(100)->create();

        Ddd::factory()->create([
            'name' => 'Information Communication Technology',
            'short' => 'ICT',
            'category' => 'Division',
            'floor' => '2nd Floor'
        ]);

        Staff::factory()->create([
            'staff_no' => '22390',
            'name' => 'Muhammad SIRAJO',
            'email' => 'msattahir@gmail.com',
            'ddd_id' => 1,
            'role' => 'Admin',
            'status' => 'Active',
            'password' => bcrypt('12345678')
        ]);
    }
}
