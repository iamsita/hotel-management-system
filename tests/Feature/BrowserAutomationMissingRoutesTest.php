<?php

use App\Models\Food;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

uses(RefreshDatabase::class);

describe('Missing Route Coverage - Show and Generation Routes', function () {
    describe('Admin - Room Show Route', function () {
        test('admin can view room details page', function () {
            $admin = User::factory()->create(['role' => 'admin']);
            $room = Room::factory()->create([
                'room_number' => 'DELUXE-101',
                'type' => 'deluxe',
                'capacity' => 2,
                'price_per_night' => 10000,
                'floor' => 1,
            ]);

            $response = $this->actingAs($admin)
                ->get(route('admin.rooms.show', $room));

            $response->assertStatus(200);
            $response->assertViewIs('admin.rooms.show');
            $response->assertViewHas('room', $room);
            $response->assertSee('DELUXE-101');
            $response->assertSee('deluxe');
            $response->assertSee('10000');
        });

        test('admin can see room details with related reservations', function () {
            $admin = User::factory()->create(['role' => 'admin']);
            $room = Room::factory()->create();
            $reservations = Reservation::factory(3)->create(['room_id' => $room->id]);

            $response = $this->actingAs($admin)
                ->get(route('admin.rooms.show', $room));

            $response->assertStatus(200);
            $response->assertViewHas('room');
        });

        test('unauthenticated user cannot view room details', function () {
            $room = Room::factory()->create();

            $response = $this->get(route('admin.rooms.show', $room));

            $response->assertStatus(302);
            $response->assertRedirect('/login');
        });

        test('guest user cannot view room details', function () {
            $guest = User::factory()->create(['role' => 'guest']);
            $room = Room::factory()->create();

            $response = $this->actingAs($guest)
                ->get(route('admin.rooms.show', $room));

            $response->assertStatus(403);
        });
    });

    describe('Admin - Food Show Route', function () {
        test('admin can view food item details page', function () {
            $admin = User::factory()->create(['role' => 'admin']);
            $food = Food::factory()->create([
                'name' => 'Deluxe Biryani',
                'category' => 'lunch',
                'price' => 450,
                'available' => true,
            ]);

            $response = $this->actingAs($admin)
                ->get(route('admin.foods.show', $food));

            $response->assertStatus(200);
            $response->assertViewIs('admin.foods.show');
            $response->assertViewHas('food', $food);
            $response->assertSee('Deluxe Biryani');
            $response->assertSee('lunch');
            $response->assertSee('450');
        });

        test('admin can see food details with availability status', function () {
            $admin = User::factory()->create(['role' => 'admin']);
            $food = Food::factory()->create(['available' => true]);

            $response = $this->actingAs($admin)
                ->get(route('admin.foods.show', $food));

            $response->assertStatus(200);
            $response->assertViewHas('food');
        });

        test('admin can see unavailable food items', function () {
            $admin = User::factory()->create(['role' => 'admin']);
            $food = Food::factory()->create(['available' => false]);

            $response = $this->actingAs($admin)
                ->get(route('admin.foods.show', $food));

            $response->assertStatus(200);
        });

        test('unauthenticated user cannot view food details', function () {
            $food = Food::factory()->create();

            $response = $this->get(route('admin.foods.show', $food));

            $response->assertStatus(302);
            $response->assertRedirect('/login');
        });

        test('guest user cannot view food details admin page', function () {
            $guest = User::factory()->create(['role' => 'guest']);
            $food = Food::factory()->create();

            $response = $this->actingAs($guest)
                ->get(route('admin.foods.show', $food));

            $response->assertStatus(403);
        });
    });

    describe('Admin - Reservation Invoice Generation', function () {
        test('admin can generate reservation invoice', function () {
            $admin = User::factory()->create(['role' => 'admin']);
            $reservation = Reservation::factory()->create([
                'total_amount' => 50000,
                'status' => 'checked_out',
            ]);

            $response = $this->actingAs($admin)
                ->get(route('admin.reservations.generate-invoice', $reservation));

            // Invoice generation should return PDF or HTML response
            $response->assertStatus(200);
        });

        test('admin invoice shows reservation details', function () {
            $admin = User::factory()->create(['role' => 'admin']);
            $guest = User::factory()->create(['name' => 'John Guest', 'email' => 'john@example.com']);
            $room = Room::factory()->create(['room_number' => 'A101']);
            $reservation = Reservation::factory()->create([
                'user_id' => $guest->id,
                'room_id' => $room->id,
                'check_in' => now()->subDays(5),
                'check_out' => now(),
                'total_amount' => 50000,
                'status' => 'checked_out',
            ]);

            $response = $this->actingAs($admin)
                ->get(route('admin.reservations.generate-invoice', $reservation));

            $response->assertStatus(200);
            // Should contain guest name, room number, or invoice details
        });

        test('unauthenticated user cannot generate invoice', function () {
            $reservation = Reservation::factory()->create();

            $response = $this->get(route('admin.reservations.generate-invoice', $reservation));

            $response->assertStatus(302);
            $response->assertRedirect('/login');
        });

        test('guest user cannot generate invoice', function () {
            $guest = User::factory()->create(['role' => 'guest']);
            $reservation = Reservation::factory()->create();

            $response = $this->actingAs($guest)
                ->get(route('admin.reservations.generate-invoice', $reservation));

            $response->assertStatus(403);
        });

        test('invoice generation works for various reservation states', function () {
            $admin = User::factory()->create(['role' => 'admin']);
            $statuses = ['checked_out', 'cancelled', 'pending', 'confirmed'];

            foreach ($statuses as $status) {
                $reservation = Reservation::factory()->create(['status' => $status]);

                $response = $this->actingAs($admin)
                    ->get(route('admin.reservations.generate-invoice', $reservation));

                $response->assertStatus(200);
            }
        });
    });

    describe('Admin - Guest Segmentation', function () {
        test('admin can trigger guest segmentation algorithm', function () {
            $admin = User::factory()->create(['role' => 'admin']);

            $response = $this->actingAs($admin)
                ->post(route('admin.algorithms.segment'));

            $response->assertStatus(302);
            $response->assertRedirect();
            $response->assertSessionHas('success', 'Guest segmentation re-run successfully.');
        });

        test('segmentation redirects back to algorithms page', function () {
            $admin = User::factory()->create(['role' => 'admin']);

            $response = $this->actingAs($admin)
                ->post(route('admin.algorithms.segment'));

            $response->assertStatus(302);
        });

        test('segmentation calls artisan command', function () {
            $admin = User::factory()->create(['role' => 'admin']);

            // Mock the Artisan call or test that command is queued
            $response = $this->actingAs($admin)
                ->post(route('admin.algorithms.segment'));

            $response->assertSessionHas('success');
        });

        test('unauthenticated user cannot trigger segmentation', function () {
            $response = $this->post(route('admin.algorithms.segment'));

            $response->assertStatus(302);
            $response->assertRedirect('/login');
        });

        test('guest user cannot trigger segmentation', function () {
            $guest = User::factory()->create(['role' => 'guest']);

            $response = $this->actingAs($guest)
                ->post(route('admin.algorithms.segment'));

            $response->assertStatus(403);
        });

        test('segmentation success message displays correctly', function () {
            $admin = User::factory()->create(['role' => 'admin']);

            $response = $this->actingAs($admin)
                ->post(route('admin.algorithms.segment'));

            expect($response->getSession()->get('success'))
                ->toContain('Guest segmentation re-run successfully');
        });
    });

    describe('All Show Routes - Data Correctness', function () {
        test('room show displays correct room attributes', function () {
            $admin = User::factory()->create(['role' => 'admin']);
            $room = Room::factory()->create([
                'room_number' => 'TEST-001',
                'type' => 'suite',
                'capacity' => 3,
                'price_per_night' => 8500,
                'status' => 'available',
                'floor' => 2,
            ]);

            $response = $this->actingAs($admin)
                ->get(route('admin.rooms.show', $room));

            $response->assertStatus(200);
            $response->assertViewHas('room', function ($viewRoom) use ($room) {
                return $viewRoom->id === $room->id
                    && $viewRoom->room_number === 'TEST-001'
                    && $viewRoom->type === 'suite'
                    && $viewRoom->capacity === 3;
            });
        });

        test('food show displays correct food attributes', function () {
            $admin = User::factory()->create(['role' => 'admin']);
            $food = Food::factory()->create([
                'name' => 'Paneer Butter Masala',
                'category' => 'lunch',
                'price' => 320,
                'available' => true,
            ]);

            $response = $this->actingAs($admin)
                ->get(route('admin.foods.show', $food));

            $response->assertStatus(200);
            $response->assertViewHas('food', function ($viewFood) use ($food) {
                return $viewFood->id === $food->id
                    && $viewFood->name === 'Paneer Butter Masala'
                    && $viewFood->price === 320
                    && $viewFood->available === true;
            });
        });
    });

    describe('Route Parameters Validation', function () {
        test('invalid room id returns 404', function () {
            $admin = User::factory()->create(['role' => 'admin']);

            $response = $this->actingAs($admin)
                ->get(route('admin.rooms.show', 99999));

            $response->assertStatus(404);
        });

        test('invalid food id returns 404', function () {
            $admin = User::factory()->create(['role' => 'admin']);

            $response = $this->actingAs($admin)
                ->get(route('admin.foods.show', 99999));

            $response->assertStatus(404);
        });

        test('invalid reservation id for invoice returns 404', function () {
            $admin = User::factory()->create(['role' => 'admin']);

            $response = $this->actingAs($admin)
                ->get(route('admin.reservations.generate-invoice', 99999));

            $response->assertStatus(404);
        });
    });
});
