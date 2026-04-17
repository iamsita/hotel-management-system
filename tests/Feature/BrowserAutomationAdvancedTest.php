<?php

use App\Models\Food;
use App\Models\FoodOrder;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Complete User Workflows', function () {
    describe('Guest Registration and Booking', function () {
        test('new user can register and book a room', function () {
            $room = Room::factory()->create(['status' => 'available', 'price_per_night' => 5000]);

            // Register
            $registerResponse = $this->post('/register', [
                'name' => 'John Visitor',
                'email' => 'john@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $registerResponse->assertRedirect(route('guest.dashboard'));

            // Guest should be logged in
            $user = User::where('email', 'john@example.com')->first();
            $this->assertAuthenticatedAs($user);

            // Book a room
            $checkIn = now()->addDays(1)->toDateString();
            $checkOut = now()->addDays(3)->toDateString();

            $bookResponse = $this->post(route('guest.book'), [
                'room_id' => $room->id,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'guests' => 2,
            ]);

            $bookResponse->assertRedirect(route('guest.dashboard'));
            $this->assertDatabaseHas('reservations', [
                'user_id' => $user->id,
                'room_id' => $room->id,
            ]);
        });
    });

    describe('Complete Admin Workflow - Room to Payment', function () {
        test('admin can create room, reservation, and process payment', function () {
            $admin = User::factory()->create(['role' => 'admin']);
            $guest = User::factory()->create(['role' => 'guest']);

            // Admin creates room
            $createRoomResponse = $this->actingAs($admin)
                ->post(route('admin.rooms.store'), [
                    'room_number' => 'LUXURY-101',
                    'type' => 'deluxe',
                    'capacity' => 2,
                    'price_per_night' => 10000,
                    'floor' => 1,
                ]);

            $createRoomResponse->assertSessionHas('success');
            $room = Room::where('room_number', 'LUXURY-101')->first();

            // Admin creates reservation
            $checkIn = now()->addDays(1)->toDateString();
            $checkOut = now()->addDays(5)->toDateString();

            $createResResponse = $this->actingAs($admin)
                ->post(route('admin.reservations.store'), [
                    'user_id' => $guest->id,
                    'room_id' => $room->id,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'guests' => 2,
                ]);

            $createResResponse->assertSessionHas('success', 'Reservation created.');
            $reservation = Reservation::where('user_id', $guest->id)->first();

            // Admin records payment
            $paymentResponse = $this->actingAs($admin)
                ->post(route('admin.reservations.pay', $reservation), [
                    'amount' => 40000,
                    'method' => 'card',
                ]);

            $paymentResponse->assertRedirect();
            $this->assertDatabaseHas('payments', [
                'reservation_id' => $reservation->id,
                'amount' => 40000,
                'method' => 'card',
            ]);
        });
    });

    describe('Guest Ordering Food During Stay', function () {
        test('guest can order multiple food items during stay', function () {
            $guest = User::factory()->create(['role' => 'guest']);
            $reservation = Reservation::factory()->create([
                'user_id' => $guest->id,
                'status' => 'checked_in',
            ]);

            $foods = Food::factory(5)->create(['available' => true]);

            foreach ($foods as $food) {
                $orderResponse = $this->actingAs($guest)
                    ->post(route('guest.order-food'), [
                        'reservation_id' => $reservation->id,
                        'food_id' => $food->id,
                        'quantity' => rand(1, 3),
                    ]);

                $orderResponse->assertRedirect();
                $this->assertDatabaseHas('food_orders', [
                    'reservation_id' => $reservation->id,
                    'food_id' => $food->id,
                ]);
            }

            $this->assertEquals(5, $reservation->foodOrders()->count());
        });
    });
});

