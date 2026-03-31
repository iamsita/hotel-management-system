<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
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
        $statuses = ['available', 'occupied', 'maintenance', 'reserved'];

        return [
            'room_number' => 'R' . fake()->unique()->numberBetween(100, 999),
            'room_type' => fake()->randomElement($roomTypes),
            'capacity' => fake()->randomElement([1, 2, 3, 4]),
            'price_per_night' => fake()->randomElement([50, 75, 100, 150, 200, 250]),
            'status' => fake()->randomElement($statuses),
            'housekeeping_status' => fake()->randomElement(['clean', 'dirty', 'in_progress', 'inspected']),
            'description' => fake()->sentence(),
            'floor' => fake()->randomElement([1, 2, 3, 4, 5]),
        ];
    }
}
