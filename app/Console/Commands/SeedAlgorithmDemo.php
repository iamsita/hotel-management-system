<?php

namespace App\Console\Commands;

use App\Models\Food;
use App\Models\FoodOrder;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SeedAlgorithmDemo extends Command
{
    protected $signature = 'seed:algorithm-demo';

    protected $description = 'Seed demo data to test and showcase all 5 implemented algorithms';

    public function handle(): void
    {
        $this->info('Seeding algorithm demo data...');
        $this->newLine();

        $this->seedIntervalOverlap();
        $this->seedSearchFilterSort();
        $this->seedBillingAggregation();
        $this->seedFiniteStateMachine();
        $this->seedGuestSegmentation();

        $this->newLine();
        $this->info('Running guest segmentation command...');
        $this->call('guests:segment');

        $this->newLine();
        $this->info('All algorithm demo data seeded successfully.');
    }

    // -------------------------------------------------------------------------
    // Algorithm 1 — Interval Overlap Detection
    // -------------------------------------------------------------------------
    private function seedIntervalOverlap(): void
    {
        $this->line('  [1] Interval Overlap Detection');

        $room = Room::where('status', 'available')->first();
        $guest = $this->makeGuest('overlap.guest@demo.com', 'Overlap Demo Guest');

        // Existing confirmed reservation: Jan 5 – Jan 10
        Reservation::create([
            'user_id' => $guest->id,
            'room_id' => $room->id,
            'check_in' => '2025-01-05',
            'check_out' => '2025-01-10',
            'guests' => 1,
            'status' => 'confirmed',
            'total_amount' => $room->price_per_night * 5,
        ]);

        // Non-overlapping reservation on same room: Jan 11 – Jan 15 (allowed)
        Reservation::create([
            'user_id' => $guest->id,
            'room_id' => $room->id,
            'check_in' => '2025-01-11',
            'check_out' => '2025-01-15',
            'guests' => 1,
            'status' => 'confirmed',
            'total_amount' => $room->price_per_night * 4,
        ]);

        // NOTE: A booking for Jan 08–Jan 12 on this room would be REJECTED
        // by isAvailable() because it overlaps with the Jan 05–Jan 10 reservation.
        // That rejection is tested via the booking form — not seeded as data.

        $this->line("     Room {$room->room_number}: Jan 05-10 (confirmed) + Jan 11-15 (confirmed) → no overlap");
        $this->line('     Attempting Jan 08-12 on same room would be REJECTED by the algorithm');
    }

    // -------------------------------------------------------------------------
    // Algorithm 2 — Multi-Criteria Search, Filter and Sort
    // -------------------------------------------------------------------------
    private function seedSearchFilterSort(): void
    {
        $this->line('  [2] Multi-Criteria Search, Filter & Sort');

        // Ensure one room of each type exists with varied prices for filter demos
        $types = [
            ['type' => 'single',  'price' => 80,  'capacity' => 1, 'floor' => 4],
            ['type' => 'double',  'price' => 120, 'capacity' => 2, 'floor' => 4],
            ['type' => 'suite',   'price' => 220, 'capacity' => 4, 'floor' => 4],
            ['type' => 'deluxe',  'price' => 170, 'capacity' => 3, 'floor' => 4],
        ];

        foreach ($types as $index => $data) {
            $number = '40'.($index + 1);
            if (! Room::where('room_number', $number)->exists()) {
                Room::create([
                    'room_number' => $number,
                    'type' => $data['type'],
                    'capacity' => $data['capacity'],
                    'price_per_night' => $data['price'],
                    'status' => 'available',
                    'floor' => $data['floor'],
                ]);
            }
        }

        $this->line('     4 demo rooms on floor 4 (single/double/suite/deluxe) ready for search filter testing');
    }

    // -------------------------------------------------------------------------
    // Algorithm 3 — Billing Aggregation
    // -------------------------------------------------------------------------
    private function seedBillingAggregation(): void
    {
        $this->line('  [3] Billing Aggregation');

        $guest = $this->makeGuest('billing.demo@demo.com', 'Billing Demo Guest');
        $room = Room::where('type', 'double')->where('status', 'available')->first();

        // 3-night stay at Rs.120/night = Rs.360 room charge
        $reservation = Reservation::create([
            'user_id' => $guest->id,
            'room_id' => $room->id,
            'check_in' => '2025-03-01',
            'check_out' => '2025-03-04',
            'guests' => 2,
            'status' => 'checked_in',
            'total_amount' => $room->price_per_night * 3,
        ]);

        $foods = Food::all()->keyBy('name');

        // Delivered food — counts toward bill
        FoodOrder::create([
            'reservation_id' => $reservation->id,
            'food_id' => $foods['Grilled Chicken']->id,
            'quantity' => 2,
            'total_price' => $foods['Grilled Chicken']->price * 2,
            'status' => 'delivered',
        ]);

        FoodOrder::create([
            'reservation_id' => $reservation->id,
            'food_id' => $foods['Coffee']->id,
            'quantity' => 1,
            'total_price' => $foods['Coffee']->price,
            'status' => 'delivered',
        ]);

        // Cancelled food — excluded from bill
        FoodOrder::create([
            'reservation_id' => $reservation->id,
            'food_id' => $foods['Pasta Carbonara']->id,
            'quantity' => 1,
            'total_price' => $foods['Pasta Carbonara']->price,
            'status' => 'cancelled',
        ]);

        // Still preparing — excluded from bill
        FoodOrder::create([
            'reservation_id' => $reservation->id,
            'food_id' => $foods['Steak']->id,
            'quantity' => 1,
            'total_price' => $foods['Steak']->price,
            'status' => 'preparing',
        ]);

        // Partial payment of Rs.200
        Payment::create([
            'reservation_id' => $reservation->id,
            'amount' => 200.00,
            'method' => 'cash',
            'status' => 'completed',
        ]);

        $roomCharge = $room->price_per_night * 3;
        $foodCharge = round($foods['Grilled Chicken']->price * 2 + $foods['Coffee']->price, 2);
        $grandTotal = $roomCharge + $foodCharge;
        $balanceDue = $grandTotal - 200;

        $this->line("     Room charge : Rs.{$roomCharge} (3 nights × Rs.{$room->price_per_night})");
        $this->line("     Food charge : Rs.{$foodCharge} (2 delivered orders)");
        $this->line("     Grand total : Rs.{$grandTotal}");
        $this->line("     Paid        : Rs.200.00  |  Balance due: Rs.{$balanceDue}");
    }

    // -------------------------------------------------------------------------
    // Algorithm 4 — Finite State Machine
    // -------------------------------------------------------------------------
    private function seedFiniteStateMachine(): void
    {
        $this->line('  [4] Finite State Machine');

        $guest = $this->makeGuest('fsm.demo@demo.com', 'FSM Demo Guest');
        $rooms = Room::where('status', 'available')->take(5)->get();

        $states = ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'];

        foreach ($states as $index => $status) {
            $room = $rooms[$index] ?? $rooms->last();

            $roomStatus = match ($status) {
                'checked_in', 'confirmed' => 'occupied',
                default => 'available',
            };

            $reservation = Reservation::create([
                'user_id' => $guest->id,
                'room_id' => $room->id,
                'check_in' => Carbon::now()->addDays($index + 1)->format('Y-m-d'),
                'check_out' => Carbon::now()->addDays($index + 3)->format('Y-m-d'),
                'guests' => 1,
                'status' => $status,
                'total_amount' => $room->price_per_night * 2,
            ]);

            // Sync room status to match reservation state
            if (in_array($status, ['confirmed', 'checked_in'])) {
                $room->update(['status' => 'occupied']);
            }

            $this->line("     Reservation #{$reservation->id} → status: {$status} | room: {$room->room_number} ({$roomStatus})");
        }
    }

    // -------------------------------------------------------------------------
    // Algorithm 5 — Rule-Based Decision Tree (Guest Segmentation)
    // -------------------------------------------------------------------------
    private function seedGuestSegmentation(): void
    {
        $this->line('  [5] Rule-Based Decision Tree — Guest Segmentation');

        $room = Room::where('type', 'suite')->first()
             ?? Room::first();

        // --- VIP: total_spend >= 10000, visit_count >= 5 ---
        $vip = $this->makeGuest('vip@demo.com', 'Alice VIP');
        for ($i = 0; $i < 6; $i++) {
            $r = $this->pastReservation($vip->id, $room->id, 5, 'checked_out');
            Payment::create(['reservation_id' => $r->id, 'amount' => 1800, 'method' => 'card', 'status' => 'completed']);
        }
        $this->line('     Alice VIP      → expected: vip            (6 stays, Rs.10800 total)');

        // --- Loyal: visit_count >= 3, cancellation_rate < 0.2 ---
        $loyal = $this->makeGuest('loyal@demo.com', 'Bob Loyal');
        for ($i = 0; $i < 4; $i++) {
            $r = $this->pastReservation($loyal->id, $room->id, 2, 'checked_out');
            Payment::create(['reservation_id' => $r->id, 'amount' => 300, 'method' => 'upi', 'status' => 'completed']);
        }
        $this->line('     Bob Loyal      → expected: loyal          (4 stays, 0 cancellations)');

        // --- At Risk: days_since_last_visit > 90, visit_count >= 2 ---
        $atRisk = $this->makeGuest('atrisk@demo.com', 'Carol AtRisk');
        for ($i = 0; $i < 2; $i++) {
            $r = $this->pastReservation($atRisk->id, $room->id, 3, 'checked_out', daysAgo: 120 + $i * 10);
            Payment::create(['reservation_id' => $r->id, 'amount' => 400, 'method' => 'cash', 'status' => 'completed']);
        }
        $this->line('     Carol AtRisk   → expected: at_risk        (2 stays, last visit 120+ days ago)');

        // --- High Value New: visit_count == 1, total_spend >= 5000 ---
        $hvNew = $this->makeGuest('hvnew@demo.com', 'Dave HighValueNew');
        $r = $this->pastReservation($hvNew->id, $room->id, 7, 'checked_out');
        Payment::create(['reservation_id' => $r->id, 'amount' => 5500, 'method' => 'card', 'status' => 'completed']);
        $this->line('     Dave HVNew     → expected: high_value_new (1 stay, Rs.5500 spend)');

        // --- Unreliable: cancellation_rate >= 0.5 ---
        $unreliable = $this->makeGuest('unreliable@demo.com', 'Eve Unreliable');
        for ($i = 0; $i < 2; $i++) {
            $this->pastReservation($unreliable->id, $room->id, 2, 'cancelled');
        }
        for ($i = 0; $i < 2; $i++) {
            $r = $this->pastReservation($unreliable->id, $room->id, 2, 'checked_out');
            Payment::create(['reservation_id' => $r->id, 'amount' => 200, 'method' => 'cash', 'status' => 'completed']);
        }
        $this->line('     Eve Unreliable → expected: unreliable     (4 reservations, 2 cancelled = 50%)');

        // --- Regular: default ---
        $regular = $this->makeGuest('regular@demo.com', 'Frank Regular');
        $r = $this->pastReservation($regular->id, $room->id, 2, 'checked_out');
        Payment::create(['reservation_id' => $r->id, 'amount' => 250, 'method' => 'cash', 'status' => 'completed']);
        $this->line('     Frank Regular  → expected: regular        (1 stay, Rs.250 spend)');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------
    private function makeGuest(string $email, string $name): User
    {
        return User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make('password'),
                'phone' => '98'.rand(10000000, 99999999),
                'role' => 'guest',
            ]
        );
    }

    private function pastReservation(
        int $userId,
        int $roomId,
        int $nights,
        string $status,
        int $daysAgo = 30
    ): Reservation {
        $checkOut = Carbon::now()->subDays($daysAgo);
        $checkIn = (clone $checkOut)->subDays($nights);

        $room = Room::find($roomId);

        return Reservation::create([
            'user_id' => $userId,
            'room_id' => $roomId,
            'check_in' => $checkIn->format('Y-m-d'),
            'check_out' => $checkOut->format('Y-m-d'),
            'guests' => 1,
            'status' => $status,
            'total_amount' => $room->price_per_night * $nights,
        ]);
    }
}
