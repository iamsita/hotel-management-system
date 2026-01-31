<?php

namespace Database\Seeders;

use App\Models\Food;
use Illuminate\Database\Seeder;

class FoodSeeder extends Seeder
{
    public function run(): void
    {
        $foods = [
            // Breakfast
            [
                'name' => 'Eggs & Toast',
                'category' => 'breakfast',
                'price' => 8.99,
                'description' => 'Scrambled eggs with buttered toast and jam',
                'available' => true,
            ],
            [
                'name' => 'Pancakes',
                'category' => 'breakfast',
                'price' => 10.99,
                'description' => 'Fluffy pancakes with maple syrup and butter',
                'available' => true,
            ],
            [
                'name' => 'Fresh Fruit Platter',
                'category' => 'breakfast',
                'price' => 9.99,
                'description' => 'Assorted fresh fruits with yogurt',
                'available' => true,
            ],

            // Lunch
            [
                'name' => 'Grilled Chicken',
                'category' => 'lunch',
                'price' => 14.99,
                'description' => 'Tender grilled chicken breast with vegetables',
                'available' => true,
            ],
            [
                'name' => 'Pasta Carbonara',
                'category' => 'lunch',
                'price' => 12.99,
                'description' => 'Classic Italian pasta with bacon and cream sauce',
                'available' => true,
            ],
            [
                'name' => 'Fish & Chips',
                'category' => 'lunch',
                'price' => 13.99,
                'description' => 'Crispy fish fillet with seasoned fries',
                'available' => true,
            ],

            // Dinner
            [
                'name' => 'Steak',
                'category' => 'dinner',
                'price' => 24.99,
                'description' => 'Prime grade steak cooked to perfection',
                'available' => true,
            ],
            [
                'name' => 'Salmon',
                'category' => 'dinner',
                'price' => 22.99,
                'description' => 'Pan-seared salmon with lemon butter sauce',
                'available' => true,
            ],
            [
                'name' => 'Vegetarian Risotto',
                'category' => 'dinner',
                'price' => 18.99,
                'description' => 'Creamy risotto with seasonal vegetables',
                'available' => true,
            ],

            // Beverages
            [
                'name' => 'Coffee',
                'category' => 'beverages',
                'price' => 3.99,
                'description' => 'Premium coffee - Espresso, Cappuccino, Americano',
                'available' => true,
            ],
            [
                'name' => 'Orange Juice',
                'category' => 'beverages',
                'price' => 4.99,
                'description' => 'Freshly squeezed orange juice',
                'available' => true,
            ],
            [
                'name' => 'Iced Tea',
                'category' => 'beverages',
                'price' => 3.99,
                'description' => 'Cold iced tea with lemon',
                'available' => true,
            ],

            // Desserts
            [
                'name' => 'Chocolate Cake',
                'category' => 'desserts',
                'price' => 7.99,
                'description' => 'Rich chocolate cake with chocolate frosting',
                'available' => true,
            ],
            [
                'name' => 'Cheesecake',
                'category' => 'desserts',
                'price' => 8.99,
                'description' => 'New York style cheesecake with berry topping',
                'available' => true,
            ],
        ];

        foreach ($foods as $food) {
            Food::firstOrCreate([
                'name' => $food['name'],
            ], $food);
        }
    }
}
