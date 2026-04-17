<?php

use App\Models\Food;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Browser Automation - Admin Room Management', function () {
    beforeEach(function () {
        $this->admin = User::factory()->create(['role' => 'admin']);
    });

    describe('Room Index', function () {
        test('admin can view rooms list page', function () {
            Room::factory(5)->create();

            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index'));

            $response->assertStatus(200);
            $response->assertViewIs('admin.rooms.index');
            $response->assertViewHas('rooms');
        });

        test('rooms list displays all rooms', function () {
            $rooms = Room::factory(3)->create();

            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index'));

            foreach ($rooms as $room) {
                $response->assertSee($room->room_number);
                $response->assertSee($room->type);
            }
        });
    });

    describe('Create Room', function () {
        test('admin can view create room form', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.create'));

            $response->assertStatus(200);
            $response->assertViewIs('admin.rooms.create');
        });

        test('admin can create new room', function () {
            $response = $this->actingAs($this->admin)
                ->post(route('admin.rooms.store'), [
                    'room_number' => 'A101',
                    'type' => 'single',
                    'capacity' => 1,
                    'price_per_night' => 5000,
                    'floor' => 1,
                ]);

            $response->assertRedirect(route('admin.rooms.index'));
            $response->assertSessionHas('success', 'Room added successfully.');
            $this->assertDatabaseHas('rooms', [
                'room_number' => 'A101',
                'type' => 'single',
                'price_per_night' => 5000,
            ]);
        });

        test('room number must be unique', function () {
            Room::factory()->create(['room_number' => 'A101']);

            $response = $this->actingAs($this->admin)
                ->post(route('admin.rooms.store'), [
                    'room_number' => 'A101',
                    'type' => 'double',
                    'capacity' => 2,
                    'price_per_night' => 7000,
                    'floor' => 1,
                ]);

            $response->assertSessionHasErrors('room_number');
        });

        test('room type must be valid', function () {
            $response = $this->actingAs($this->admin)
                ->post(route('admin.rooms.store'), [
                    'room_number' => 'A101',
                    'type' => 'invalid-type',
                    'capacity' => 2,
                    'price_per_night' => 7000,
                    'floor' => 1,
                ]);

            $response->assertSessionHasErrors('type');
        });
    });

    describe('Edit Room', function () {
        test('admin can view edit room form', function () {
            $room = Room::factory()->create();

            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.edit', $room));

            $response->assertStatus(200);
            $response->assertViewIs('admin.rooms.edit');
            $response->assertViewHas('room', $room);
        });

        test('admin can update room', function () {
            $room = Room::factory()->create();

            $response = $this->actingAs($this->admin)
                ->put(route('admin.rooms.update', $room), [
                    'room_number' => 'B202',
                    'type' => 'suite',
                    'capacity' => 4,
                    'price_per_night' => 12000,
                    'status' => 'available',
                    'floor' => 2,
                ]);

            $response->assertRedirect(route('admin.rooms.index'));
            $response->assertSessionHas('success', 'Room updated successfully.');
            $this->assertDatabaseHas('rooms', [
                'id' => $room->id,
                'room_number' => 'B202',
                'type' => 'suite',
            ]);
        });
    });

    describe('Delete Room', function () {
        test('admin can delete room', function () {
            $room = Room::factory()->create();

            $response = $this->actingAs($this->admin)
                ->delete(route('admin.rooms.destroy', $room));

            $response->assertRedirect(route('admin.rooms.index'));
            $response->assertSessionHas('success', 'Room deleted.');
            $this->assertDatabaseMissing('rooms', ['id' => $room->id]);
        });
    });
});

