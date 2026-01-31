<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => 'password',
                'phone' => '9800000000',
                'type' => 'admin',
                'status' => 'active',
            ]
        );

        // Manager
        User::firstOrCreate(
            ['email' => 'manager@gmail.com'],
            [
                'name' => 'Manager',
                'password' => 'password',
                'phone' => '9800000001',
                'type' => 'manager',
                'status' => 'active',
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        // Staff
        User::firstOrCreate(
            ['email' => 'staff@gmail.com'],
            [
                'name' => 'Staff',
                'password' => 'password',
                'phone' => '9800000002',
                'type' => 'staff',
                'status' => 'active',
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        // Some guest users
        $guestUsers = [
            ['name' => 'Guest One', 'email' => 'guest1@gmail.com', 'phone' => '9800000003'],
            ['name' => 'Guest Two', 'email' => 'guest2@gmail.com', 'phone' => '9800000004'],
            ['name' => 'Guest Three', 'email' => 'guest3@gmail.com', 'phone' => null],
        ];

        foreach ($guestUsers as $u) {
            User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => 'password',
                    'phone' => $u['phone'],
                    'type' => 'guest',
                    'status' => 'active',
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                ]
            );
        }
    }
}
