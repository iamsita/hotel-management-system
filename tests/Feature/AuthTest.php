<?php

use App\Models\User;

describe('Auth Routes', function () {
    describe('Login Routes', function () {
        test('user can view login form', function () {
            $response = $this->get('/login');

            $response->assertStatus(200);
            $response->assertViewIs('auth.login');
        });

        test('unauthenticated user cannot access protected routes', function () {
            $response = $this->get('/profile');

            $response->assertStatus(302);
            $response->assertRedirect('/login');
        });

        test('user can login with valid credentials', function () {
            $user = User::factory()->create([
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'role' => 'guest',
            ]);

            $response = $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'password',
            ]);

            $response->assertStatus(302);
            $this->assertAuthenticatedAs($user);
        });

        test('admin user is redirected to admin dashboard after login', function () {
            $admin = User::factory()->create([
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]);

            $response = $this->post('/login', [
                'email' => 'admin@example.com',
                'password' => 'password',
            ]);

            $response->assertRedirect(route('admin.dashboard'));
        });

        test('guest user is redirected to guest dashboard after login', function () {
            $guest = User::factory()->create([
                'email' => 'guest@example.com',
                'password' => bcrypt('password'),
                'role' => 'guest',
            ]);

            $response = $this->post('/login', [
                'email' => 'guest@example.com',
                'password' => 'password',
            ]);

            $response->assertRedirect(route('guest.dashboard'));
        });

        test('user cannot login with invalid credentials', function () {
            User::factory()->create([
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);

            $response = $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'wrong-password',
            ]);

            $response->assertStatus(302);
            $response->assertSessionHasErrors('email');
            $this->assertGuest();
        });

        test('login requires valid email', function () {
            $response = $this->post('/login', [
                'email' => 'invalid-email',
                'password' => 'password',
            ]);

            $response->assertSessionHasErrors('email');
        });

        test('login requires password', function () {
            $response = $this->post('/login', [
                'email' => 'test@example.com',
                'password' => '',
            ]);

            $response->assertSessionHasErrors('password');
        });
    });

    describe('Register Routes', function () {
        test('user can view register form', function () {
            $response = $this->get('/register');

            $response->assertStatus(200);
            $response->assertViewIs('auth.register');
        });

        test('user can register with valid data', function () {
            $response = $this->post('/register', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'phone' => '1234567890',
            ]);

            $response->assertRedirect(route('guest.dashboard'));
            $this->assertDatabaseHas('users', [
                'email' => 'john@example.com',
                'name' => 'John Doe',
                'role' => 'guest',
            ]);
        });

        test('user cannot register with existing email', function () {
            User::factory()->create(['email' => 'existing@example.com']);

            $response = $this->post('/register', [
                'name' => 'John Doe',
                'email' => 'existing@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response->assertSessionHasErrors('email');
        });

        test('register requires password confirmation', function () {
            $response = $this->post('/register', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'password',
                'password_confirmation' => 'different-password',
            ]);

            $response->assertSessionHasErrors('password');
        });

        test('register requires minimum 6 character password', function () {
            $response = $this->post('/register', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => '12345',
                'password_confirmation' => '12345',
            ]);

            $response->assertSessionHasErrors('password');
        });

        test('register requires email', function () {
            $response = $this->post('/register', [
                'name' => 'John Doe',
                'email' => '',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response->assertSessionHasErrors('email');
        });
    });

    describe('Logout Routes', function () {
        test('authenticated user can logout', function () {
            $user = User::factory()->create();
            $this->actingAs($user);

            $response = $this->post('/logout');

            $response->assertStatus(302);
            $response->assertRedirect('/');
            $this->assertGuest();
        });

        test('session is invalidated after logout', function () {
            $user = User::factory()->create();
            $this->actingAs($user);

            $this->post('/logout');

            $this->assertGuest();
        });
    });
});
