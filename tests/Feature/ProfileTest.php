<?php

use App\Models\User;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});

test('updating profile with lowercase names automatically capitalizes first letter of names', function () {
    $user = User::factory()->create([
        'first_name' => 'Initial',
        'middle_name' => 'Middle',
        'last_name' => 'Name',
        'name' => 'Initial Middle Name',
    ]);

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'first_name' => 'juan carlos',
            'middle_name' => 'de la cruz',
            'last_name' => 'reyes',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Juan Carlos', $user->first_name);
    $this->assertSame('De La Cruz', $user->middle_name);
    $this->assertSame('Reyes', $user->last_name);
    $this->assertSame('Juan Carlos De La Cruz Reyes', $user->name);
});

test('user model automatically capitalizes first letter of names on direct creation', function () {
    $user = User::create([
        'first_name' => 'maria clara',
        'middle_name' => 'de los santos',
        'last_name' => 'ibarra',
        'email' => 'maria.clara@example.com',
        'password' => 'secret123',
    ]);

    $this->assertSame('Maria Clara', $user->first_name);
    $this->assertSame('De Los Santos', $user->middle_name);
    $this->assertSame('Ibarra', $user->last_name);
    $this->assertSame('Maria Clara De Los Santos Ibarra', $user->name);
});
