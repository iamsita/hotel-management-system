<?php

use App\Models\User;
use Laravel\Dusk\Browser;

test('unauthenticated user is redirected to login when accessing profile', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/profile')
            ->assertPathIs('/login');
    });
});

test('authenticated user can view their profile', function () {
    $user = User::factory()->create(['name' => 'Jane Doe', 'email' => 'jane@example.com']);

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/profile')
            ->assertSee('Jane Doe')
            ->assertSee('jane@example.com');
    });
});

test('profile page has link to edit profile', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/profile')
            ->assertPresent('a[href*="profile/edit"]');
    });
});

test('profile edit page displays form with current values', function () {
    $user = User::factory()->create(['name' => 'John Smith', 'email' => 'john@example.com']);

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/profile/edit')
            ->assertInputValue('name', 'John Smith')
            ->assertInputValue('email', 'john@example.com')
            ->assertPresent('input[name="phone"]')
            ->assertPresent('input[name="current_password"]')
            ->assertPresent('input[name="password"]')
            ->assertPresent('input[name="password_confirmation"]');
    });
});

test('user can update name and email', function () {
    $user = User::factory()->create(['name' => 'Old Name', 'email' => 'old@example.com']);

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/profile/edit')
            ->clear('name')
            ->type('name', 'New Name')
            ->clear('email')
            ->type('email', 'new@example.com')
            ->press('Save Changes')
            ->assertPathIs('/profile')
            ->assertSee('New Name');
    });
});

test('user can update phone number', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/profile/edit')
            ->clear('phone')
            ->type('phone', '9999988888')
            ->press('Save Changes')
            ->assertPathIs('/profile');
    });
});

test('profile update with duplicate email shows error', function () {
    $other = User::factory()->create(['email' => 'taken@example.com']);
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user, $other) {
        $browser->loginAs($user)
            ->visit('/profile/edit')
            ->clear('email')
            ->type('email', 'taken@example.com')
            ->press('Save Changes')
            ->assertSee('taken');
    });
});

test('user can change password with correct current password', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/profile/edit')
            ->type('current_password', 'password')
            ->type('password', 'newpassword123')
            ->type('password_confirmation', 'newpassword123')
            ->press('Save Changes')
            ->assertPathIs('/profile');
    });
});

test('password change with wrong current password shows error', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/profile/edit')
            ->type('current_password', 'wrongcurrentpassword')
            ->type('password', 'newpassword123')
            ->type('password_confirmation', 'newpassword123')
            ->press('Save Changes')
            ->assertSee('Current password is incorrect');
    });
});
