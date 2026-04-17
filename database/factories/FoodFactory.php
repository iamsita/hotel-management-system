<?php

namespace Database\Factories;

use App\Models\Food;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Food>
 */
class FoodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['breakfast', 'lunch', 'dinner', 'snacks', 'beverages'];

        return [
            'name' => $this->faker->word() . ' ' . $this->faker->word(),
            'category' => $this->faker->randomElement($categories),
            'price' => $this->faker->numberBetween(100, 800),
            'available' => true,
        ];
    }
}
