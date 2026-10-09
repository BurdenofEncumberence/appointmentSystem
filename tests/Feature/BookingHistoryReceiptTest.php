<?php

use App\Models\Booking;
use App\Models\Court;
use App\Models\Payment;
use App\Models\User;

test('admin can access admin booking history page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $court = Court::create([
        'court_name' => 'Court Test 1',
        'price_per_hour' => 350,
        'court_status' => 'available',
    ]);
    $booking = Booking::create([
        'user_id' => $admin->id,
        'court_id' => $court->id,
        'date' => today()->toDateString(),
        'start_time' => '08:00',
        'end_time' => '09:00',
        'booking_status' => 'confirmed',
        'booking_type' => 'online',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.bookings.index'))
        ->assertOk()
        ->assertViewIs('admin.bookings.index')
        ->assertSee('Booking Records')
        ->assertSee('Court Test 1');
});

test('manager can access admin booking history page', function () {
    $manager = User::factory()->create(['role' => 'manager']);

    $this->actingAs($manager)
        ->get(route('admin.bookings.index'))
        ->assertOk()
        ->assertViewIs('admin.bookings.index');
});

test('regular player cannot access admin booking history page', function () {
    $player = User::factory()->create(['role' => 'player']);

    $this->actingAs($player)
        ->get(route('admin.bookings.index'))
        ->assertForbidden();
});

test('staff cannot access admin booking history page', function () {
    $staff = User::factory()->create(['role' => 'staff']);

    $this->actingAs($staff)
        ->get(route('admin.bookings.index'))
        ->assertForbidden();
});

test('admin booking history filters correctly by status and search', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $playerA = User::factory()->create(['name' => 'John Doe Target', 'email' => 'target@example.com']);
    $court = Court::create([
        'court_name' => 'Center Court Alpha',
        'price_per_hour' => 400,
        'court_status' => 'available',
    ]);

    $bookingA = Booking::create([
        'user_id' => $playerA->id,
        'court_id' => $court->id,
        'date' => today()->toDateString(),
        'start_time' => '10:00',
        'end_time' => '11:00',
        'booking_status' => 'confirmed',
        'booking_type' => 'online',
    ]);

    $bookingB = Booking::create([
        'user_id' => $admin->id,
        'court_id' => $court->id,
        'date' => today()->toDateString(),
        'start_time' => '11:00',
        'end_time' => '12:00',
        'booking_status' => 'cancelled',
        'booking_type' => 'walk_in',
    ]);

    // Search query test
    $this->actingAs($admin)
        ->get(route('admin.bookings.index', ['search' => 'Target']))
        ->assertOk()
        ->assertSee('John Doe Target');

    // Status filter test
    $this->actingAs($admin)
        ->get(route('admin.bookings.index', ['status' => 'cancelled']))
        ->assertOk()
        ->assertSee('cancelled')
        ->assertDontSee('John Doe Target');
});

test('player can view their own booking receipt in html and json formats', function () {
    $player = User::factory()->create(['role' => 'player']);
    $court = Court::create([
        'court_name' => 'Court Receipt Test',
        'price_per_hour' => 300,
        'court_status' => 'available',
    ]);

    $booking = Booking::create([
        'user_id' => $player->id,
        'court_id' => $court->id,
        'date' => today()->toDateString(),
        'start_time' => '14:00',
        'end_time' => '15:00',
        'booking_status' => 'confirmed',
        'booking_type' => 'online',
    ]);

    Payment::create([
        'booking_id' => $booking->id,
        'payment_method' => 'gcash',
        'payment_status' => 'paid',
        'amount' => 300.00,
        'ref_num' => 'REC-TEST-999',
        'date' => today()->toDateString(),
        'time' => '14:00:00',
    ]);

    // HTML Receipt
    $this->actingAs($player)
        ->get(route('bookings.receipt', $booking))
        ->assertOk()
        ->assertViewIs('bookings.receipt')
        ->assertSee('REC-TEST-999')
        ->assertSee('Court Receipt Test');

    // JSON Receipt (used by receipt modal)
    $response = $this->actingAs($player)
        ->getJson(route('bookings.receipt', $booking));

    $response->assertOk()
        ->assertJsonPath('ref_num', 'REC-TEST-999')
        ->assertJsonPath('total_amount', 300)
        ->assertJsonPath('payment_method', 'GCash');
});

test('player cannot view another player booking receipt', function () {
    $player1 = User::factory()->create(['role' => 'player']);
    $player2 = User::factory()->create(['role' => 'player']);
    $court = Court::create([
        'court_name' => 'Court Private',
        'price_per_hour' => 300,
        'court_status' => 'available',
    ]);

    $booking = Booking::create([
        'user_id' => $player1->id,
        'court_id' => $court->id,
        'date' => today()->toDateString(),
        'start_time' => '14:00',
        'end_time' => '15:00',
        'booking_status' => 'confirmed',
        'booking_type' => 'online',
    ]);

    $this->actingAs($player2)
        ->get(route('bookings.receipt', $booking))
        ->assertForbidden();
});

test('admin, manager, and staff can view any booking receipt', function () {
    $player = User::factory()->create(['role' => 'player']);
    $court = Court::create([
        'court_name' => 'Court Universal',
        'price_per_hour' => 350,
        'court_status' => 'available',
    ]);

    $booking = Booking::create([
        'user_id' => $player->id,
        'court_id' => $court->id,
        'date' => today()->toDateString(),
        'start_time' => '16:00',
        'end_time' => '17:00',
        'booking_status' => 'confirmed',
        'booking_type' => 'online',
    ]);

    $admin = User::factory()->create(['role' => 'admin']);
    $manager = User::factory()->create(['role' => 'manager']);
    $staff = User::factory()->create(['role' => 'staff']);

    // Admin
    $this->actingAs($admin)
        ->get(route('bookings.receipt', $booking))
        ->assertOk();

    // Manager
    $this->actingAs($manager)
        ->get(route('bookings.receipt', $booking))
        ->assertOk();

    // Staff
    $this->actingAs($staff)
        ->get(route('bookings.receipt', $booking))
        ->assertOk();
});
