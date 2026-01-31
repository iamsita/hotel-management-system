<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Room Service Breakfast',
                'type' => 'room_service',
                'price' => 15.00,
                'description' => 'Complimentary breakfast service in your room',
                'active' => true,
            ],
            [
                'name' => 'Room Service Lunch',
                'type' => 'room_service',
                'price' => 20.00,
                'description' => 'Lunch menu available from 12 PM to 3 PM',
                'active' => true,
            ],
            [
                'name' => 'Room Service Dinner',
                'type' => 'room_service',
                'price' => 25.00,
                'description' => 'Full dinner menu from 6 PM to 10 PM',
                'active' => true,
            ],
            [
                'name' => 'Restaurant Reservation',
                'type' => 'restaurant',
                'price' => 0.00,
                'description' => 'Book a table at our hotel restaurant',
                'active' => true,
            ],
            [
                'name' => 'Spa Services',
                'type' => 'spa',
                'price' => 80.00,
                'description' => 'Relaxing massage and spa treatments',
                'active' => true,
            ],
            [
                'name' => 'Laundry Service',
                'type' => 'laundry',
                'price' => 12.00,
                'description' => 'Express laundry service - same day delivery',
                'active' => true,
            ],
            [
                'name' => 'Concierge Service',
                'type' => 'other',
                'price' => 0.00,
                'description' => '24/7 concierge for all your needs',
                'active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