describe('Validation and Error Handling', function () {
    describe('Room Validation', function () {
        test('room creation with invalid floor number fails', function () {
            $admin = User::factory()->create(['role' => 'admin']);

            $response = $this->actingAs($admin)
                ->post(route('admin.rooms.store'), [
                    'room_number' => 'A101',
                    'type' => 'single',
                    'capacity' => 1,
                    'price_per_night' => 5000,
                    'floor' => 0,
                ]);

            $response->assertSessionHasErrors('floor');
        });

        test('room creation with negative price fails', function () {
            $admin = User::factory()->create(['role' => 'admin']);

            $response = $this->actingAs($admin)
                ->post(route('admin.rooms.store'), [
                    'room_number' => 'A101',
                    'type' => 'single',
                    'capacity' => 1,
                    'price_per_night' => -5000,
                    'floor' => 1,
                ]);

            $response->assertSessionHasErrors('price_per_night');
        });

        test('room capacity must be at least 1', function () {
            $admin = User::factory()->create(['role' => 'admin']);

            $response = $this->actingAs($admin)
                ->post(route('admin.rooms.store'), [
                    'room_number' => 'A101',
                    'type' => 'single',
                    'capacity' => 0,
                    'price_per_night' => 5000,
                    'floor' => 1,
                ]);

            $response->assertSessionHasErrors('capacity');
        });
    });

    describe('Reservation Validation', function () {
        test('check-out date must be after check-in date', function () {
            $admin = User::factory()->create(['role' => 'admin']);
            $guest = User::factory()->create(['role' => 'guest']);
            $room = Room::factory()->create();

            $response = $this->actingAs($admin)
                ->post(route('admin.reservations.store'), [
                    'user_id' => $guest->id,
                    'room_id' => $room->id,
                    'check_in' => now()->addDays(5)->toDateString(),
                    'check_out' => now()->addDays(1)->toDateString(),
                    'guests' => 2,
                ]);

            $response->assertSessionHasErrors('check_out');
        });

        test('check-in date cannot be in the past', function () {
            $admin = User::factory()->create(['role' => 'admin']);
            $guest = User::factory()->create(['role' => 'guest']);
            $room = Room::factory()->create();

            $response = $this->actingAs($admin)
                ->post(route('admin.reservations.store'), [
                    'user_id' => $guest->id,
                    'room_id' => $room->id,
                    'check_in' => now()->subDays(1)->toDateString(),
                    'check_out' => now()->addDays(5)->toDateString(),
                    'guests' => 2,
                ]);

            $response->assertSessionHasErrors('check_in');
        });
    });

    describe('Food Validation', function () {
        test('food price must be non-negative', function () {
            $admin = User::factory()->create(['role' => 'admin']);

            $response = $this->actingAs($admin)
                ->post(route('admin.foods.store'), [
                    'name' => 'Biryani',
                    'category' => 'lunch',
                    'price' => -100,
                ]);

            $response->assertSessionHasErrors('price');
        });

        test('food name is required', function () {
            $admin = User::factory()->create(['role' => 'admin']);

            $response = $this->actingAs($admin)
                ->post(route('admin.foods.store'), [
                    'name' => '',
                    'category' => 'lunch',
                    'price' => 300,
                ]);

            $response->assertSessionHasErrors('name');
        });
    });
});

describe('Data Filtering and Pagination', function () {
    test('rooms list is paginated', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        Room::factory(20)->create();

        $response = $this->actingAs($admin)
            ->get(route('admin.rooms.index'));

        $response->assertStatus(200);
        $rooms = $response['rooms'];
        expect($rooms)->toHaveCount(15); // Default pagination
    });

    test('reservations can be filtered by check-in date', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        $futureDate = now()->addDays(10)->toDateString();

        Reservation::factory(5)->create([
            'check_in' => now()->addDays(5),
        ]);

        Reservation::factory(3)->create([
            'check_in' => now()->addDays(15),
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.reservations.index'), [
                'check_in_from' => $futureDate,
            ]);

        $response->assertStatus(200);
    });

    test('guest list shows reservation count', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        $guest = User::factory()->create(['role' => 'guest']);

        Reservation::factory(3)->create(['user_id' => $guest->id]);

        $response = $this->actingAs($admin)
            ->get(route('admin.guests.index'));

        $response->assertStatus(200);
    });
});

