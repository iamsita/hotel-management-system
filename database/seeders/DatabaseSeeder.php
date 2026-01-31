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
            UserSeeder::class,
            GuestSeeder::class,
            RoomSeeder::class,
            ServiceSeeder::class,
            FoodSeeder::class,
            ReservationSeeder::class,
        ]);
    }
}
