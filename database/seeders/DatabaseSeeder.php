<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call individual seeders in order
        $this->call([
            ServiceSeeder::class,
            FoodSeeder::class,
            UserSeeder::class,
            RoomSeeder::class,
            ReservationSeeder::class,
        ]);
    }
}
