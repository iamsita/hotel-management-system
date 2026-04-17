<?php

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Dusk\Browser;

beforeEach(function () {
    $this->guest = User::factory()->create(['role' => 'guest']);
});

test('unauthenticated user is redirected to login', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/guest/dashboard')
            ->assertPathIs('/login');
    });
});

test('guest can access their dashboard', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->guest)
            ->visit('/guest/dashboard')
            ->assertPathIs('/guest/dashboard')
            ->assertAuthenticated();
    });
});

test('guest dashboard has link to browse rooms', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->guest)
            ->visit('/guest/dashboard')
            ->assertPresent('a[href*="guest/rooms"]');
    });
});

test('guest dashboard shows existing reservations', function () {
    $room = Room::factory()->create(['room_number' => '202']);

    Reservation::create([
        'user_id' => $this->guest->id,
        'room_id' => $room->id,
        'check_in' => Carbon::tomorrow()->toDateString(),
        'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
        'guests' => 1,
        'status' => 'confirmed',
        'total_amount' => 10000,
    ]);

    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->guest)
            ->visit('/guest/dashboard')
            ->assertSee('202');
    });
});

test('guest dashboard shows link to view reservation details', function () {
    $room = Room::factory()->create();

    $reservation = Reservation::create([
        'user_id' => $this->guest->id,
        'room_id' => $room->id,
        'check_in' => Carbon::tomorrow()->toDateString(),
        'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
        'guests' => 1,
        'status' => 'confirmed',
        'total_amount' => 10000,
    ]);

    $this->browse(function (Browser $browser) use ($reservation) {
        $browser->loginAs($this->guest)
            ->visit('/guest/dashboard')
            ->assertPresent("a[href*='guest/reservation/{$reservation->id}']");
    });
});

test('guest dashboard shows empty state when no reservations', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->guest)
            ->visit('/guest/dashboard')
            ->assertPathIs('/guest/dashboard');
    });
});
