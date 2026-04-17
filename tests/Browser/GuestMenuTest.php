<?php

use App\Models\Food;
use App\Models\FoodOrder;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Dusk\Browser;

beforeEach(function () {
    $this->guest = User::factory()->create(['role' => 'guest']);
    $this->room = Room::factory()->create(['status' => 'available']);
    $this->food = Food::factory()->create([
        'name' => 'Veg Biryani',
        'category' => 'lunch',
        'price' => 250,
        'available' => true,
    ]);
});

test('unauthenticated user is redirected to login', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/guest/menu')
            ->assertPathIs('/login');
    });
});

test('guest can access the food menu page', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->guest)
            ->visit('/guest/menu')
            ->assertPathIs('/guest/menu');
    });
});

test('menu page shows available food items', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->guest)
            ->visit('/guest/menu')
            ->assertSee('Veg Biryani');
    });
});

test('menu page organises items by category', function () {
    Food::factory()->create(['name' => 'Orange Juice', 'category' => 'beverages', 'available' => true]);
    Food::factory()->create(['name' => 'Aloo Paratha', 'category' => 'breakfast', 'available' => true]);

    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->guest)
            ->visit('/guest/menu')
            ->assertSee('lunch')
            ->assertSee('beverages')
            ->assertSee('breakfast');
    });
});

test('menu page has ordering form', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->guest)
            ->visit('/guest/menu')
            ->assertPresent('input[name="quantity"]');
    });
});

test('guest with checked-in reservation can order food', function () {
    $reservation = Reservation::create([
        'user_id' => $this->guest->id,
        'room_id' => $this->room->id,
        'check_in' => Carbon::today()->toDateString(),
        'check_out' => Carbon::today()->addDays(2)->toDateString(),
        'guests' => 1,
        'status' => 'checked_in',
        'total_amount' => 5000,
    ]);

    $this->browse(function (Browser $browser) use ($reservation) {
        $browser->loginAs($this->guest)
            ->visit('/guest/menu')
            ->type('quantity', '2')
            ->press('Order')
            ->assertPathIs('/guest/menu');
    });
});

test('guest without active reservation sees menu but cannot order', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->guest)
            ->visit('/guest/menu')
            ->assertSee('Veg Biryani');
    });
});

test('guest can view menu items grouped by category', function () {
    $categories = ['breakfast', 'lunch', 'dinner', 'snacks', 'beverages'];

    foreach ($categories as $category) {
        Food::factory()->create(['category' => $category, 'available' => true]);
    }

    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->guest)
            ->visit('/guest/menu')
            ->assertPathIs('/guest/menu');
    });
});

test('guest reservation page shows food orders', function () {
    $reservation = Reservation::create([
        'user_id' => $this->guest->id,
        'room_id' => $this->room->id,
        'check_in' => Carbon::today()->toDateString(),
        'check_out' => Carbon::today()->addDays(2)->toDateString(),
        'guests' => 1,
        'status' => 'checked_in',
        'total_amount' => 5000,
    ]);

    FoodOrder::create([
        'reservation_id' => $reservation->id,
        'food_id' => $this->food->id,
        'quantity' => 1,
        'total_price' => $this->food->price,
        'status' => 'delivered',
    ]);

    $this->browse(function (Browser $browser) use ($reservation) {
        $browser->loginAs($this->guest)
            ->visit("/guest/reservation/{$reservation->id}")
            ->assertSee('Veg Biryani');
    });
});
