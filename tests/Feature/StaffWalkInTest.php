<?php

use App\Models\Booking;
use App\Models\Court;
use App\Models\Payment;
use App\Models\User;

test('staff can view the walk-in booking creation form', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    $court = Court::create([
        'court_name' => 'Center Court',
        'price_per_hour' => 500,
        'court_status' => 'available',
    ]);

    $response = $this->actingAs($staff)->get(route('staff.walkin.create'));

    $response->assertOk();
    $response->assertSee('Walk-in Court Booking');
    $response->assertSee('Center Court');
    $response->assertSee('8:00 AM - 9:00 AM');
});

test('players and guests are blocked from accessing the staff walk-in portal', function () {
    $player = User::factory()->create(['role' => 'player']);

    // Guest redirected to login
    $this->get(route('staff.walkin.create'))->assertRedirect(route('login'));

    // Player blocked with 403 Forbidden
    $this->actingAs($player)
        ->get(route('staff.walkin.create'))
        ->assertForbidden();

    $this->actingAs($player)
        ->post(route('staff.walkin.store'), [])
        ->assertForbidden();
});

test('staff can create a walk-in booking for a guest customer with cash payment', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    $court = Court::create([
        'court_name' => 'Court 1',
        'price_per_hour' => 450,
        'court_status' => 'available',
    ]);

    $response = $this->actingAs($staff)->post(route('staff.walkin.store'), [
        'first_name' => 'juan',
        'middle_name' => 'carlos',
        'last_name' => 'delos reyes',
        'date' => today()->toDateString(),
        'court_id' => $court->id,
        'time_slot' => '10:00 AM - 11:00 AM',
        'payment_method' => 'cash',
        'attendance_status' => 'show',
    ]);

    $response->assertRedirect(route('staff.today'));
    $response->assertSessionHas('status');

    // Auto-created player user has title-cased name
    $user = User::where('last_name', 'Delos Reyes')->first();
    expect($user)->not->toBeNull();
    expect($user->first_name)->toBe('Juan');
    expect($user->middle_name)->toBe('Carlos');
    expect($user->name)->toBe('Juan Carlos Delos Reyes');

    // Booking created with 'show' status
    $booking = Booking::where('user_id', $user->id)->first();
    expect($booking)->not->toBeNull();
    expect($booking->court_id)->toBe($court->id);
    expect($booking->booking_status)->toBe('show');
    expect($booking->start_time)->toBe('10:00:00');
    expect($booking->end_time)->toBe('11:00:00');

    // Paid payment created with Cash method
    $payment = Payment::where('booking_id', $booking->id)->first();
    expect($payment)->not->toBeNull();
    expect($payment->payment_method)->toBe('cash');
    expect($payment->payment_status)->toBe('paid');
    expect((float) $payment->amount)->toBe(450.00);

    // Verify it appears on today's run-sheet
    $todayResponse = $this->actingAs($staff)->get(route('staff.today'));
    $todayResponse->assertSee('Juan Carlos Delos Reyes');
    $todayResponse->assertSee('Court 1');
});

test('staff can book a walk-in for an existing registered player by email', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    $player = User::factory()->create([
        'role' => 'player',
        'email' => 'regular.player@example.com',
        'first_name' => 'Regular',
        'last_name' => 'Player',
        'name' => 'Regular Player',
    ]);
    $court = Court::create([
        'court_name' => 'Court 2',
        'price_per_hour' => 600,
        'court_status' => 'available',
    ]);

    $response = $this->actingAs($staff)->post(route('staff.walkin.store'), [
        'first_name' => 'Regular',
        'last_name' => 'Player',
        'email' => 'regular.player@example.com',
        'date' => today()->toDateString(),
        'court_id' => $court->id,
        'time_slot' => '2:00 PM - 3:00 PM',
        'payment_method' => 'gcash',
        'attendance_status' => 'confirmed',
        'ref_num' => 'GCASH-987654321',
    ]);

    $response->assertRedirect(route('staff.today'));

    // Reused existing user
    $booking = Booking::where('user_id', $player->id)->first();
    expect($booking)->not->toBeNull();
    expect($booking->booking_status)->toBe('confirmed');

    $payment = Payment::where('booking_id', $booking->id)->first();
    expect($payment->payment_method)->toBe('gcash');
    expect($payment->ref_num)->toBe('GCASH-987654321');
    expect((float) $payment->amount)->toBe(600.00);
});

test('staff cannot book a slot that is already booked', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    $existingPlayer = User::factory()->create(['role' => 'player']);
    $court = Court::create([
        'court_name' => 'Court 3',
        'price_per_hour' => 500,
        'court_status' => 'available',
    ]);

    Booking::create([
        'user_id' => $existingPlayer->id,
        'court_id' => $court->id,
        'date' => today()->toDateString(),
        'start_time' => '08:00:00',
        'end_time' => '09:00:00',
        'booking_status' => 'confirmed',
    ]);

    $response = $this->actingAs($staff)
        ->from(route('staff.walkin.create'))
        ->post(route('staff.walkin.store'), [
            'first_name' => 'Walkin',
            'last_name' => 'Customer',
            'date' => today()->toDateString(),
            'court_id' => $court->id,
            'time_slot' => '8:00 AM - 9:00 AM',
            'payment_method' => 'cash',
            'attendance_status' => 'show',
        ]);

    $response->assertRedirect(route('staff.walkin.create'));
    $response->assertSessionHasErrors(['time_slot']);
});
