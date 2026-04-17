<?php

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Dusk\Browser;

beforeEach(function () {
    $this->guest = User::factory()->create(['role' => 'guest']);
    $this->room = Room::factory()->create([
        'room_number' => '101',
        'type' => 'single',
        'capacity' => 1,
        'price_per_night' => 3000,
        'status' => 'available',
        'floor' => 1,
    ]);
});

test('unauthenticated user is redirected to login', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/guest/rooms')
            ->assertPathIs('/login');
    });
});

test('guest can browse available rooms', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->guest)
            ->visit('/guest/rooms')
            ->assertPathIs('/guest/rooms')
            ->assertSee('101');
    });
});

test('rooms page shows room details', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->guest)
            ->visit('/guest/rooms')
            ->assertSee('single')
            ->assertSee('3000');
    });
});

test('guest can filter rooms by type', function () {
    Room::factory()->create(['type' => 'suite', 'room_number' => '301', 'status' => 'available']);

    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->guest)
            ->visit('/guest/rooms')
            ->select('type', 'suite')
            ->press('Search')
            ->assertSee('301')
            ->assertDontSee('101');
    });
});

test('guest can filter rooms by price range', function () {
    Room::factory()->create(['room_number' => '999', 'price_per_night' => 20000, 'status' => 'available']);

    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->guest)
            ->visit('/guest/rooms')
            ->type('max_price', '5000')
            ->press('Search')
            ->assertSee('101')
            ->assertDontSee('999');
    });
});

test('guest can filter rooms by capacity', function () {
    Room::factory()->create(['room_number' => '401', 'capacity' => 4, 'type' => 'deluxe', 'status' => 'available']);

    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->guest)
            ->visit('/guest/rooms')
            ->type('capacity', '4')
            ->press('Search')
            ->assertSee('401');
    });
});

test('rooms page has booking form for each room', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->guest)
            ->visit('/guest/rooms')
            ->assertPresent('input[name="check_in"]')
            ->assertPresent('input[name="check_out"]')
            ->assertPresent('input[name="guests"]');
    });
});

test('guest can book an available room', function () {
    $checkIn = Carbon::tomorrow()->format('Y-m-d');
    $checkOut = Carbon::tomorrow()->addDays(3)->format('Y-m-d');

    $this->browse(function (Browser $browser) use ($checkIn, $checkOut) {
        $browser->loginAs($this->guest)
            ->visit('/guest/rooms')
            ->value('[name="check_in"]', $checkIn)
            ->value('[name="check_out"]', $checkOut)
            ->type('guests', '1')
            ->press('Book Now')
            ->assertPathIs('/guest/dashboard');
    });
});

test('booking with past check-in date shows validation error', function () {
    $pastDate = Carbon::yesterday()->format('Y-m-d');
    $futureDate = Carbon::tomorrow()->format('Y-m-d');

    $this->browse(function (Browser $browser) use ($pastDate, $futureDate) {
        $browser->loginAs($this->guest)
            ->visit('/guest/rooms')
            ->value('[name="check_in"]', $pastDate)
            ->value('[name="check_out"]', $futureDate)
            ->type('guests', '1')
            ->press('Book Now')
            ->assertPathIsNot('/guest/dashboard');
    });
});

test('booking with checkout before checkin shows validation error', function () {
    $checkIn = Carbon::tomorrow()->addDays(3)->format('Y-m-d');
    $checkOut = Carbon::tomorrow()->format('Y-m-d');

    $this->browse(function (Browser $browser) use ($checkIn, $checkOut) {
        $browser->loginAs($this->guest)
            ->visit('/guest/rooms')
            ->value('[name="check_in"]', $checkIn)
            ->value('[name="check_out"]', $checkOut)
            ->type('guests', '1')
            ->press('Book Now')
            ->assertPathIsNot('/guest/dashboard');
    });
});

test('guest with active reservation cannot book another room', function () {
    $anotherRoom = Room::factory()->create(['status' => 'available']);

    Reservation::create([
        'user_id' => $this->guest->id,
        'room_id' => $this->room->id,
        'check_in' => Carbon::tomorrow()->toDateString(),
        'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
        'guests' => 1,
        'status' => 'confirmed',
        'total_amount' => 6000,
    ]);

    $checkIn = Carbon::now()->addDays(5)->format('Y-m-d');
    $checkOut = Carbon::now()->addDays(7)->format('Y-m-d');

    $this->browse(function (Browser $browser) use ($anotherRoom, $checkIn, $checkOut) {
        $browser->loginAs($this->guest)
            ->visit('/guest/rooms')
            ->value("[name='room_id'][value='{$anotherRoom->id}'] ~ [name='check_in'], input[name='check_in']", $checkIn)
            ->value('input[name="check_out"]', $checkOut)
            ->type('guests', '1')
            ->press('Book Now')
            ->assertPathIsNot('/guest/dashboard');
    });
});

test('guest can view reservation details after booking', function () {
    $checkIn = Carbon::tomorrow()->format('Y-m-d');
    $checkOut = Carbon::tomorrow()->addDays(2)->format('Y-m-d');

    $reservation = Reservation::create([
        'user_id' => $this->guest->id,
        'room_id' => $this->room->id,
        'check_in' => $checkIn,
        'check_out' => $checkOut,
        'guests' => 1,
        'status' => 'pending',
        'total_amount' => 6000,
    ]);

    $this->browse(function (Browser $browser) use ($reservation) {
        $browser->loginAs($this->guest)
            ->visit("/guest/reservation/{$reservation->id}")
            ->assertSee('101');
    });
});
