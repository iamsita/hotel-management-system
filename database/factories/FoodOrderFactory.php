<?php

namespace Database\Factories;

use App\Models\Food;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

class FoodOrderFactory extends Factory
{
    public function definition(): array
    {
        $food = Food::inRandomOrder()->first();
        $quantity = rand(1, 3);

        return [
            'reservation_id' => Reservation::inRandomOrder()->value('id'),
            'food_id' => $food->id,
            'quantity' => $quantity,
            'total_price' => $food->price * $quantity,
            'status' => 'delivered',
        ];
    }

    public function cancelled(): static
    {
        return $this->state(['status' => 'cancelled']);
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }
}