describe('Browser Automation - Admin Reservation Management', function () {
    beforeEach(function () {
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->guest = User::factory()->create(['role' => 'guest']);
        $this->room = Room::factory()->create(['status' => 'available']);
    });

    describe('Reservation Index', function () {
        test('admin can view reservations list', function () {
            Reservation::factory(3)->create();

            $response = $this->actingAs($this->admin)
                ->get(route('admin.reservations.index'));

            $response->assertStatus(200);
            $response->assertViewIs('admin.reservations.index');
            $response->assertViewHas('reservations');
        });

        test('admin can search reservations', function () {
            $reservation = Reservation::factory()->create();

            $response = $this->actingAs($this->admin)
                ->get(route('admin.reservations.index'), ['search' => $reservation->user->name]);

            $response->assertStatus(200);
            $response->assertSee($reservation->user->name);
        });

        test('admin can filter by status', function () {
            Reservation::factory()->create(['status' => 'confirmed']);
            Reservation::factory()->create(['status' => 'cancelled']);

            $response = $this->actingAs($this->admin)
                ->get(route('admin.reservations.index'), ['status' => 'confirmed']);

            $response->assertStatus(200);
        });
    });

    describe('Create Reservation', function () {
        test('admin can view create reservation form', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.reservations.create'));

            $response->assertStatus(200);
            $response->assertViewIs('admin.reservations.create');
            $response->assertViewHas('guests');
            $response->assertViewHas('rooms');
        });

        test('admin can create reservation', function () {
            $checkIn = now()->addDays(1)->toDateString();
            $checkOut = now()->addDays(5)->toDateString();

            $response = $this->actingAs($this->admin)
                ->post(route('admin.reservations.store'), [
                    'user_id' => $this->guest->id,
                    'room_id' => $this->room->id,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'guests' => 2,
                ]);

            $response->assertRedirect(route('admin.reservations.index'));
            $response->assertSessionHas('success', 'Reservation created.');
            $this->assertDatabaseHas('reservations', [
                'user_id' => $this->guest->id,
                'room_id' => $this->room->id,
                'status' => 'confirmed',
            ]);
        });

        test('cannot create overlapping reservations', function () {
            $checkIn = now()->addDays(1)->toDateString();
            $checkOut = now()->addDays(5)->toDateString();

            Reservation::factory()->create([
                'room_id' => $this->room->id,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'status' => 'confirmed',
            ]);

            $response = $this->actingAs($this->admin)
                ->post(route('admin.reservations.store'), [
                    'user_id' => $this->guest->id,
                    'room_id' => $this->room->id,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'guests' => 2,
                ]);

            $response->assertSessionHasErrors('room_id');
        });
    });

    describe('View Reservation', function () {
        test('admin can view reservation details', function () {
            $reservation = Reservation::factory()->create();
            $reservation->load('user', 'room', 'foodOrders.food', 'payments');

            $response = $this->actingAs($this->admin)
                ->get(route('admin.reservations.show', $reservation));

            $response->assertStatus(200);
            $response->assertViewIs('admin.reservations.show');
            $response->assertViewHas('reservation', $reservation);
            $response->assertSee($reservation->user->name);
            $response->assertSee($reservation->room->room_number);
        });
    });

    describe('Edit Reservation', function () {
        test('admin can view edit reservation form', function () {
            $reservation = Reservation::factory()->create();

            $response = $this->actingAs($this->admin)
                ->get(route('admin.reservations.edit', $reservation));

            $response->assertStatus(200);
            $response->assertViewIs('admin.reservations.edit');
            $response->assertViewHas('reservation', $reservation);
        });

        test('admin can update reservation', function () {
            $reservation = Reservation::factory()->create();
            $newCheckIn = now()->addDays(10)->toDateString();
            $newCheckOut = now()->addDays(15)->toDateString();

            $response = $this->actingAs($this->admin)
                ->put(route('admin.reservations.update', $reservation), [
                    'user_id' => $reservation->user_id,
                    'room_id' => $reservation->room_id,
                    'check_in' => $newCheckIn,
                    'check_out' => $newCheckOut,
                    'guests' => 3,
                    'status' => 'confirmed',
                ]);

            $response->assertRedirect(route('admin.reservations.show', $reservation));
            $response->assertSessionHas('success');
        });
    });

    describe('Payment Recording', function () {
        test('admin can record payment for reservation', function () {
            $reservation = Reservation::factory()->create(['total_amount' => 10000]);

            $response = $this->actingAs($this->admin)
                ->post(route('admin.reservations.pay', $reservation), [
                    'amount' => 5000,
                    'method' => 'card',
                ]);

            $response->assertRedirect();
            $this->assertDatabaseHas('payments', [
                'reservation_id' => $reservation->id,
                'amount' => 5000,
                'method' => 'card',
                'status' => 'completed',
            ]);
        });

        test('payment cannot exceed balance due', function () {
            $reservation = Reservation::factory()->create(['total_amount' => 10000]);

            $response = $this->actingAs($this->admin)
                ->post(route('admin.reservations.pay', $reservation), [
                    'amount' => 15000,
                    'method' => 'card',
                ]);

            $response->assertSessionHasErrors('amount');
        });
    });
});

