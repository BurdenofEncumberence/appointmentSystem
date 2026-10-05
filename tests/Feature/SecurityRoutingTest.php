<?php

use App\Models\Booking;
use App\Models\Court;
use App\Models\User;

test('http responses include standard security headers', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
    $response->assertHeader('X-XSS-Protection', '1; mode=block');
    $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
});

test('unverified users are redirected away from booking portal', function () {
    $unverifiedUser = User::factory()->unverified()->create(['role' => 'player']);

    $this->actingAs($unverifiedUser)
        ->get(route('booking'))
        ->assertRedirect(route('verification.notice'));
});

test('route parameter constraints reject non-numeric IDs with 404', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    $admin = User::factory()->create(['role' => 'admin']);

    // Non-numeric booking ID on staff route
    $this->actingAs($staff)
        ->patch('/staff/bookings/not-a-number/status', [
            'attendance_status' => 'show',
        ])
        ->assertNotFound();

    // Non-numeric court ID on admin route
    $this->actingAs($admin)
        ->get('/admin/courts/not-a-number/edit')
        ->assertNotFound();
});

test('staff cannot update attendance on cancelled bookings', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    $player = User::factory()->create(['role' => 'player']);
    $court = Court::create([
        'court_name' => 'Court Security Test',
        'price_per_hour' => 300,
        'court_status' => 'available',
    ]);

    $cancelledBooking = Booking::create([
        'user_id' => $player->id,
        'court_id' => $court->id,
        'date' => today()->toDateString(),
        'start_time' => '09:00:00',
        'end_time' => '10:00:00',
        'booking_status' => 'cancelled',
    ]);

    $response = $this->actingAs($staff)
        ->from(route('staff.today'))
        ->patch(route('staff.bookings.status', $cancelledBooking), [
            'attendance_status' => 'show',
        ]);

    $response->assertRedirect(route('staff.today'));
    $response->assertSessionHas('error', 'Cannot update attendance on a cancelled booking.');
    $this->assertSame('cancelled', $cancelledBooking->fresh()->booking_status);
});

test('staff cannot update attendance on bookings from a different date', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    $player = User::factory()->create(['role' => 'player']);
    $court = Court::create([
        'court_name' => 'Court Date Test',
        'price_per_hour' => 300,
        'court_status' => 'available',
    ]);

    $futureBooking = Booking::create([
        'user_id' => $player->id,
        'court_id' => $court->id,
        'date' => today()->addDays(5)->toDateString(),
        'start_time' => '09:00:00',
        'end_time' => '10:00:00',
        'booking_status' => 'confirmed',
    ]);

    $response = $this->actingAs($staff)
        ->from(route('staff.today'))
        ->patch(route('staff.bookings.status', $futureBooking), [
            'attendance_status' => 'show',
        ]);

    $response->assertRedirect(route('staff.today'));
    $response->assertSessionHas('error', "Attendance status can only be updated for today's scheduled bookings.");
    $this->assertSame('confirmed', $futureBooking->fresh()->booking_status);
});

test('rate limiting triggers 429 when booking endpoint is hammered', function () {
    $player = User::factory()->create(['role' => 'player']);

    // Send 25 rapid booking requests to exceed the 20-per-minute limit
    $lastResponse = null;
    for ($i = 0; $i < 25; $i++) {
        $lastResponse = $this->actingAs($player)->post(route('bookings.store'), [
            'slots' => [],
        ]);
    }

    $lastResponse->assertStatus(429);
});
