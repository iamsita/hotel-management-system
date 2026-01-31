<?php

namespace Database\Seeders;

use App\Models\Guest;
use App\Models\Room;
use App\Models\Service;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create sample rooms
        $roomTypes = ['single', 'double', 'suite', 'deluxe'];
        $prices = ['single' => 79.99, 'double' => 119.99, 'suite' => 179.99, 'deluxe' => 249.99];

        for ($i = 1; $i <= 20; $i++) {
            $floor = intval($i / 5) + 1;
            $type = $roomTypes[($i - 1) % 4];

            Room::create([
                'room_number' => str_pad($i, 3, '0', STR_PAD_LEFT),
                'room_type' => $type,
                'capacity' => $type === 'single' ? 1 : ($type === 'double' ? 2 : ($type === 'suite' ? 4 : 3)),
                'price_per_night' => $prices[$type],
                'status' => $i % 5 === 0 ? 'available' : ($i % 5 === 1 ? 'occupied' : 'available'),
                'housekeeping_status' => $i % 3 === 0 ? 'dirty' : 'clean',
                'floor' => $floor,
                'description' => ucfirst($type).' room with modern amenities',
            ]);
        }

        // Create sample guests
        $firstNames = ['John', 'Jane', 'Michael', 'Sarah', 'David', 'Emma', 'James', 'Olivia'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis'];

        for ($i = 0; $i < count($firstNames); $i++) {
            Guest::create([
                'first_name' => $firstNames[$i],
                'last_name' => $lastNames[$i],
                'email' => strtolower($firstNames[$i]).'.'.strtolower($lastNames[$i]).'@example.com',
                'phone' => '+1'.rand(2000000000, 9999999999),
                'id_type' => ['passport', 'national_id', 'driving_license'][rand(0, 2)],
                'id_number' => rand(1000000, 9999999),
                'address' => rand(100, 999).' Main Street',
                'city' => ['New York', 'Los Angeles', 'Chicago', 'Houston'][rand(0, 3)],
                'country' => 'USA',
                'guest_type' => rand(0, 1) ? 'individual' : 'corporate',
            ]);
        }

        // Create sample services
        $services = [
            ['name' => 'Room Service Breakfast', 'type' => 'room_service', 'price' => 25.00],
            ['name' => 'Room Service Lunch', 'type' => 'room_service', 'price' => 35.00],
            ['name' => 'Room Service Dinner', 'type' => 'room_service', 'price' => 45.00],
            ['name' => 'Restaurant Meal', 'type' => 'restaurant', 'price' => 50.00],
            ['name' => 'Spa Treatment', 'type' => 'spa', 'price' => 75.00],
            ['name' => 'Laundry Service', 'type' => 'laundry', 'price' => 15.00],
            ['name' => 'Mini Bar', 'type' => 'other', 'price' => 10.00],
        ];

        foreach ($services as $service) {
            Service::create([
                'name' => $service['name'],
                'type' => $service['type'],
                'price' => $service['price'],
                'description' => 'Premium '.$service['name'],
                'active' => true,
            ]);
        }
    }
}
