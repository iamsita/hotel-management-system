<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => 'password',
            'phone' => '9800000000',
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'John Guest',
            'email' => 'guest@gmail.com',
            'password' => 'password',
            'phone' => '9800000001',
            'role' => 'guest',
        ]);

        User::create([
            'name' => 'Jane Guest',
            'email' => 'guest2@gmail.com',
            'password' => 'password',
            'phone' => '9800000002',
            'role' => 'guest',
        ]);
    }
}