describe('Browser Automation - Guest Management', function () {
    beforeEach(function () {
        $this->admin = User::factory()->create(['role' => 'admin']);
    });

    describe('Guests List', function () {
        test('admin can view guests list', function () {
            User::factory(5)->create(['role' => 'guest']);

            $response = $this->actingAs($this->admin)
                ->get(route('admin.guests.index'));

            $response->assertStatus(200);
            $response->assertViewIs('admin.guests.index');
            $response->assertViewHas('guests');
        });
    });

    describe('Create Guest', function () {
        test('admin can view create guest form', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.guests.create'));

            $response->assertStatus(200);
            $response->assertViewIs('admin.guests.create');
        });

        test('admin can create guest', function () {
            $response = $this->actingAs($this->admin)
                ->post(route('admin.guests.store'), [
                    'name' => 'New Guest',
                    'email' => 'guest@example.com',
                    'phone' => '1234567890',
                    'password' => 'password123',
                ]);

            $response->assertRedirect(route('admin.guests.index'));
            $response->assertSessionHas('success', 'Guest created successfully.');
            $this->assertDatabaseHas('users', [
                'email' => 'guest@example.com',
                'role' => 'guest',
            ]);
        });
    });
});

describe('Browser Automation - Food Management', function () {
    beforeEach(function () {
        $this->admin = User::factory()->create(['role' => 'admin']);
    });

    describe('Foods List', function () {
        test('admin can view foods list', function () {
            Food::factory(5)->create();

            $response = $this->actingAs($this->admin)
                ->get(route('admin.foods.index'));

            $response->assertStatus(200);
            $response->assertViewIs('admin.foods.index');
            $response->assertViewHas('foods');
        });
    });

    describe('Create Food', function () {
        test('admin can view create food form', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.foods.create'));

            $response->assertStatus(200);
            $response->assertViewIs('admin.foods.create');
        });

        test('admin can create food item', function () {
            $response = $this->actingAs($this->admin)
                ->post(route('admin.foods.store'), [
                    'name' => 'Biryani',
                    'category' => 'lunch',
                    'price' => 300,
                    'available' => true,
                ]);

            $response->assertRedirect(route('admin.foods.index'));
            $response->assertSessionHas('success', 'Food item added.');
            $this->assertDatabaseHas('foods', [
                'name' => 'Biryani',
                'category' => 'lunch',
                'price' => 300,
            ]);
        });

        test('food category must be valid', function () {
            $response = $this->actingAs($this->admin)
                ->post(route('admin.foods.store'), [
                    'name' => 'Biryani',
                    'category' => 'invalid-category',
                    'price' => 300,
                ]);

            $response->assertSessionHasErrors('category');
        });
    });

    describe('Edit Food', function () {
        test('admin can view edit food form', function () {
            $food = Food::factory()->create();

            $response = $this->actingAs($this->admin)
                ->get(route('admin.foods.edit', $food));

            $response->assertStatus(200);
            $response->assertViewIs('admin.foods.edit');
            $response->assertViewHas('food', $food);
        });

        test('admin can update food item', function () {
            $food = Food::factory()->create();

            $response = $this->actingAs($this->admin)
                ->put(route('admin.foods.update', $food), [
                    'name' => 'Updated Biryani',
                    'category' => 'dinner',
                    'price' => 350,
                    'available' => true,
                ]);

            $response->assertRedirect(route('admin.foods.index'));
            $response->assertSessionHas('success', 'Food item updated.');
            $this->assertDatabaseHas('foods', [
                'id' => $food->id,
                'name' => 'Updated Biryani',
                'price' => 350,
            ]);
        });
    });

    describe('Delete Food', function () {
        test('admin can delete food item', function () {
            $food = Food::factory()->create();

            $response = $this->actingAs($this->admin)
                ->delete(route('admin.foods.destroy', $food));

            $response->assertRedirect(route('admin.foods.index'));
            $response->assertSessionHas('success', 'Food item deleted.');
            $this->assertDatabaseMissing('foods', ['id' => $food->id]);
        });
    });
});

