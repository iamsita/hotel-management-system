<?php

namespace Database\Factories;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reservation_id' => Reservation::inRandomOrder()->value('id'),
            'amount' => $this->faker->randomFloat(2, 50, 500),
            'method' => $this->faker->randomElement(['cash', 'card', 'upi']),
            'status' => 'completed',
        ];
    }
}
