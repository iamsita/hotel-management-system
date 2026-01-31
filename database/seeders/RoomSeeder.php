<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            // Single Rooms
            ['room_number' => '101', 'room_type' => 'single', 'capacity' => 1, 'price_per_night' => 79.99, 'floor' => 1],
            ['room_number' => '102', 'room_type' => 'single', 'capacity' => 1, 'price_per_night' => 79.99, 'floor' => 1],
            ['room_number' => '201', 'room_type' => 'single', 'capacity' => 1, 'price_per_night' => 79.99, 'floor' => 2],
            ['room_number' => '202', 'room_type' => 'single', 'capacity' => 1, 'price_per_night' => 79.99, 'floor' => 2],
            ['room_number' => '301', 'room_type' => 'single', 'capacity' => 1, 'price_per_night' => 79.99, 'floor' => 3],

            // Double Rooms
            ['room_number' => '103', 'room_type' => 'double', 'capacity' => 2, 'price_per_night' => 119.99, 'floor' => 1],
            ['room_number' => '104', 'room_type' => 'double', 'capacity' => 2, 'price_per_night' => 119.99, 'floor' => 1],
            ['room_number' => '203', 'room_type' => 'double', 'capacity' => 2, 'price_per_night' => 119.99, 'floor' => 2],
            ['room_number' => '204', 'room_type' => 'double', 'capacity' => 2, 'price_per_night' => 119.99, 'floor' => 2],
            ['room_number' => '302', 'room_type' => 'double', 'capacity' => 2, 'price_per_night' => 119.99, 'floor' => 3],

            // Suite Rooms
            ['room_number' => '105', 'room_type' => 'suite', 'capacity' => 3, 'price_per_night' => 179.99, 'floor' => 1],
            ['room_number' => '106', 'room_type' => 'suite', 'capacity' => 3, 'price_per_night' => 179.99, 'floor' => 1],
            ['room_number' => '205', 'room_type' => 'suite', 'capacity' => 3, 'price_per_night' => 179.99, 'floor' => 2],
            ['room_number' => '206', 'room_type' => 'suite', 'capacity' => 3, 'price_per_night' => 179.99, 'floor' => 2],
            ['room_number' => '303', 'room_type' => 'suite', 'capacity' => 3, 'price_per_night' => 179.99, 'floor' => 3],

            // Deluxe Rooms
            ['room_number' => '107', 'room_type' => 'deluxe', 'capacity' => 4, 'price_per_night' => 249.99, 'floor' => 1],
            ['room_number' => '108', 'room_type' => 'deluxe', 'capacity' => 4, 'price_per_night' => 249.99, 'floor' => 1],
            ['room_number' => '207', 'room_type' => 'deluxe', 'capacity' => 4, 'price_per_night' => 249.99, 'floor' => 2],
            ['room_number' => '208', 'room_type' => 'deluxe', 'capacity' => 4, 'price_per_night' => 249.99, 'floor' => 2],
            ['room_number' => '304', 'room_type' => 'deluxe', 'capacity' => 4, 'price_per_night' => 249.99, 'floor' => 3],
        ];

        foreach ($rooms as $room) {
            Room::create(array_merge($room, [
                'status' => 'available',
                'housekeeping_status' => 'clean',
                'description' => ucfirst($room['room_type']).' room with modern amenities',
            ]));
        }
    }
}