describe('Browser Automation - Payments', function () {
    beforeEach(function () {
        $this->admin = User::factory()->create(['role' => 'admin']);
    });

    describe('Payments List', function () {
        test('admin can view payments list', function () {
            Payment::factory(5)->create();

            $response = $this->actingAs($this->admin)
                ->get(route('admin.payments.index'));

            $response->assertStatus(200);
            $response->assertViewIs('admin.payments.index');
            $response->assertViewHas('payments');
        });
    });
});

describe('Browser Automation - Food Orders', function () {
    beforeEach(function () {
        $this->admin = User::factory()->create(['role' => 'admin']);
    });

    describe('Food Orders List', function () {
        test('admin can view food orders list', function () {
            $orders = \App\Models\FoodOrder::factory(5)->create();

            $response = $this->actingAs($this->admin)
                ->get(route('admin.food-orders.index'));

            $response->assertStatus(200);
            $response->assertViewIs('admin.food-orders.index');
            $response->assertViewHas('orders');
        });
    });

    describe('Update Order Status', function () {
        test('admin can update food order status', function () {
            $order = \App\Models\FoodOrder::factory()->create(['status' => 'pending']);

            $response = $this->actingAs($this->admin)
                ->patch(route('admin.food-orders.status', [$order, 'delivered']));

            $response->assertRedirect();
            $this->assertDatabaseHas('food_orders', [
                'id' => $order->id,
                'status' => 'delivered',
            ]);
        });
    });
});

describe('Browser Automation - Dashboard', function () {
    describe('Admin Dashboard', function () {
        test('admin can view dashboard', function () {
            $admin = User::factory()->create(['role' => 'admin']);

            $response = $this->actingAs($admin)
                ->get(route('admin.dashboard'));

            $response->assertStatus(200);
            $response->assertViewIs('admin.dashboard');
            $response->assertViewHas('totalRooms');
            $response->assertViewHas('availableRooms');
            $response->assertViewHas('totalGuests');
            $response->assertViewHas('totalReservations');
        });
    });

    describe('Guest Dashboard', function () {
        test('guest can view dashboard', function () {
            $guest = User::factory()->create(['role' => 'guest']);

            $response = $this->actingAs($guest)
                ->get(route('guest.dashboard'));

            $response->assertStatus(200);
            $response->assertViewIs('guest.dashboard');
        });
    });
});

