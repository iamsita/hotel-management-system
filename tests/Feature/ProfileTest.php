<?php

use App\Models\User;

describe('Profile Routes', function () {
    describe('Show Profile', function () {
        test('authenticated user can view their profile', function () {
            $user = User::factory()->create(['name' => 'John Doe']);
            $this->actingAs($user);

            $response = $this->get('/profile');

            $response->assertStatus(200);
            $response->assertViewIs('profile.show');
            $response->assertViewHas('user', $user);
        });

        test('unauthenticated user cannot view profile', function () {
            $response = $this->get('/profile');

            $response->assertStatus(302);
            $response->assertRedirect('/login');
        });
    });

    describe('Edit Profile', function () {
        test('authenticated user can view edit profile form', function () {
            $user = User::factory()->create();
            $this->actingAs($user);

            $response = $this->get('/profile/edit');

            $response->assertStatus(200);
            $response->assertViewIs('profile.edit');
            $response->assertViewHas('user');
        });

        test('user can update their profile', function () {
            $user = User::factory()->create();
            $this->actingAs($user);

            $response = $this->put('/profile', [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'phone' => '9876543210',
            ]);

            $response->assertRedirect(route('profile.show'));
            $response->assertSessionHas('success', 'Profile updated successfully!');
            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'phone' => '9876543210',
            ]);
        });

        test('user can update password with correct current password', function () {
            $user = User::factory()->create([
                'password' => bcrypt('old-password'),
            ]);
            $this->actingAs($user);

            $response = $this->put('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'current_password' => 'old-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

            $response->assertRedirect(route('profile.show'));
            $response->assertSessionHas('success');
            $this->assertTrue(\Hash::check('new-password', User::find($user->id)->password));
        });

        test('user cannot update password with incorrect current password', function () {
            $user = User::factory()->create([
                'password' => bcrypt('old-password'),
            ]);
            $this->actingAs($user);

            $response = $this->put('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

            $response->assertSessionHasErrors('current_password');
        });

        test('email must be unique during update', function () {
            $user1 = User::factory()->create(['email' => 'user1@example.com']);
            $user2 = User::factory()->create(['email' => 'user2@example.com']);
            $this->actingAs($user1);

            $response = $this->put('/profile', [
                'name' => 'Updated Name',
                'email' => 'user2@example.com',
            ]);

            $response->assertSessionHasErrors('email');
        });

        test('name is required for profile update', function () {
            $user = User::factory()->create();
            $this->actingAs($user);

            $response = $this->put('/profile', [
                'name' => '',
                'email' => 'test@example.com',
            ]);

            $response->assertSessionHasErrors('name');
        });

        test('email is required for profile update', function () {
            $user = User::factory()->create();
            $this->actingAs($user);

            $response = $this->put('/profile', [
                'name' => 'John Doe',
                'email' => '',
            ]);

            $response->assertSessionHasErrors('email');
        });
    });
});
