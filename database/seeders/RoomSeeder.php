<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['single', 'double', 'suite', 'deluxe'];
        $prices = ['single' => 80, 'double' => 120, 'suite' => 220, 'deluxe' => 170];
        $capacities = ['single' => 1, 'double' => 2, 'suite' => 4, 'deluxe' => 3];

        for ($floor = 1; $floor <= 3; $floor++) {
            for ($i = 1; $i <= 5; $i++) {
                $type = $types[array_rand($types)];
                Room::create([
                    'room_number' => $floor.'0'.$i,
                    'type' => $type,
                    'capacity' => $capacities[$type],
                    'price_per_night' => $prices[$type],
                    'status' => 'available',
                    'floor' => $floor,
                ]);
            }
        }
    }
}
