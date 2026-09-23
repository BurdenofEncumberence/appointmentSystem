<?php

use App\Models\User;

it('blocks players from the admin workspace', function () {
    $user = User::factory()->create(['role' => 'player']);

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

it('blocks staff from the admin workspace', function () {
    $staff = User::factory()->create(['role' => 'staff']);

    $this->actingAs($staff)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

it('allows admins into the admin workspace', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk();
});

it('redirects admins to the operations dashboard after login', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'password' => 'password',
    ]);

    $this->post(route('login'), [
        'email' => $admin->email,
        'password' => 'password',
    ])->assertRedirect(route('admin.dashboard', absolute: false));
});
