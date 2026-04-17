<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    public function definition(): array
    {
        $checkIn = $this->faker->dateTimeBetween('-6 months', '-1 month');
        $checkOut = (clone $checkIn)->modify('+'.rand(1, 5).' days');
        $room = Room::inRandomOrder()->first();

        return [
            'user_id' => User::where('role', 'guest')->inRandomOrder()->value('id'),
            'room_id' => $room->id,
            'check_in' => $checkIn->format('Y-m-d'),
            'check_out' => $checkOut->format('Y-m-d'),
            'guests' => rand(1, $room->capacity),
            'status' => 'checked_out',
            'total_amount' => $room->price_per_night
                * (new Carbon($checkIn))->diffInDays(new Carbon($checkOut)),
        ];
    }

    public function cancelled(): static
    {
        return $this->state(['status' => 'cancelled']);
    }

    public function confirmed(): static
    {
        return $this->state(['status' => 'confirmed']);
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }
}
