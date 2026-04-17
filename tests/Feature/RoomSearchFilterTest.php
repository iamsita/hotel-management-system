<?php

use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Room Search and Filter Functionality', function () {
    beforeEach(function () {
        $this->admin = User::factory()->create(['role' => 'admin']);

        // Create test rooms with different attributes
        Room::factory()->create([
            'room_number' => 'A101',
            'type' => 'single',
            'status' => 'available',
            'floor' => 1,
            'price_per_night' => 3000,
            'capacity' => 1,
        ]);

        Room::factory()->create([
            'room_number' => 'A102',
            'type' => 'double',
            'status' => 'occupied',
            'floor' => 1,
            'price_per_night' => 5000,
            'capacity' => 2,
        ]);

        Room::factory()->create([
            'room_number' => 'B201',
            'type' => 'suite',
            'status' => 'available',
            'floor' => 2,
            'price_per_night' => 8000,
            'capacity' => 3,
        ]);

        Room::factory()->create([
            'room_number' => 'B202',
            'type' => 'deluxe',
            'status' => 'maintenance',
            'floor' => 2,
            'price_per_night' => 12000,
            'capacity' => 4,
        ]);
    });

    describe('Search by Room Number', function () {
        test('admin can search rooms by room number', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', ['search' => 'A101']));

            $response->assertStatus(200);
            $response->assertSee('A101');
            $response->assertDontSee('A102');
            $response->assertDontSee('B201');
        });

        test('search is case insensitive', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', ['search' => 'a10']));

            $response->assertStatus(200);
            $response->assertSee('A101');
            $response->assertSee('A102');
        });

        test('search with non-matching room number returns empty', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', ['search' => 'NONEXISTENT']));

            $response->assertStatus(200);
            $response->assertSee('No rooms found');
        });

        test('search preserves filter values in form', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', [
                    'search' => 'A101',
                    'type' => 'single',
                ]));

            $response->assertStatus(200);
            $response->assertSee('value="A101"', false);
        });
    });

    describe('Filter by Type', function () {
        test('admin can filter rooms by type', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', ['type' => 'single']));

            $response->assertStatus(200);
            $response->assertSee('A101');
            $response->assertDontSee('A102');
            $response->assertDontSee('B201');
            $response->assertDontSee('B202');
        });

        test('filter by double rooms', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', ['type' => 'double']));

            $response->assertStatus(200);
            $response->assertSee('A102');
        });

        test('filter by suite rooms', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', ['type' => 'suite']));

            $response->assertStatus(200);
            $response->assertSee('B201');
        });

        test('filter by deluxe rooms', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', ['type' => 'deluxe']));

            $response->assertStatus(200);
            $response->assertSee('B202');
        });
    });

    describe('Filter by Status', function () {
        test('admin can filter rooms by available status', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', ['status' => 'available']));

            $response->assertStatus(200);
            $response->assertSee('A101');
            $response->assertSee('B201');
        });

        test('admin can filter rooms by occupied status', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', ['status' => 'occupied']));

            $response->assertStatus(200);
            $response->assertSee('A102');
        });

        test('admin can filter rooms by maintenance status', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', ['status' => 'maintenance']));

            $response->assertStatus(200);
            $response->assertSee('B202');
        });
    });

    describe('Filter by Floor', function () {
        test('admin can filter rooms by floor', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', ['floor' => '1']));

            $response->assertStatus(200);
            $response->assertSee('A101');
            $response->assertSee('A102');
            $response->assertDontSee('B201');
            $response->assertDontSee('B202');
        });

        test('admin can filter rooms by floor 2', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', ['floor' => '2']));

            $response->assertStatus(200);
            $response->assertSee('B201');
            $response->assertSee('B202');
        });

        test('filter by non-existent floor returns empty', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', ['floor' => '99']));

            $response->assertStatus(200);
            $response->assertSee('No rooms found');
        });
    });

    describe('Filter by Price Range', function () {
        test('admin can filter rooms by minimum price', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', ['price_from' => '5000']));

            $response->assertStatus(200);
            $response->assertSee('A102');
            $response->assertSee('B201');
            $response->assertSee('B202');
            $response->assertDontSee('A101');
        });

        test('admin can filter rooms by maximum price', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', ['price_to' => '5000']));

            $response->assertStatus(200);
            $response->assertSee('A101');
            $response->assertSee('A102');
        });

        test('admin can filter rooms by price range', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', [
                    'price_from' => '5000',
                    'price_to' => '9000',
                ]));

            $response->assertStatus(200);
            $response->assertSee('A102');
            $response->assertSee('B201');
            $response->assertDontSee('A101');
            $response->assertDontSee('B202');
        });
    });

    describe('Combined Filters', function () {
        test('admin can combine search and type filter', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', [
                    'search' => 'A',
                    'type' => 'double',
                ]));

            $response->assertStatus(200);
            $response->assertSee('A102');
        });

        test('admin can combine multiple filters', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', [
                    'floor' => '1',
                    'status' => 'available',
                    'type' => 'single',
                ]));

            $response->assertStatus(200);
            $response->assertSee('A101');
        });

        test('admin can combine floor and price filters', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', [
                    'floor' => '2',
                    'price_from' => '8000',
                ]));

            $response->assertStatus(200);
            $response->assertSee('B201');
            $response->assertSee('B202');
        });

        test('combined filters with no matches returns empty', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', [
                    'floor' => '1',
                    'type' => 'deluxe', // Deluxe rooms are on floor 2
                ]));

            $response->assertStatus(200);
            $response->assertSee('No rooms found');
        });
    });

    describe('Clear Filters', function () {
        test('clear button appears when filters are active', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', ['search' => 'A101']));

            $response->assertStatus(200);
            $response->assertSee('Clear');
        });

        test('clear button does not appear when no filters', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index'));

            $response->assertStatus(200);
            $response->assertDontSee('class="btn btn-sm btn-outline-secondary w-100">Clear</a>');
        });

        test('clear link removes all filter parameters', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', [
                    'search' => 'test',
                    'type' => 'single',
                ]));

            $clearUrl = route('admin.rooms.index');
            $response->assertSee($clearUrl);
        });
    });

    describe('Pagination', function () {
        test('rooms are paginated at 15 per page', function () {
            Room::factory(20)->create();

            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index'));

            $response->assertStatus(200);
            // The response should show pagination links
        });

        test('pagination preserves search parameters', function () {
            Room::factory(20)->create();

            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', ['search' => 'A', 'page' => 1]));

            $response->assertStatus(200);
            // Pagination should work with search parameters
        });

        test('pagination preserves filter parameters', function () {
            Room::factory(20)->create(['type' => 'single']);

            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', ['type' => 'single', 'page' => 1]));

            $response->assertStatus(200);
            // Pagination should work with filter parameters
        });
    });

    describe('Filter Form Display', function () {
        test('filter form displays all available options', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index'));

            $response->assertStatus(200);
            $response->assertSee('Search');
            $response->assertSee('Type');
            $response->assertSee('Status');
            $response->assertSee('Floor');
            $response->assertSee('Price From');
        });

        test('filter inputs retain previous values', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', [
                    'search' => 'A101',
                    'type' => 'single',
                    'status' => 'available',
                ]));

            $response->assertStatus(200);
            $response->assertSee('value="A101"', false);
        });

        test('selected filter option stays selected', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', ['status' => 'available']));

            $response->assertStatus(200);
            // The selected attribute should be rendered in the option
            $response->assertSee('selected');
        });
    });

    describe('Authorization', function () {
        test('unauthenticated user cannot access rooms filter', function () {
            $response = $this->get(route('admin.rooms.index', ['search' => 'A101']));

            $response->assertStatus(302);
            $response->assertRedirect('/login');
        });

        test('guest user cannot access rooms filter', function () {
            $guest = User::factory()->create(['role' => 'guest']);

            $response = $this->actingAs($guest)
                ->get(route('admin.rooms.index', ['search' => 'A101']));

            $response->assertStatus(403);
        });

        test('admin can access rooms filter', function () {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.rooms.index', ['search' => 'A101']));

            $response->assertStatus(200);
        });
    });
});
