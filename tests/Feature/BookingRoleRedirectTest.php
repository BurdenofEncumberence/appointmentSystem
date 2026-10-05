<?php

use App\Models\Court;
use App\Models\User;

test('admin accessing booking page is redirected to admin dashboard', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('booking'))
        ->assertRedirect(route('admin.dashboard'));
});

test('manager accessing booking page is redirected to admin dashboard', function () {
    $manager = User::factory()->create(['role' => 'manager']);

    $this->actingAs($manager)
        ->get(route('booking'))
        ->assertRedirect(route('admin.dashboard'));
});

test('staff accessing booking page is redirected to staff today', function () {
    $staff = User::factory()->create(['role' => 'staff']);

    $this->actingAs($staff)
        ->get(route('booking'))
        ->assertRedirect(route('staff.today'));
});

test('player accessing booking page is allowed', function () {
    $player = User::factory()->create(['role' => 'player']);

    $this->actingAs($player)
        ->get(route('booking'))
        ->assertOk();
});

test('admin cannot book a court via post request', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $court = Court::create([
        'court_name' => 'Court C',
        'price_per_hour' => 300,
        'court_status' => 'available',
    ]);

    $this->actingAs($admin)
        ->post(route('bookings.store'), [
            'court_id' => $court->id,
            'date' => today()->addDay()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
        ])
        ->assertRedirect(route('admin.dashboard'));
});

test('manager cannot book a court via post request', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $court = Court::create([
        'court_name' => 'Court M',
        'price_per_hour' => 300,
        'court_status' => 'available',
    ]);

    $this->actingAs($manager)
        ->post(route('bookings.store'), [
            'court_id' => $court->id,
            'date' => today()->addDay()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
        ])
        ->assertRedirect(route('admin.dashboard'));
});

test('staff cannot book a court via post request', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    $court = Court::create([
        'court_name' => 'Court D',
        'price_per_hour' => 300,
        'court_status' => 'available',
    ]);

    $this->actingAs($staff)
        ->post(route('bookings.store'), [
            'court_id' => $court->id,
            'date' => today()->addDay()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
        ])
        ->assertRedirect(route('staff.today'));
});

test('admin logging in with intended booking is redirected to admin dashboard', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'password' => 'password',
    ]);

    // Guest attempts to visit booking and gets redirected to login, setting intended url
    $this->get(route('booking'))->assertRedirect(route('login'));

    $response = $this->post(route('login'), [
        'email' => $admin->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
});

test('staff logging in with intended booking is redirected to staff today', function () {
    $staff = User::factory()->create([
        'role' => 'staff',
        'password' => 'password',
    ]);

    // Guest attempts to visit booking and gets redirected to login, setting intended url
    $this->get(route('booking'))->assertRedirect(route('login'));

    $response = $this->post(route('login'), [
        'email' => $staff->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('staff.today'));
});

test('manager logging in with intended booking is redirected to admin dashboard', function () {
    $manager = User::factory()->create([
        'role' => 'manager',
        'password' => 'password',
    ]);

    // Guest attempts to visit booking and gets redirected to login, setting intended url
    $this->get(route('booking'))->assertRedirect(route('login'));

    $response = $this->post(route('login'), [
        'email' => $manager->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
});

test('player logging in is redirected to homepage', function () {
    $player = User::factory()->create([
        'role' => 'player',
        'password' => 'password',
    ]);

    // Guest attempts to visit booking and gets redirected to login
    $this->get(route('booking'))->assertRedirect(route('login'));

    $response = $this->post(route('login'), [
        'email' => $player->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('welcome'));
});

test('welcome page reserve court link adapts to role', function () {
    // Guest
    $this->get('/')
        ->assertOk()
        ->assertSee(route('booking'));

    // Admin
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin)
        ->get('/')
        ->assertOk()
        ->assertSee(route('admin.dashboard'));

    // Staff
    $staff = User::factory()->create(['role' => 'staff']);
    $this->actingAs($staff)
        ->get('/')
        ->assertOk()
        ->assertSee(route('staff.today'));
});

test('player accessing dashboard route is redirected to homepage', function () {
    $player = User::factory()->create(['role' => 'player']);

    $this->actingAs($player)
        ->get('/dashboard')
        ->assertRedirect(route('welcome'));
});
