<?php

use App\Models\User;
use Laravel\Dusk\Browser;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
});

test('algorithms page is accessible to admin', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/algorithms')
            ->assertPathIs('/admin/algorithms');
    });
});

test('algorithms page has segment button', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/algorithms')
            ->assertPresent('form[action*="algorithms/segment"]');
    });
});

test('admin can re-run guest segmentation', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
            ->visit('/admin/algorithms')
            ->press('Re-run Segmentation')
            ->assertPathIs('/admin/algorithms')
            ->assertSee('successfully');
    });
});

test('guest cannot access algorithms page', function () {
    $guest = User::factory()->create(['role' => 'guest']);

    $this->browse(function (Browser $browser) use ($guest) {
        $browser->loginAs($guest)
            ->visit('/admin/algorithms')
            ->assertSee('403');
    });
});
