<?php

use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Dusk\Browser;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
});

test('payments index is accessible to admin', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/payments')
            ->assertPathIs('/admin/payments');
    });
});

test('payments index lists existing payment records', function () {
    $guest = User::factory()->create(['role' => 'guest']);
    $room = Room::factory()->create();

    $reservation = Reservation::create([
        'user_id' => $guest->id,
        'room_id' => $room->id,
        'check_in' => Carbon::yesterday()->toDateString(),
        'check_out' => Carbon::today()->toDateString(),
        'guests' => 1,
        'status' => 'checked_out',
        'total_amount' => 5000,
    ]);

    Payment::create([
        'reservation_id' => $reservation->id,
        'amount' => 5000,
        'method' => 'cash',
        'status' => 'completed',
    ]);

    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/payments')
            ->assertSee('5000')
            ->assertSee('cash');
    });
});

test('payments page shows empty state when no payments', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/payments')
            ->assertPathIs('/admin/payments');
    });
});

test('guest cannot access admin payments page', function () {
    $guest = User::factory()->create(['role' => 'guest']);

    $this->browse(function (Browser $browser) use ($guest) {
        $browser->loginAs($guest)
            ->visit('/admin/payments')
            ->assertSee('403');
    });
});
