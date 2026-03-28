<?php

namespace Database\Seeders;

use App\Models\Food;
use Illuminate\Database\Seeder;

class FoodSeeder extends Seeder
{
    public function run(): void
    {
        $foods = [
            ['name' => 'Eggs & Toast', 'category' => 'breakfast', 'price' => 8.99],
            ['name' => 'Pancakes', 'category' => 'breakfast', 'price' => 10.99],
            ['name' => 'Grilled Chicken', 'category' => 'lunch', 'price' => 14.99],
            ['name' => 'Pasta Carbonara', 'category' => 'lunch', 'price' => 12.99],
            ['name' => 'Steak', 'category' => 'dinner', 'price' => 24.99],
            ['name' => 'Salmon', 'category' => 'dinner', 'price' => 22.99],
            ['name' => 'French Fries', 'category' => 'snacks', 'price' => 5.99],
            ['name' => 'Coffee', 'category' => 'beverages', 'price' => 3.99],
            ['name' => 'Orange Juice', 'category' => 'beverages', 'price' => 4.99],
            ['name' => 'Iced Tea', 'category' => 'beverages', 'price' => 3.99],
        ];

        foreach ($foods as $food) {
            Food::create(array_merge($food, ['available' => true]));
        }
    }
}
