<?php

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Dusk\Browser;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->guest = User::factory()->create(['role' => 'guest']);
    $this->room = Room::factory()->create(['room_number' => '101', 'status' => 'available']);
});

test('reservations index is accessible to admin', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/reservations')
            ->assertPathIs('/admin/reservations');
    });
});

test('reservations index lists existing reservations', function () {
    Reservation::create([
        'user_id' => $this->guest->id,
        'room_id' => $this->room->id,
        'check_in' => Carbon::tomorrow()->toDateString(),
        'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
        'guests' => 1,
        'status' => 'pending',
        'total_amount' => 10000,
    ]);

    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/reservations')
            ->assertSee($this->guest->name);
    });
});

test('create reservation form displays required fields', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/reservations/create')
            ->assertPresent('select[name="user_id"]')
            ->assertPresent('select[name="room_id"]')
            ->assertPresent('input[name="check_in"]')
            ->assertPresent('input[name="check_out"]')
            ->assertPresent('input[name="guests"]');
    });
});

test('admin can create a reservation', function () {
    $checkIn = Carbon::tomorrow()->format('Y-m-d');
    $checkOut = Carbon::tomorrow()->addDays(3)->format('Y-m-d');

    $this->browse(function (Browser $browser) use ($checkIn, $checkOut) {
        $browser->loginAs($this->admin)
            ->visit('/admin/reservations/create')
            ->select('user_id', (string) $this->guest->id)
            ->select('room_id', (string) $this->room->id)
            ->value('[name="check_in"]', $checkIn)
            ->value('[name="check_out"]', $checkOut)
            ->type('guests', '1')
            ->press('Create Reservation')
            ->assertPathIs('/admin/reservations');
    });
});

test('create reservation with missing fields shows validation errors', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/reservations/create')
            ->press('Create Reservation')
            ->assertSee('required');
    });
});

test('admin can filter reservations by status', function () {
    Reservation::create([
        'user_id' => $this->guest->id,
        'room_id' => $this->room->id,
        'check_in' => Carbon::tomorrow()->toDateString(),
        'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
        'guests' => 1,
        'status' => 'confirmed',
        'total_amount' => 10000,
    ]);

    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/reservations')
            ->select('status', 'confirmed')
            ->press('Filter')
            ->assertSee('confirmed')
            ->assertQueryStringHas('status', 'confirmed');
    });
});

test('admin can view a reservation', function () {
    $reservation = Reservation::create([
        'user_id' => $this->guest->id,
        'room_id' => $this->room->id,
        'check_in' => Carbon::tomorrow()->toDateString(),
        'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
        'guests' => 1,
        'status' => 'pending',
        'total_amount' => 10000,
    ]);

    $this->browse(function (Browser $browser) use ($reservation) {
        $browser->loginAs($this->admin)
            ->visit("/admin/reservations/{$reservation->id}")
            ->assertSee($this->guest->name)
            ->assertSee('101');
    });
});

test('admin can confirm a pending reservation', function () {
    $reservation = Reservation::create([
        'user_id' => $this->guest->id,
        'room_id' => $this->room->id,
        'check_in' => Carbon::tomorrow()->toDateString(),
        'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
        'guests' => 1,
        'status' => 'pending',
        'total_amount' => 10000,
    ]);

    $this->browse(function (Browser $browser) use ($reservation) {
        $browser->loginAs($this->admin)
            ->visit('/admin/reservations')
            ->press('Confirm')
            ->assertPathIs('/admin/reservations');
    });
});

test('admin can cancel a reservation', function () {
    $reservation = Reservation::create([
        'user_id' => $this->guest->id,
        'room_id' => $this->room->id,
        'check_in' => Carbon::tomorrow()->toDateString(),
        'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
        'guests' => 1,
        'status' => 'pending',
        'total_amount' => 10000,
    ]);

    $this->browse(function (Browser $browser) use ($reservation) {
        $browser->loginAs($this->admin)
            ->visit('/admin/reservations')
            ->press('Cancel')
            ->assertPathIs('/admin/reservations');
    });
});

test('admin can record a payment for a confirmed reservation', function () {
    $reservation = Reservation::create([
        'user_id' => $this->guest->id,
        'room_id' => $this->room->id,
        'check_in' => Carbon::tomorrow()->toDateString(),
        'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
        'guests' => 1,
        'status' => 'confirmed',
        'total_amount' => 10000,
    ]);

    $this->browse(function (Browser $browser) use ($reservation) {
        $browser->loginAs($this->admin)
            ->visit("/admin/reservations/{$reservation->id}")
            ->type('amount', '10000')
            ->select('method', 'cash')
            ->press('Record Payment')
            ->assertPathIs("/admin/reservations/{$reservation->id}");
    });
});

test('admin can generate invoice for a reservation', function () {
    $reservation = Reservation::create([
        'user_id' => $this->guest->id,
        'room_id' => $this->room->id,
        'check_in' => Carbon::yesterday()->toDateString(),
        'check_out' => Carbon::today()->toDateString(),
        'guests' => 1,
        'status' => 'checked_out',
        'total_amount' => 10000,
    ]);

    $this->browse(function (Browser $browser) use ($reservation) {
        $browser->loginAs($this->admin)
            ->visit("/admin/reservations/{$reservation->id}/invoice")
            ->assertPathIs("/admin/reservations/{$reservation->id}/invoice");
    });
});

test('admin can edit a reservation', function () {
    $reservation = Reservation::create([
        'user_id' => $this->guest->id,
        'room_id' => $this->room->id,
        'check_in' => Carbon::tomorrow()->toDateString(),
        'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
        'guests' => 1,
        'status' => 'pending',
        'total_amount' => 10000,
    ]);

    $newCheckIn = Carbon::now()->addDays(5)->format('Y-m-d');
    $newCheckOut = Carbon::now()->addDays(8)->format('Y-m-d');

    $this->browse(function (Browser $browser) use ($reservation, $newCheckIn, $newCheckOut) {
        $browser->loginAs($this->admin)
            ->visit("/admin/reservations/{$reservation->id}/edit")
            ->value('[name="check_in"]', $newCheckIn)
            ->value('[name="check_out"]', $newCheckOut)
            ->type('guests', '2')
            ->press('Update Reservation')
            ->assertPathIs('/admin/reservations');
    });
});

test('admin can delete a reservation', function () {
    $reservation = Reservation::create([
        'user_id' => $this->guest->id,
        'room_id' => $this->room->id,
        'check_in' => Carbon::tomorrow()->toDateString(),
        'check_out' => Carbon::tomorrow()->addDays(2)->toDateString(),
        'guests' => 1,
        'status' => 'pending',
        'total_amount' => 10000,
    ]);

    $this->browse(function (Browser $browser) use ($reservation) {
        $browser->loginAs($this->admin)
            ->visit('/admin/reservations');

        $browser->script('window.confirm = () => true');

        $browser->click("form[action*='/admin/reservations/{$reservation->id}'][method='POST'] button[type='submit']")
            ->assertPathIs('/admin/reservations');
    });
});
