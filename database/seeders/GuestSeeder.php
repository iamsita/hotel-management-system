<?php

namespace Database\Seeders;

use App\Models\Guest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GuestSeeder extends Seeder
{
    public function run(): void
    {
        $guests = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@email.com',
                'password' => Hash::make('password'),
                'phone' => '+1-555-0101',
                'id_type' => 'passport',
                'id_number' => 'US123456789',
                'address' => '123 Main Street',
                'city' => 'New York',
                'country' => 'USA',
                'guest_type' => 'individual',
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@email.com',
                'password' => Hash::make('password'),
                'phone' => '+1-555-0102',
                'id_type' => 'driving_license',
                'id_number' => 'DL987654321',
                'address' => '456 Oak Avenue',
                'city' => 'Los Angeles',
                'country' => 'USA',
                'guest_type' => 'corporate',
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Johnson',
                'email' => 'michael.johnson@email.com',
                'password' => Hash::make('password'),
                'phone' => '+1-555-0103',
                'id_type' => 'national_id',
                'id_number' => 'NI456789123',
                'address' => '789 Pine Road',
                'city' => 'Chicago',
                'country' => 'USA',
                'guest_type' => 'individual',
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Williams',
                'email' => 'sarah.williams@email.com',
                'password' => Hash::make('password'),
                'phone' => '+1-555-0104',
                'id_type' => 'passport',
                'id_number' => 'UK789456123',
                'address' => '321 Elm Street',
                'city' => 'London',
                'country' => 'UK',
                'guest_type' => 'corporate',
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Brown',
                'email' => 'david.brown@email.com',
                'password' => Hash::make('password'),
                'phone' => '+1-555-0105',
                'id_type' => 'passport',
                'id_number' => 'CA123789456',
                'address' => '654 Maple Drive',
                'city' => 'Toronto',
                'country' => 'Canada',
                'guest_type' => 'individual',
            ],
            [
                'first_name' => 'Emma',
                'last_name' => 'Davis',
                'email' => 'emma.davis@email.com',
                'password' => Hash::make('password'),
                'phone' => '+1-555-0106',
                'id_type' => 'driving_license',
                'id_number' => 'DL654321987',
                'address' => '987 Birch Lane',
                'city' => 'Boston',
                'country' => 'USA',
                'guest_type' => 'individual',
            ],
            [
                'first_name' => 'Chris',
                'last_name' => 'Miller',
                'email' => 'chris.miller@email.com',
                'password' => Hash::make('password'),
                'phone' => '+1-555-0107',
                'id_type' => 'national_id',
                'id_number' => 'NI789123456',
                'address' => '147 Cedar Court',
                'city' => 'Miami',
                'country' => 'USA',
                'guest_type' => 'corporate',
            ],
            [
                'first_name' => 'Kate',
                'last_name' => 'Wilson',
                'email' => 'kate.wilson@email.com',
                'password' => Hash::make('password'),
                'phone' => '+1-555-0108',
                'id_type' => 'passport',
                'id_number' => 'FR456123789',
                'address' => '258 Walnut Way',
                'city' => 'Paris',
                'country' => 'France',
                'guest_type' => 'individual',
            ],
        ];

        foreach ($guests as $guest) {
            Guest::create($guest);
        }
    }
}