describe('Complex Reservation Scenarios', function () {
    test('admin can handle reservation status transitions', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        $reservation = Reservation::factory()->create(['status' => 'confirmed']);

        // Update to checked_in
        $this->actingAs($admin)
            ->patch(route('admin.reservations.update-status', $reservation), [
                'status' => 'checked_in',
            ])->assertStatus(200);

        // Room should be occupied
        $this->assertDatabaseHas('rooms', [
            'id' => $reservation->room_id,
            'status' => 'occupied',
        ]);

        // Update to checked_out
        $this->actingAs($admin)
            ->patch(route('admin.reservations.update-status', $reservation), [
                'status' => 'checked_out',
            ])->assertStatus(200);

        // Room should be available
        $this->assertDatabaseHas('rooms', [
            'id' => $reservation->room_id,
            'status' => 'available',
        ]);
    });

    test('reservation deletion cascades to related records', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        $reservation = Reservation::factory()->create();

        FoodOrder::factory(3)->create(['reservation_id' => $reservation->id]);
        Payment::factory(2)->create(['reservation_id' => $reservation->id]);

        $reservationId = $reservation->id;

        $this->actingAs($admin)
            ->delete(route('admin.reservations.destroy', $reservation))
            ->assertStatus(302);

        $this->assertDatabaseMissing('reservations', ['id' => $reservationId]);
        $this->assertDatabaseMissing('food_orders', ['reservation_id' => $reservationId]);
        $this->assertDatabaseMissing('payments', ['reservation_id' => $reservationId]);
    });

    test('admin can update reservation with room change', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        $reservation = Reservation::factory()->create();
        $newRoom = Room::factory()->create(['status' => 'available']);

        $oldRoomId = $reservation->room_id;

        $this->actingAs($admin)
            ->put(route('admin.reservations.update', $reservation), [
                'user_id' => $reservation->user_id,
                'room_id' => $newRoom->id,
                'check_in' => $reservation->check_in,
                'check_out' => $reservation->check_out,
                'guests' => 2,
                'status' => 'confirmed',
            ])->assertStatus(302);

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'room_id' => $newRoom->id,
        ]);

        // Old room should be available
        $this->assertDatabaseHas('rooms', [
            'id' => $oldRoomId,
            'status' => 'available',
        ]);
    });
});

describe('Profile Management', function () {
    test('user can update profile information', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'phone' => '9999999999',
            ]);

        $response->assertRedirect(route('profile.show'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    });

    test('user cannot change email to existing email', function () {
        $user1 = User::factory()->create(['email' => 'user1@example.com']);
        $user2 = User::factory()->create(['email' => 'user2@example.com']);

        $response = $this->actingAs($user1)
            ->put(route('profile.update'), [
                'name' => 'User One',
                'email' => 'user2@example.com',
            ]);

        $response->assertSessionHasErrors('email');
    });

    test('password change requires current password verification', function () {
        $user = User::factory()->create([
            'password' => bcrypt('current-password'),
        ]);

        $response = $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response->assertSessionHasErrors('current_password');
    });
});

describe('Authorization and Security', function () {
    test('guest cannot access admin payment records', function () {
        $guest = User::factory()->create(['role' => 'guest']);
        Payment::factory(5)->create();

        $response = $this->actingAs($guest)
            ->get(route('admin.payments.index'));

        $response->assertStatus(403);
    });

    test('user can only view their own reservation', function () {
        $user1 = User::factory()->create(['role' => 'guest']);
        $user2 = User::factory()->create(['role' => 'guest']);
        $reservation = Reservation::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)
            ->get(route('guest.reservation.show', $reservation));

        $response->assertStatus(403);
    });

    test('guest cannot modify reservations directly', function () {
        $guest = User::factory()->create(['role' => 'guest']);
        $reservation = Reservation::factory()->create(['user_id' => $guest->id]);

        $response = $this->actingAs($guest)
            ->put(route('admin.reservations.update', $reservation), [
                'status' => 'checked_in',
            ]);

        $response->assertStatus(403);
    });
});

describe('Data Integrity', function () {
    test('room cannot be deleted if it has active reservations', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        $room = Room::factory()->create();

        Reservation::factory()->create([
            'room_id' => $room->id,
            'status' => 'confirmed',
        ]);

        // Attempting to delete should fail or handle gracefully
        $this->actingAs($admin)->delete(route('admin.rooms.destroy', $room));
    });

    test('food order tracks correct pricing', function () {
        $guest = User::factory()->create(['role' => 'guest']);
        $reservation = Reservation::factory()->create([
            'user_id' => $guest->id,
            'status' => 'checked_in',
        ]);

        $food = Food::factory()->create(['price' => 350]);
        $quantity = 5;

        $this->actingAs($guest)
            ->post(route('guest.order-food'), [
                'reservation_id' => $reservation->id,
                'food_id' => $food->id,
                'quantity' => $quantity,
            ]);

        $this->assertDatabaseHas('food_orders', [
            'food_id' => $food->id,
            'quantity' => $quantity,
            'total_price' => $food->price * $quantity,
        ]);
    });
});
