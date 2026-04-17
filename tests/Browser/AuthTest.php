<?php

use App\Models\User;
use Laravel\Dusk\Browser;

test('home page loads', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->assertSee('Hotel Management System');
    });
});

test('login page displays form', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/login')
            ->assertSee('Login')
            ->assertPresent('input[name="email"]')
            ->assertPresent('input[name="password"]');
    });
});

test('login with invalid credentials shows error', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/login')
            ->type('email', 'nobody@example.com')
            ->type('password', 'wrongpassword')
            ->press('Login')
            ->assertSee('credentials');
    });
});

test('login with missing fields shows validation', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/login');
        // Remove HTML5 required attributes so server-side validation fires
        $browser->script([
            'document.querySelector("input[name=email]").removeAttribute("required")',
            'document.querySelector("input[name=password]").removeAttribute("required")',
        ]);
        $browser->press('Login')
            ->assertSee('required');
    });
});

test('guest user is redirected to guest dashboard after login', function () {
    $user = User::factory()->create(['role' => 'guest']);

    $this->browse(function (Browser $browser) use ($user) {
        $browser->visit('/login')
            ->type('email', $user->email)
            ->type('password', 'password')
            ->press('Login')
            ->assertPathIs('/guest/dashboard');
    });
});

test('admin user is redirected to admin dashboard after login', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->visit('/login')
            ->screenshot('debug-admin-login')
            ->assertPathIs('/login')
            ->type('email', $admin->email)
            ->type('password', 'password')
            ->press('Login')
            ->assertPathIs('/admin/dashboard');
    });
});

test('register page displays form', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/register')
            ->assertSee('Register')
            ->assertPresent('input[name="name"]')
            ->assertPresent('input[name="email"]')
            ->assertPresent('input[name="phone"]')
            ->assertPresent('input[name="password"]')
            ->assertPresent('input[name="password_confirmation"]');
    });
});

test('register with missing required fields shows validation errors', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/register');
        // Remove HTML5 required attributes so server-side validation fires
        $browser->script([
            'document.querySelectorAll("input[required]").forEach(el => el.removeAttribute("required"))',
        ]);
        $browser->press('Register')
            ->assertSee('required');
    });
});

test('register with mismatched passwords shows confirmation error', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/register')
            ->type('name', 'Test User')
            ->type('email', 'test@example.com')
            ->type('password', 'password123')
            ->type('password_confirmation', 'different456')
            ->press('Register')
            ->assertSee('confirmation');
    });
});

test('register with existing email shows unique error', function () {
    $existing = User::factory()->create();

    $this->browse(function (Browser $browser) use ($existing) {
        $browser->visit('/register')
            ->waitFor('input[name="name"]')
            ->type('name', 'Another User')
            ->type('email', $existing->email)
            ->type('password', 'password123')
            ->type('password_confirmation', 'password123')
            ->press('Register')
            ->assertSee('taken');
    });
});

test('register with valid data creates account and redirects to guest dashboard', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/register')
            ->type('name', 'New Guest User')
            ->type('email', 'newguest@example.com')
            ->type('phone', '9876543210')
            ->type('password', 'password123')
            ->type('password_confirmation', 'password123')
            ->press('Register')
            ->assertPathIs('/guest/dashboard');
    });
});

test('authenticated user is redirected away from login page', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/login')
            ->assertPathIsNot('/login');
    });
});

test('authenticated user can logout', function () {
    $user = User::factory()->create(['role' => 'guest']);

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/guest/dashboard')
            ->click('form[action*="logout"] button[type="submit"]')
            ->assertGuest();
    });
});
