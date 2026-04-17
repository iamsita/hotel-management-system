<?php

use App\Models\Food;
use App\Models\User;
use Laravel\Dusk\Browser;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
});

test('foods index is accessible to admin', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/foods')
            ->assertPathIs('/admin/foods');
    });
});

test('foods index lists existing food items', function () {
    Food::factory()->create(['name' => 'Butter Chicken', 'category' => 'dinner']);

    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/foods')
            ->assertSee('Butter Chicken');
    });
});

test('foods index has link to create new food', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/foods')
            ->assertPresent('a[href*="admin/foods/create"]');
    });
});

test('create food page displays form', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/foods/create')
            ->assertPresent('input[name="name"]')
            ->assertPresent('select[name="category"]')
            ->assertPresent('input[name="price"]')
            ->assertPresent('input[name="available"]');
    });
});

test('create food with missing fields shows validation errors', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/foods/create')
            ->press('Add Item')
            ->assertSee('required');
    });
});

test('admin can create a new food item', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/foods/create')
            ->type('name', 'Paneer Tikka')
            ->select('category', 'dinner')
            ->type('price', '350')
            ->check('available')
            ->press('Add Item')
            ->assertPathIs('/admin/foods')
            ->assertSee('Paneer Tikka');
    });
});

test('admin can create an unavailable food item', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/foods/create')
            ->type('name', 'Seasonal Special')
            ->select('category', 'lunch')
            ->type('price', '500')
            ->uncheck('available')
            ->press('Add Item')
            ->assertPathIs('/admin/foods');
    });
});

test('admin can view a food item', function () {
    $food = Food::factory()->create(['name' => 'Masala Chai']);

    $this->browse(function (Browser $browser) use ($food) {
        $browser->loginAs($this->admin)
            ->visit("/admin/foods/{$food->id}")
            ->assertSee('Masala Chai');
    });
});

test('admin can edit a food item', function () {
    $food = Food::factory()->create(['name' => 'Old Name', 'category' => 'snacks', 'price' => 100]);

    $this->browse(function (Browser $browser) use ($food) {
        $browser->loginAs($this->admin)
            ->visit("/admin/foods/{$food->id}/edit")
            ->clear('name')
            ->type('name', 'Updated Snack')
            ->clear('price')
            ->type('price', '150')
            ->press('Update Item')
            ->assertPathIs('/admin/foods')
            ->assertSee('Updated Snack');
    });
});

test('admin can delete a food item', function () {
    $food = Food::factory()->create(['name' => 'Delete Me']);

    $this->browse(function (Browser $browser) use ($food) {
        $browser->loginAs($this->admin)
            ->visit('/admin/foods');

        $browser->script('window.confirm = () => true');

        $browser->click("form[action*='/admin/foods/{$food->id}'] button[type='submit']")
            ->assertPathIs('/admin/foods')
            ->assertDontSee('Delete Me');
    });
});

test('guest cannot access admin foods page', function () {
    $guest = User::factory()->create(['role' => 'guest']);

    $this->browse(function (Browser $browser) use ($guest) {
        $browser->loginAs($guest)
            ->visit('/admin/foods')
            ->assertSee('403');
    });
});
