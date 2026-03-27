<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        // (optional) clear old data
        // Room::truncate();

        $types = ['single', 'double', 'suite', 'deluxe'];
        $statuses = ['available', 'occupied', 'maintenance', 'reserved'];
        $hkStatuses = ['clean', 'dirty', 'in_progress', 'inspected'];

        // Simple mapping for realistic values
        $capacityByType = [
            'single' => 1,
            'double' => 2,
            'suite' => 4,
            'deluxe' => 3,
        ];

        $priceByType = [
            'single' => 80.00,
            'double' => 120.00,
            'suite' => 220.00,
            'deluxe' => 170.00,
        ];

        $floors = [1, 2, 3, 4, 5]; // 5 floors
        $roomsPerFloor = 10;       // 10 rooms each = 50 rooms

        foreach ($floors as $floor) {
            for ($i = 1; $i <= $roomsPerFloor; $i++) {
                $type = $types[array_rand($types)];

                Room::updateOrCreate(
                    ['room_number' => sprintf('%d%02d', $floor, $i)], // 101, 102, ..., 510
                    [
                        'room_type' => $type,
                        'capacity' => $capacityByType[$type],
                        'price_per_night' => $priceByType[$type],
                        'status' => $statuses[array_rand($statuses)],
                        'housekeeping_status' => $hkStatuses[array_rand($hkStatuses)],
                        'description' => ucfirst($type)." room on floor {$floor}",
                        'floor' => $floor,
                    ]
                );
            }
        }
    }
}
