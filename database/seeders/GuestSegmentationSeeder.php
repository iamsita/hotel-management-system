<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class GuestSegmentationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating test data for Guest Segmentation Algorithm...');

        // Create rooms if they don't exist
        $roomCount = Room::count();
        if ($roomCount === 0) {
            $this->command->line('Creating rooms...');
            Room::factory(10)->create();
        }

        // Create VIP Guests (high lifetime value, multiple bookings)
        $this->createVIPGuests();

        // Create Loyal Guests (frequent, longer stays)
        $this->createLoyalGuests();

        // Create Business Guests (short stays, frequent)
        $this->createBusinessGuests();

        // Create Leisure Guests (longer stays, occasional)
        $this->createLeisureGuests();

        // Create Budget Guests (price-conscious)
        $this->createBudgetGuests();

        // Create Risk Guests (high cancellation rate or payment issues)
        $this->createRiskGuests();

        // Create Regular Guests (average behavior)
        $this->createRegularGuests();

        $this->command->info('✓ Guest Segmentation test data created successfully!');
    }

    /**
     * Create VIP guests: Lifetime value >= $50,000 and >= 5 bookings
     */
    private function createVIPGuests(): void
    {
        $this->command->line('Creating VIP guests (8)...');

        for ($i = 1; $i <= 8; $i++) {
            $guest = User::factory()->guest()->create([
                'name' => "VIP Guest {$i}",
                'email' => "vip-{$i}+" . fake()->randomNumber() . '@example.com',
            ]);

            // Create 10-12 completed bookings with high prices
            for ($j = 0; $j < fake()->numberBetween(10, 12); $j++) {
                $room = Room::inRandomOrder()->first();
                $checkInDate = Carbon::now()->subMonths(24 - ($j * 2));
                $checkOutDate = $checkInDate->copy()->addDays(fake()->numberBetween(3, 5));
                $amount = fake()->randomElement([450, 500, 550, 600, 700, 800, 900]);

                $reservation = Reservation::create([
                    'user_id' => $guest->id,
                    'room_id' => $room->id,
                    'check_in_date' => $checkInDate,
                    'check_out_date' => $checkOutDate,
                    'number_of_guests' => fake()->numberBetween(2, 4),
                    'status' => 'checked_out',
                    'total_amount' => $amount,
                    'special_requests' => 'VIP service requested',
                ]);

                // Create completed payment
                Payment::create([
                    'reservation_id' => $reservation->id,
                    'amount' => $amount,
                    'payment_method' => fake()->randomElement(['card', 'bank_transfer']),
                    'status' => 'completed',
                    'transaction_id' => 'TXN-' . fake()->unique()->numerify('#########'),
                    'processed_by' => User::where('type', 'staff')->first()?->id ?? 1,
                    'paid_at' => $checkInDate->copy()->subDays(2),
                ]);
            }
        }
    }

    /**
     * Create Loyal guests: >= 10 bookings and avg stay >= 3 days
     */
    private function createLoyalGuests(): void
    {
        $this->command->line('Creating Loyal guests (12)...');

        for ($i = 1; $i <= 12; $i++) {
            $guest = User::factory()->guest()->create([
                'name' => "Loyal Guest {$i}",
                'email' => "loyal-{$i}+" . fake()->randomNumber() . '@example.com',
            ]);

            // Create 14-16 completed bookings with consistent stays
            for ($j = 0; $j < fake()->numberBetween(14, 16); $j++) {
                $room = Room::inRandomOrder()->first();
                $checkInDate = Carbon::now()->subMonths(30 - ($j * 2));
                $nights = fake()->numberBetween(3, 6);
                $checkOutDate = $checkInDate->copy()->addDays($nights);
                $amount = $room->price_per_night * $nights;

                $reservation = Reservation::create([
                    'user_id' => $guest->id,
                    'room_id' => $room->id,
                    'check_in_date' => $checkInDate,
                    'check_out_date' => $checkOutDate,
                    'number_of_guests' => fake()->numberBetween(2, 3),
                    'status' => 'checked_out',
                    'total_amount' => $amount,
                    'special_requests' => 'Regular guest - frequent visitor',
                ]);

                // Create completed payment
                Payment::create([
                    'reservation_id' => $reservation->id,
                    'amount' => $amount,
                    'payment_method' => fake()->randomElement(['card', 'bank_transfer']),
                    'status' => 'completed',
                    'transaction_id' => 'TXN-' . fake()->unique()->numerify('#########'),
                    'processed_by' => User::where('type', 'staff')->first()?->id ?? 1,
                    'paid_at' => $checkInDate->copy()->subDays(1),
                ]);
            }
        }
    }

    /**
     * Create Business guests: Avg stay <= 2 days and >= 4 bookings
     */
    private function createBusinessGuests(): void
    {
        $this->command->line('Creating Business guests (20)...');

        for ($i = 1; $i <= 20; $i++) {
            $guest = User::factory()->guest()->create([
                'name' => "Business Guest {$i}",
                'email' => "business-{$i}+" . fake()->randomNumber() . '@example.com',
            ]);

            // Create 8-10 completed bookings with 1-2 night stays
            for ($j = 0; $j < fake()->numberBetween(8, 10); $j++) {
                $room = Room::inRandomOrder()->first();
                $checkInDate = Carbon::now()->subMonths(18 - ($j * 1.8));
                $nights = fake()->randomElement([1, 2]);
                $checkOutDate = $checkInDate->copy()->addDays($nights);
                $amount = $room->price_per_night * $nights;

                $reservation = Reservation::create([
                    'user_id' => $guest->id,
                    'room_id' => $room->id,
                    'check_in_date' => $checkInDate,
                    'check_out_date' => $checkOutDate,
                    'number_of_guests' => 1,
                    'status' => 'checked_out',
                    'total_amount' => $amount,
                    'special_requests' => 'Business meeting - corporate account',
                ]);

                // Create completed payment (high reliability)
                Payment::create([
                    'reservation_id' => $reservation->id,
                    'amount' => $amount,
                    'payment_method' => fake()->randomElement(['card', 'bank_transfer']),
                    'status' => 'completed',
                    'transaction_id' => 'TXN-' . fake()->unique()->numerify('#########'),
                    'processed_by' => User::where('type', 'staff')->first()?->id ?? 1,
                    'paid_at' => $checkInDate->copy()->subDays(1),
                ]);
            }
        }
    }

    /**
     * Create Leisure guests: Avg stay >= 4 days
     */
    private function createLeisureGuests(): void
    {
        $this->command->line('Creating Leisure guests (12)...');

        for ($i = 1; $i <= 12; $i++) {
            $guest = User::factory()->guest()->create([
                'name' => "Leisure Guest {$i}",
                'email' => "leisure-{$i}+" . fake()->randomNumber() . '@example.com',
            ]);

            // Create 6-8 completed bookings with long stays (4-8 nights)
            for ($j = 0; $j < fake()->numberBetween(6, 8); $j++) {
                $room = Room::inRandomOrder()->first();
                $checkInDate = Carbon::now()->subMonths(24 - ($j * 3.5));
                $nights = fake()->randomElement([4, 5, 6, 7, 8]);
                $checkOutDate = $checkInDate->copy()->addDays($nights);
                $amount = $room->price_per_night * $nights;

                $reservation = Reservation::create([
                    'user_id' => $guest->id,
                    'room_id' => $room->id,
                    'check_in_date' => $checkInDate,
                    'check_out_date' => $checkOutDate,
                    'number_of_guests' => fake()->numberBetween(2, 4),
                    'status' => 'checked_out',
                    'total_amount' => $amount,
                    'special_requests' => 'Vacation - extended stay',
                ]);

                // Create completed payment
                Payment::create([
                    'reservation_id' => $reservation->id,
                    'amount' => $amount,
                    'payment_method' => fake()->randomElement(['card', 'cash']),
                    'status' => 'completed',
                    'transaction_id' => 'TXN-' . fake()->unique()->numerify('#########'),
                    'processed_by' => User::where('type', 'staff')->first()?->id ?? 1,
                    'paid_at' => $checkInDate->copy()->subDays(2),
                ]);
            }
        }
    }

    /**
     * Create Budget guests: Below median spending
     */
    private function createBudgetGuests(): void
    {
        $this->command->line('Creating Budget guests (16)...');

        for ($i = 1; $i <= 16; $i++) {
            $guest = User::factory()->guest()->create([
                'name' => "Budget Guest {$i}",
                'email' => "budget-{$i}+" . fake()->randomNumber() . '@example.com',
            ]);

            // Create 5-7 completed bookings with budget rooms and short stays
            for ($j = 0; $j < fake()->numberBetween(5, 7); $j++) {
                $room = Room::where('price_per_night', '<=', 120)->inRandomOrder()->first()
                    ?? Room::inRandomOrder()->first();
                $checkInDate = Carbon::now()->subMonths(12 - ($j * 1.8));
                $nights = fake()->numberBetween(1, 2);
                $checkOutDate = $checkInDate->copy()->addDays($nights);
                $amount = $room->price_per_night * $nights;

                $reservation = Reservation::create([
                    'user_id' => $guest->id,
                    'room_id' => $room->id,
                    'check_in_date' => $checkInDate,
                    'check_out_date' => $checkOutDate,
                    'number_of_guests' => 1,
                    'status' => 'checked_out',
                    'total_amount' => $amount,
                    'special_requests' => 'Budget accommodation',
                ]);

                // Create completed payment
                Payment::create([
                    'reservation_id' => $reservation->id,
                    'amount' => $amount,
                    'payment_method' => fake()->randomElement(['cash', 'card']),
                    'status' => 'completed',
                    'transaction_id' => 'TXN-' . fake()->unique()->numerify('#########'),
                    'processed_by' => User::where('type', 'staff')->first()?->id ?? 1,
                    'paid_at' => $checkInDate->copy()->subDays(1),
                ]);
            }
        }
    }

    /**
     * Create Risk guests: High cancellation or payment issues
     */
    private function createRiskGuests(): void
    {
        $this->command->line('Creating Risk guests (6)...');

        for ($i = 1; $i <= 6; $i++) {
            $guest = User::factory()->guest()->create([
                'name' => "Risk Guest {$i}",
                'email' => "risk-{$i}+" . fake()->randomNumber() . '@example.com',
            ]);

            // Create bookings with high cancellation rate and payment issues
            for ($j = 0; $j < 7; $j++) {
                $room = Room::inRandomOrder()->first();
                $checkInDate = Carbon::now()->subMonths(10 - $j);
                $checkOutDate = $checkInDate->copy()->addDays(fake()->numberBetween(1, 3));
                $amount = $room->price_per_night * $checkOutDate->diffInDays($checkInDate);

                // Mix of checked_out and cancelled (higher cancellation rate for risk segment)
                $resStatus = ($j % 2 === 0) ? 'cancelled' : 'checked_out';

                $reservation = Reservation::create([
                    'user_id' => $guest->id,
                    'room_id' => $room->id,
                    'check_in_date' => $checkInDate,
                    'check_out_date' => $checkOutDate,
                    'number_of_guests' => 1,
                    'status' => $resStatus,
                    'total_amount' => $amount,
                    'special_requests' => 'At-risk profile',
                ]);

                // Mix of completed and failed payments (high failure rate)
                $paymentStatus = ($j % 2 === 0) ? 'failed' : 'completed';

                Payment::create([
                    'reservation_id' => $reservation->id,
                    'amount' => $amount,
                    'payment_method' => fake()->randomElement(['card', 'bank_transfer']),
                    'status' => $paymentStatus,
                    'transaction_id' => 'TXN-' . fake()->unique()->numerify('#########'),
                    'processed_by' => User::where('type', 'staff')->first()?->id ?? 1,
                    'paid_at' => $paymentStatus === 'completed' ? $checkInDate->copy()->subDays(1) : null,
                ]);
            }
        }
    }

    /**
     * Create Regular guests: Average behavior
     */
    private function createRegularGuests(): void
    {
        $this->command->line('Creating Regular guests (6)...');

        for ($i = 1; $i <= 6; $i++) {
            $guest = User::factory()->guest()->create([
                'name' => "Regular Guest {$i}",
            ]);

            // Create 4-5 completed bookings with average behavior
            for ($j = 0; $j < fake()->numberBetween(4, 5); $j++) {
                $room = Room::inRandomOrder()->first();
                $checkInDate = Carbon::now()->subMonths(12 - ($j * 2.5));
                $nights = fake()->numberBetween(2, 4);
                $checkOutDate = $checkInDate->copy()->addDays($nights);
                $amount = $room->price_per_night * $nights;

                $reservation = Reservation::create([
                    'user_id' => $guest->id,
                    'room_id' => $room->id,
                    'check_in_date' => $checkInDate,
                    'check_out_date' => $checkOutDate,
                    'number_of_guests' => 2,
                    'status' => 'checked_out',
                    'total_amount' => $amount,
                    'special_requests' => 'Standard booking',
                ]);

                // Create completed payment
                Payment::create([
                    'reservation_id' => $reservation->id,
                    'amount' => $amount,
                    'payment_method' => fake()->randomElement(['card', 'cash']),
                    'status' => 'completed',
                    'transaction_id' => 'TXN-' . fake()->unique()->numerify('#########'),
                    'processed_by' => User::where('type', 'staff')->first()?->id ?? 1,
                    'paid_at' => $checkInDate->copy()->subDays(1),
                ]);
            }
        }
    }
}
