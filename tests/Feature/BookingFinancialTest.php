<?php

use App\Models\Booking;
use App\Models\Court;
use App\Models\Payment;
use App\Models\User;

it('records full payment upon successful customer booking and reflects in admin financials', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $player = User::factory()->create(['role' => 'player']);

    $court = Court::create([
        'court_name' => 'Court Center',
        'price_per_hour' => 750.00,
        'court_status' => 'available',
    ]);

    // Customer books an appointment
    $response = $this->actingAs($player)->post(route('bookings.store'), [
        'court_id' => $court->id,
        'date' => today()->addDay()->toDateString(),
        'time_slot' => '9:00 AM - 10:00 AM',
    ]);

    $response->assertRedirect(route('bookings.index'));

    // Verify booking was created and confirmed
    $booking = Booking::where('user_id', $player->id)->latest()->first();
    expect($booking)->not->toBeNull();
    expect($booking->booking_status)->toBe('confirmed');

    // Verify paid payment was generated
    $payment = Payment::where('booking_id', $booking->id)->first();
    expect($payment)->not->toBeNull();
    expect($payment->payment_status)->toBe('paid');
    expect((float) $payment->amount)->toBe(750.00);

    // Verify admin finance tab reflects the payment
    $adminFinanceResponse = $this->actingAs($admin)->get(route('admin.finance'));
    $adminFinanceResponse->assertOk();
    $adminFinanceResponse->assertSee('750.00');
    $adminFinanceResponse->assertSee($payment->ref_num);

    // Verify admin overview dashboard reflects the revenue
    $adminDashboardResponse = $this->actingAs($admin)->get(route('admin.dashboard'));
    $adminDashboardResponse->assertOk();
    $adminDashboardResponse->assertSee('750.00');
});

it('allows player to book multiple courts across different times and dates', function () {
    $player = User::factory()->create(['role' => 'player']);

    $court1 = Court::create([
        'court_name' => 'Court 1',
        'price_per_hour' => 500.00,
        'court_status' => 'available',
    ]);
    $court2 = Court::create([
        'court_name' => 'Court 2',
        'price_per_hour' => 500.00,
        'court_status' => 'available',
    ]);

    $date1 = today()->addDays(2)->toDateString();
    $date2 = today()->addDays(3)->toDateString();

    $response = $this->actingAs($player)->post(route('bookings.store'), [
        'courts' => [
            ['court_id' => $court1->id, 'time_slot' => '6:00 AM - 7:00 AM', 'date' => $date1],
            ['court_id' => $court1->id, 'time_slot' => '7:00 AM - 8:00 AM', 'date' => $date1],
            ['court_id' => $court2->id, 'time_slot' => '8:00 AM - 9:00 AM', 'date' => $date2],
        ],
        'payment_method' => 'gcash',
    ]);

    $response->assertRedirect(route('bookings.index'));
    $response->assertSessionHasNoErrors();

    expect(Booking::where('user_id', $player->id)->count())->toBe(3);
    expect(Booking::where('user_id', $player->id)->where('date', $date1)->count())->toBe(2);
    expect(Booking::where('user_id', $player->id)->where('date', $date2)->count())->toBe(1);
    expect(Payment::whereIn('booking_id', Booking::where('user_id', $player->id)->pluck('id'))->count())->toBe(3);
});

it('allows booking using courts_json fallback', function () {
    $player = User::factory()->create(['role' => 'player']);

    $court = Court::create([
        'court_name' => 'Court JSON',
        'price_per_hour' => 500.00,
        'court_status' => 'available',
    ]);

    $date = today()->addDays(4)->toDateString();

    $response = $this->actingAs($player)->post(route('bookings.store'), [
        'courts_json' => json_encode([
            ['courtId' => $court->id, 'timeSlot' => '10:00 AM - 11:00 AM', 'date' => $date],
        ]),
        'payment_method' => 'cash',
    ]);

    $response->assertRedirect(route('bookings.index'));
    $response->assertSessionHasNoErrors();

    $booking = Booking::where('user_id', $player->id)->where('court_id', $court->id)->first();
    expect($booking)->not->toBeNull();
    expect($booking->date)->toBe($date);

    $payment = Payment::where('booking_id', $booking->id)->first();
    expect($payment->payment_status)->toBe('pending');
});

it('renders the booking page cleanly without spilling javascript code', function () {
    $player = User::factory()->create(['role' => 'player']);

    $response = $this->actingAs($player)->get(route('booking'));

    $response->assertOk();
    $response->assertSee('x-data="bookingApp()"', false);
    $response->assertSee('Set Your Date and Time');
    $response->assertDontSee("return ' across '");
});


