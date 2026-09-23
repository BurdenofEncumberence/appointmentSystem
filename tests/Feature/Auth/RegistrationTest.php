<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register with first name, optional middle name, and last name', function () {
    $response = $this->post('/register', [
        'first_name' => 'John',
        'middle_name' => 'Fitzgerald',
        'last_name' => 'Kennedy',
        'email' => 'john.kennedy@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $this->assertDatabaseHas('users', [
        'first_name' => 'John',
        'middle_name' => 'Fitzgerald',
        'last_name' => 'Kennedy',
        'name' => 'John Fitzgerald Kennedy',
        'email' => 'john.kennedy@example.com',
    ]);
});

test('new users can register without middle name', function () {
    $response = $this->post('/register', [
        'first_name' => 'Jane',
        'middle_name' => '',
        'last_name' => 'Doe',
        'email' => 'jane.doe@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $this->assertDatabaseHas('users', [
        'first_name' => 'Jane',
        'middle_name' => null,
        'last_name' => 'Doe',
        'name' => 'Jane Doe',
        'email' => 'jane.doe@example.com',
    ]);
});

