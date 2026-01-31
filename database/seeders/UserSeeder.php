<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => 'password',
            'phone' => '+1-800-123-4567',
            'role' => 'admin',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Manager User',
            'email' => 'manager@gmail.com',
            'password' => 'password',
            'phone' => '+1-800-123-4568',
            'role' => 'manager',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Staff Member 1',
            'email' => 'staff1@gmail.com',
            'password' => 'password',
            'phone' => '+1-800-123-4569',
            'role' => 'staff',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Staff Member 2',
            'email' => 'staff2@gmail.com',
            'password' => 'password',
            'phone' => '+1-800-123-4570',
            'role' => 'staff',
            'status' => 'active',
        ]);
    }
}
