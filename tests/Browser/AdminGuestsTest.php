<?php

use App\Models\User;
use Laravel\Dusk\Browser;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
});

test('guests index is accessible to admin', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/guests')
            ->assertPathIs('/admin/guests');
    });
});

test('guests index lists existing guest users', function () {
    User::factory()->create(['role' => 'guest', 'name' => 'Listed Guest', 'email' => 'listed@example.com']);

    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/guests')
            ->assertSee('Listed Guest');
    });
});

test('guests index has link to create new guest', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/guests')
            ->assertPresent('a[href*="admin/guests/create"]');
    });
});

test('create guest page displays form', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/guests/create')
            ->assertPresent('input[name="name"]')
            ->assertPresent('input[name="email"]')
            ->assertPresent('input[name="phone"]')
            ->assertPresent('input[name="password"]');
    });
});

test('create guest with missing fields shows validation errors', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/guests/create')
            ->press('Create Guest')
            ->assertSee('required');
    });
});

test('admin can create a new guest', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/guests/create')
            ->type('name', 'Guest From Admin')
            ->type('email', 'guestadmin@example.com')
            ->type('phone', '9876543210')
            ->type('password', 'password123')
            ->press('Create Guest')
            ->assertPathIs('/admin/guests')
            ->assertSee('Guest From Admin');
    });
});

test('create guest with duplicate email shows error', function () {
    User::factory()->create(['email' => 'duplicate@example.com']);

    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/guests/create')
            ->type('name', 'Duplicate User')
            ->type('email', 'duplicate@example.com')
            ->type('password', 'password123')
            ->press('Create Guest')
            ->assertSee('taken');
    });
});

test('guest user cannot access admin guests page', function () {
    $guest = User::factory()->create(['role' => 'guest']);

    $this->browse(function (Browser $browser) use ($guest) {
        $browser->loginAs($guest)
            ->visit('/admin/guests')
            ->assertSee('403');
    });
});
