<?php

use App\Models\Room;
use App\Models\User;
use Laravel\Dusk\Browser;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
});

test('rooms index is accessible to admin', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/rooms')
            ->assertPathIs('/admin/rooms');
    });
});

test('rooms index lists existing rooms', function () {
    Room::factory()->create(['room_number' => '101', 'type' => 'single']);

    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/rooms')
            ->assertSee('101');
    });
});

test('rooms index has link to create new room', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/rooms')
            ->assertPresent('a[href*="admin/rooms/create"]');
    });
});

test('create room page displays form', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/rooms/create')
            ->assertPresent('input[name="room_number"]')
            ->assertPresent('select[name="type"]')
            ->assertPresent('input[name="capacity"]')
            ->assertPresent('input[name="price_per_night"]')
            ->assertPresent('input[name="floor"]');
    });
});

test('create room with missing fields shows validation errors', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/rooms/create')
            ->press('Add Room')
            ->assertSee('required');
    });
});

test('admin can create a new room', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/rooms/create')
            ->type('room_number', '201')
            ->select('type', 'double')
            ->type('capacity', '2')
            ->type('price_per_night', '5000')
            ->type('floor', '2')
            ->press('Add Room')
            ->assertPathIs('/admin/rooms')
            ->assertSee('201');
    });
});

test('create room with duplicate room number shows error', function () {
    Room::factory()->create(['room_number' => '301']);

    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/rooms/create')
            ->type('room_number', '301')
            ->select('type', 'single')
            ->type('capacity', '1')
            ->type('price_per_night', '3000')
            ->type('floor', '3')
            ->press('Add Room')
            ->assertSee('taken');
    });
});

test('admin can view a room', function () {
    $room = Room::factory()->create(['room_number' => '401']);

    $this->browse(function (Browser $browser) use ($room) {
        $browser->loginAs($this->admin)
            ->visit("/admin/rooms/{$room->id}")
            ->assertSee('401');
    });
});

test('admin can access edit room form', function () {
    $room = Room::factory()->create(['room_number' => '501', 'type' => 'suite']);

    $this->browse(function (Browser $browser) use ($room) {
        $browser->loginAs($this->admin)
            ->visit("/admin/rooms/{$room->id}/edit")
            ->assertInputValue('room_number', '501')
            ->assertPresent('select[name="type"]')
            ->assertPresent('select[name="status"]');
    });
});

test('admin can update a room', function () {
    $room = Room::factory()->create(['room_number' => '601', 'floor' => 6]);

    $this->browse(function (Browser $browser) use ($room) {
        $browser->loginAs($this->admin)
            ->visit("/admin/rooms/{$room->id}/edit")
            ->clear('price_per_night')
            ->type('price_per_night', '9999')
            ->select('status', 'maintenance')
            ->press('Update Room')
            ->assertPathIs('/admin/rooms');
    });
});

test('admin can delete a room', function () {
    $room = Room::factory()->create(['room_number' => '701']);

    $this->browse(function (Browser $browser) use ($room) {
        $browser->loginAs($this->admin)
            ->visit('/admin/rooms');

        $browser->script('window.confirm = () => true');

        $browser->click("form[action*='/admin/rooms/{$room->id}'] button[type='submit']")
            ->assertPathIs('/admin/rooms')
            ->assertDontSee('701');
    });
});

test('guest cannot access admin rooms', function () {
    $guest = User::factory()->create(['role' => 'guest']);

    $this->browse(function (Browser $browser) use ($guest) {
        $browser->loginAs($guest)
            ->visit('/admin/rooms')
            ->assertSee('403');
    });
});
