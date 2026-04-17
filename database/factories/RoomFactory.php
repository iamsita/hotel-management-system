<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roomTypes = ['single', 'double', 'suite', 'deluxe'];
        $roomType = $this->faker->randomElement($roomTypes);

        return [
            'room_number' => $this->faker->unique()->numerify('###'),
            'type' => $roomType,
            'capacity' => $roomType === 'single' ? 1 : ($roomType === 'double' ? 2 : ($roomType === 'suite' ? 3 : 4)),
            'price_per_night' => $this->faker->numberBetween(3000, 15000),
            'status' => 'available',
            'floor' => $this->faker->numberBetween(1, 10),
        ];
    }
}