describe('Browser Automation - Guest Booking', function () {
    beforeEach(function () {
        $this->guest = User::factory()->create(['role' => 'guest']);
        $this->room = Room::factory()->create(['status' => 'available']);
    });

    describe('View Available Rooms', function () {
        test('guest can view available rooms', function () {
            Room::factory(5)->create(['status' => 'available']);

            $response = $this->actingAs($this->guest)
                ->get(route('guest.rooms'));

            $response->assertStatus(200);
            $response->assertViewIs('guest.rooms');
            $response->assertViewHas('rooms');
        });
    });

    describe('Book Room', function () {
        test('guest can book a room', function () {
            $checkIn = now()->addDays(1)->toDateString();
            $checkOut = now()->addDays(5)->toDateString();

            $response = $this->actingAs($this->guest)
                ->post(route('guest.book'), [
                    'room_id' => $this->room->id,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'guests' => 2,
                ]);

            $response->assertRedirect(route('guest.dashboard'));
            $response->assertSessionHas('success', 'Booking request submitted!');
            $this->assertDatabaseHas('reservations', [
                'user_id' => $this->guest->id,
                'room_id' => $this->room->id,
                'status' => 'pending',
            ]);
        });

        test('guest cannot book if already has active reservation', function () {
            Reservation::factory()->create([
                'user_id' => $this->guest->id,
                'status' => 'confirmed',
            ]);

            $checkIn = now()->addDays(10)->toDateString();
            $checkOut = now()->addDays(15)->toDateString();

            $response = $this->actingAs($this->guest)
                ->post(route('guest.book'), [
                    'room_id' => $this->room->id,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'guests' => 2,
                ]);

            $response->assertSessionHasErrors('booking');
        });
    });

    describe('View Reservation', function () {
        test('guest can view their reservation', function () {
            $reservation = Reservation::factory()->create(['user_id' => $this->guest->id]);
            $reservation->load('room', 'foodOrders.food', 'payments');

            $response = $this->actingAs($this->guest)
                ->get(route('guest.reservation.show', $reservation));

            $response->assertStatus(200);
            $response->assertViewIs('guest.reservation');
            $response->assertViewHas('reservation', $reservation);
        });

        test('guest cannot view other guests reservation', function () {
            $otherGuest = User::factory()->create(['role' => 'guest']);
            $reservation = Reservation::factory()->create(['user_id' => $otherGuest->id]);

            $response = $this->actingAs($this->guest)
                ->get(route('guest.reservation.show', $reservation));

            $response->assertStatus(403);
        });
    });

    describe('View Menu', function () {
        test('guest can view menu', function () {
            Food::factory(10)->create(['available' => true]);

            $response = $this->actingAs($this->guest)
                ->get(route('guest.menu'));

            $response->assertStatus(200);
            $response->assertViewIs('guest.menu');
            $response->assertViewHas('foods');
        });
    });

    describe('Order Food', function () {
        test('guest can order food during checked in reservation', function () {
            $reservation = Reservation::factory()->create([
                'user_id' => $this->guest->id,
                'status' => 'checked_in',
            ]);
            $food = Food::factory()->create();

            $response = $this->actingAs($this->guest)
                ->post(route('guest.order-food'), [
                    'reservation_id' => $reservation->id,
                    'food_id' => $food->id,
                    'quantity' => 2,
                ]);

            $response->assertRedirect();
            $this->assertDatabaseHas('food_orders', [
                'reservation_id' => $reservation->id,
                'food_id' => $food->id,
                'quantity' => 2,
            ]);
        });

        test('guest cannot order food without checked in reservation', function () {
            $reservation = Reservation::factory()->create([
                'user_id' => $this->guest->id,
                'status' => 'pending',
            ]);
            $food = Food::factory()->create();

            $response = $this->actingAs($this->guest)
                ->post(route('guest.order-food'), [
                    'reservation_id' => $reservation->id,
                    'food_id' => $food->id,
                    'quantity' => 2,
                ]);

            $response->assertStatus(404);
        });
    });
});

describe('Browser Automation - Access Control', function () {
    test('unauthenticated user cannot access admin routes', function () {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect('/login');
    });

    test('guest user cannot access admin routes', function () {
        $guest = User::factory()->create(['role' => 'guest']);

        $response = $this->actingAs($guest)
            ->get(route('admin.dashboard'));

        $response->assertStatus(403);
    });

    test('unauthenticated user cannot access guest routes', function () {
        $response = $this->get(route('guest.dashboard'));
        $response->assertRedirect('/login');
    });

    test('admin can access admin routes', function () {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
    });
});

describe('Browser Automation - Home Page', function () {
    test('anyone can view home page', function () {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('welcome');
    });
});
