<?php

use App\Models\User;
use Laravel\Dusk\Browser;

test('unauthenticated user is redirected to login', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/admin/dashboard')
            ->assertPathIs('/login');
    });
});

test('guest user cannot access admin dashboard', function () {
    $guest = User::factory()->create(['role' => 'guest']);

    $this->browse(function (Browser $browser) use ($guest) {
        $browser->loginAs($guest)
            ->visit('/admin/dashboard')
            ->assertSee('403');
    });
});

test('admin can access dashboard', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/dashboard')
            ->assertPathIs('/admin/dashboard')
            ->assertAuthenticated();
    });
});

test('admin dashboard shows navigation links', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/dashboard')
            ->assertPresent('a[href*="admin/rooms"]')
            ->assertPresent('a[href*="admin/reservations"]');
    });
});
