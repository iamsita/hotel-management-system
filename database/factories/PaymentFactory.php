<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['completed', 'completed', 'completed', 'failed', 'pending']);
        $reservation = Reservation::factory()->create();
        $paymentMethods = ['card', 'bank_transfer', 'cash', 'check', 'online'];

        $paidAt = null;
        if ($status === 'completed') {
            $paidAt = fake()->dateTimeBetween(
                $reservation->created_at,
                now()
            );
        }

        return [
            'reservation_id' => $reservation->id,
            'invoice_id' => null,
            'amount' => $reservation->total_amount,
            'payment_method' => fake()->randomElement($paymentMethods),
            'status' => $status,
            'transaction_id' => 'TXN-' . fake()->unique()->numerify('#########'),
            'notes' => fake()->optional()->sentence(),
            'processed_by' => User::where('type', 'staff')->first()?->id ?? User::factory()->create(['type' => 'staff'])->id,
            'paid_at' => $paidAt,
        ];
    }
}
