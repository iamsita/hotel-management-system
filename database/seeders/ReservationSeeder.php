<?php

namespace Database\Seeders;

use App\Models\Charge;
use App\Models\CleaningRequest;
use App\Models\FoodOrder;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        // Reservation 1: Checked-in
        $reservation1 = Reservation::create([
            'guest_id' => 1,
            'room_id' => 1,
            'check_in_date' => now()->subDays(2),
            'check_out_date' => now()->addDays(3),
            'number_of_guests' => 1,
            'status' => 'checked_in',
            'total_amount' => 400.00,
            'special_requests' => 'High floor preferred, late checkout if available',
            'managed_by' => 1,
        ]);

        // Add charges to reservation 1
        Charge::create([
            'reservation_id' => $reservation1->id,
            'service_id' => 1,
            'description' => 'Room Service Breakfast',
            'amount' => 15.00,
            'charge_type' => 'service',
            'status' => 'paid',
        ]);

        // Add invoice for reservation 1
        $invoice1 = Invoice::create([
            'invoice_number' => 'INV-2024-00001',
            'reservation_id' => $reservation1->id,
            'subtotal' => 415.00,
            'tax' => 41.50,
            'discount' => 0,
            'total_amount' => 456.50,
            'status' => 'paid',
            'issue_date' => now()->subDays(2),
            'due_date' => now()->addDays(3),
            'paid_date' => now(),
            'payment_method' => 'card',
        ]);

        // Add payment for reservation 1
        Payment::create([
            'reservation_id' => $reservation1->id,
            'invoice_id' => $invoice1->id,
            'amount' => 456.50,
            'payment_method' => 'card',
            'status' => 'completed',
            'transaction_id' => 'TXN-'.time().'-001',
            'processed_by' => 1,
            'paid_at' => now(),
        ]);

        // Add food orders for reservation 1
        FoodOrder::create([
            'reservation_id' => $reservation1->id,
            'food_id' => 1,
            'quantity' => 2,
            'price' => 8.99,
            'special_notes' => 'Extra butter on toast',
            'status' => 'delivered',
            'ordered_at' => now()->subDays(1),
            'delivered_at' => now()->subDays(1)->addHours(1),
        ]);

        // Add cleaning request for reservation 1
        CleaningRequest::create([
            'reservation_id' => $reservation1->id,
            'room_id' => 1,
            'request_type' => 'towels',
            'description' => 'Need extra towels and pillows',
            'priority' => 'medium',
            'status' => 'completed',
            'requested_at' => now()->subHours(2),
            'completed_at' => now()->subHours(1),
        ]);

        // Reservation 2: Confirmed
        $reservation2 = Reservation::create([
            'guest_id' => 2,
            'room_id' => 3,
            'check_in_date' => now()->addDays(5),
            'check_out_date' => now()->addDays(8),
            'number_of_guests' => 2,
            'status' => 'confirmed',
            'total_amount' => 360.00,
            'special_requests' => 'Honeymoon suite, champagne and flowers appreciated',
            'managed_by' => 2,
        ]);

        // Add invoice for reservation 2
        $invoice2 = Invoice::create([
            'invoice_number' => 'INV-2024-00002',
            'reservation_id' => $reservation2->id,
            'subtotal' => 360.00,
            'tax' => 36.00,
            'discount' => 0,
            'total_amount' => 396.00,
            'status' => 'sent',
            'issue_date' => now(),
            'due_date' => now()->addDays(5),
            'payment_method' => null,
        ]);

        // Reservation 3: Pending
        $reservation3 = Reservation::create([
            'guest_id' => 3,
            'room_id' => 6,
            'check_in_date' => now()->addDays(10),
            'check_out_date' => now()->addDays(12),
            'number_of_guests' => 3,
            'status' => 'pending',
            'total_amount' => 240.00,
            'special_requests' => null,
            'managed_by' => 3,
        ]);

        // Add charges to reservation 3
        Charge::create([
            'reservation_id' => $reservation3->id,
            'service_id' => 5,
            'description' => 'Spa Services',
            'amount' => 80.00,
            'charge_type' => 'service',
            'status' => 'pending',
        ]);

        // Reservation 4: Cancelled
        $reservation4 = Reservation::create([
            'guest_id' => 4,
            'room_id' => 11,
            'check_in_date' => now()->subDays(15),
            'check_out_date' => now()->subDays(13),
            'number_of_guests' => 2,
            'status' => 'cancelled',
            'total_amount' => 360.00,
            'special_requests' => 'Cancelled due to emergency',
            'managed_by' => 1,
        ]);

        // Reservation 5: Checked out
        $reservation5 = Reservation::create([
            'guest_id' => 5,
            'room_id' => 8,
            'check_in_date' => now()->subDays(8),
            'check_out_date' => now()->subDays(3),
            'number_of_guests' => 2,
            'status' => 'checked_out',
            'total_amount' => 600.00,
            'special_requests' => 'Corporate rate applied',
            'managed_by' => 2,
        ]);

        // Add charges to reservation 5
        Charge::create([
            'reservation_id' => $reservation5->id,
            'service_id' => 3,
            'description' => 'Room Service Dinner',
            'amount' => 25.00,
            'charge_type' => 'service',
            'status' => 'paid',
        ]);

        // Add invoice for reservation 5
        $invoice5 = Invoice::create([
            'invoice_number' => 'INV-2024-00005',
            'reservation_id' => $reservation5->id,
            'subtotal' => 625.00,
            'tax' => 62.50,
            'discount' => 0,
            'total_amount' => 687.50,
            'status' => 'paid',
            'issue_date' => now()->subDays(3),
            'due_date' => now(),
            'paid_date' => now()->subDays(2),
            'payment_method' => 'card',
        ]);

        // Add payment for reservation 5
        Payment::create([
            'reservation_id' => $reservation5->id,
            'invoice_id' => $invoice5->id,
            'amount' => 687.50,
            'payment_method' => 'card',
            'status' => 'completed',
            'transaction_id' => 'TXN-'.time().'-005',
            'processed_by' => 2,
            'paid_at' => now()->subDays(2),
        ]);

        // Add food orders for reservation 5
        FoodOrder::create([
            'reservation_id' => $reservation5->id,
            'food_id' => 7,
            'quantity' => 1,
            'price' => 24.99,
            'special_notes' => 'Medium rare, side vegetables',
            'status' => 'delivered',
            'ordered_at' => now()->subDays(4),
            'delivered_at' => now()->subDays(4)->addHours(1),
        ]);

        FoodOrder::create([
            'reservation_id' => $reservation5->id,
            'food_id' => 14,
            'quantity' => 2,
            'price' => 8.99,
            'special_notes' => null,
            'status' => 'delivered',
            'ordered_at' => now()->subDays(4),
            'delivered_at' => now()->subDays(4)->addHours(2),
        ]);

        // Add cleaning request for reservation 5
        CleaningRequest::create([
            'reservation_id' => $reservation5->id,
            'room_id' => 8,
            'request_type' => 'cleaning',
            'description' => 'Full room cleaning before checkout',
            'priority' => 'high',
            'status' => 'completed',
            'requested_at' => now()->subDays(3),
            'completed_at' => now()->subDays(3)->addHours(2),
        ]);
    }
}
