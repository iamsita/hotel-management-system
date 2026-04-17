<?php

use App\Models\Food;
use App\Models\FoodOrder;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Dusk\Browser;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->guest = User::factory()->create(['role' => 'guest']);
    $this->room = Room::factory()->create();
    $this->food = Food::factory()->create(['name' => 'Test Dish', 'available' => true]);

    $this->reservation = Reservation::create([
        'user_id' => $this->guest->id,
        'room_id' => $this->room->id,
        'check_in' => Carbon::today()->toDateString(),
        'check_out' => Carbon::today()->addDays(2)->toDateString(),
        'guests' => 1,
        'status' => 'checked_in',
        'total_amount' => 10000,
    ]);

    $this->order = FoodOrder::create([
        'reservation_id' => $this->reservation->id,
        'food_id' => $this->food->id,
        'quantity' => 2,
        'total_price' => $this->food->price * 2,
        'status' => 'pending',
    ]);
});

test('food orders index is accessible to admin', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/food-orders')
            ->assertPathIs('/admin/food-orders');
    });
});

test('food orders index lists existing orders', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/food-orders')
            ->assertSee('Test Dish');
    });
});

test('admin can update order status to preparing', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/food-orders')
            ->press('Prepare')
            ->assertPathIs('/admin/food-orders');
    });
});

test('admin can update order status to delivered', function () {
    $this->order->update(['status' => 'preparing']);

    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/food-orders')
            ->press('Deliver')
            ->assertPathIs('/admin/food-orders');
    });
});

test('guest cannot access admin food orders page', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->guest)
            ->visit('/admin/food-orders')
            ->assertSee('403');
    });
});
