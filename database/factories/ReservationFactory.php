<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkInDate = Carbon::instance(fake()->dateTimeBetween('-6 months', now()));
        $checkOutDate = $checkInDate->clone()->addDays(fake()->numberBetween(1, 7));

        $status = fake()->randomElement(['checked_out', 'checked_out', 'checked_out', 'cancelled', 'pending']);

        // Adjust dates based on status
        if ($status === 'cancelled') {
            $checkInDate = Carbon::instance(fake()->dateTimeBetween('-3 months', now()));
            $checkOutDate = $checkInDate->clone();
        }

        $room = Room::factory()->create();
        $user = User::firstOrCreate(
            ['email' => fake()->unique()->safeEmail()],
            [
                'name' => fake()->name(),
                'password' => bcrypt('password'),
                'phone' => fake()->phoneNumber(),
                'type' => 'guest',
                'status' => 'active',
            ]
        );

        $pricePerNight = $room->price_per_night ?? 100;
        $nights = max(1, $checkOutDate->diffInDays($checkInDate));
        $totalAmount = $pricePerNight * $nights;

        return [
            'user_id' => $user->id,
            'room_id' => $room->id,
            'check_in_date' => $checkInDate,
            'check_out_date' => $checkOutDate,
            'number_of_guests' => fake()->numberBetween(1, 4),
            'status' => $status,
            'total_amount' => $totalAmount,
            'special_requests' => fake()->optional()->sentence(),
            'managed_by' => null,
        ];
    }
}
