<?php

use App\Models\Booking;
use App\Models\Court;
use App\Models\Payment;
use App\Models\User;

test('a player can book multiple courts and multiple times in one transaction', function () {
    $player = User::factory()->create(['role' => 'player']);
    $admin = User::factory()->create(['role' => 'admin']);

    $courtA = Court::create([
        'court_name' => 'Court Alpha',
        'price_per_hour' => 500.00,
        'court_status' => 'available',
    ]);

    $courtB = Court::create([
        'court_name' => 'Court Beta',
        'price_per_hour' => 600.00,
        'court_status' => 'available',
    ]);

    $targetDate = today()->addDays(2)->toDateString();

    // Player books 3 slots:
    // 1. Court Alpha at 8:00 AM - 9:00 AM (₱500)
    // 2. Court Alpha at 9:00 AM - 10:00 AM (₱500)
    // 3. Court Beta at 8:00 AM - 9:00 AM (₱600)
    $payload = [
        'slots' => [
            [
                'court_id' => $courtA->id,
                'date' => $targetDate,
                'time_slot' => '8:00 AM - 9:00 AM',
            ],
            [
                'court_id' => $courtA->id,
                'date' => $targetDate,
                'time_slot' => '9:00 AM - 10:00 AM',
            ],
            [
                'court_id' => $courtB->id,
                'date' => $targetDate,
                'time_slot' => '8:00 AM - 9:00 AM',
            ],
        ],
        'payment_method' => 'gcash',
    ];

    $response = $this->actingAs($player)->post(route('bookings.store'), $payload);

    $response->assertRedirect(route('bookings.index'));

    // Check that 3 bookings were created
    $bookings = Booking::where('user_id', $player->id)->get();
    expect($bookings)->toHaveCount(3);
    expect($bookings->every(fn ($b) => $b->booking_status === 'confirmed'))->toBeTrue();

    // Check that 3 payments were created
    $payments = Payment::whereIn('booking_id', $bookings->pluck('id'))->get();
    expect($payments)->toHaveCount(3);

    // Check that all 3 payments share the exact same transaction reference number
    $firstRef = $payments->first()->ref_num;
    expect($firstRef)->not->toBeNull();
    expect($payments->every(fn ($p) => $p->ref_num === $firstRef))->toBeTrue();
    expect($payments->every(fn ($p) => $p->payment_status === 'paid'))->toBeTrue();
    expect($payments->every(fn ($p) => $p->payment_method === 'gcash'))->toBeTrue();

    // Check individual payment amounts
    $alphaPayments = $payments->filter(fn ($p) => $p->booking->court_id === $courtA->id);
    expect($alphaPayments)->toHaveCount(2);
    expect((float) $alphaPayments->first()->amount)->toBe(500.00);

    $betaPayments = $payments->filter(fn ($p) => $p->booking->court_id === $courtB->id);
    expect($betaPayments)->toHaveCount(1);
    expect((float) $betaPayments->first()->amount)->toBe(600.00);

    // Total transaction amount should be 1,600.00
    expect((float) $payments->sum('amount'))->toBe(1600.00);

    // Verify admin finance view reflects the transaction and total
    $adminFinanceResponse = $this->actingAs($admin)->get(route('admin.finance'));
    $adminFinanceResponse->assertOk();
    $adminFinanceResponse->assertSee($firstRef);
    $adminFinanceResponse->assertSee('1,600.00');

    // Verify user courts booked view shows the bookings and transaction ref
    $userBookingsResponse = $this->actingAs($player)->get(route('bookings.index'));
    $userBookingsResponse->assertOk();
    $userBookingsResponse->assertSee('Court Alpha');
    $userBookingsResponse->assertSee('Court Beta');
    $userBookingsResponse->assertSee($firstRef);
});

test('a player can book multiple slots via json-encoded slots payload from Alpine.js', function () {
    $player = User::factory()->create(['role' => 'player']);
    $court = Court::create([
        'court_name' => 'Court Gamma',
        'price_per_hour' => 450.00,
        'court_status' => 'available',
    ]);

    $targetDate = today()->addDay()->toDateString();

    $response = $this->actingAs($player)->post(route('bookings.store'), [
        'slots' => json_encode([
            [
                'court_id' => $court->id,
                'date' => $targetDate,
                'time_slot' => '10:00 AM - 11:00 AM',
            ],
            [
                'court_id' => $court->id,
                'date' => $targetDate,
                'time_slot' => '11:00 AM - 12:00 PM',
            ],
        ]),
        'payment_method' => 'card',
    ]);

    $response->assertRedirect(route('bookings.index'));

    $bookings = Booking::where('user_id', $player->id)->get();
    expect($bookings)->toHaveCount(2);

    $payments = Payment::whereIn('booking_id', $bookings->pluck('id'))->get();
    expect($payments)->toHaveCount(2);
    expect($payments->pluck('ref_num')->unique())->toHaveCount(1);
});

test('submitting duplicate slots in the same transaction is rejected', function () {
    $player = User::factory()->create(['role' => 'player']);
    $court = Court::create([
        'court_name' => 'Court Delta',
        'price_per_hour' => 400.00,
        'court_status' => 'available',
    ]);

    $targetDate = today()->addDay()->toDateString();

    $response = $this->actingAs($player)->post(route('bookings.store'), [
        'slots' => [
            [
                'court_id' => $court->id,
                'date' => $targetDate,
                'time_slot' => '1:00 PM - 2:00 PM',
            ],
            [
                'court_id' => $court->id,
                'date' => $targetDate,
                'time_slot' => '1:00 PM - 2:00 PM', // duplicate
            ],
        ],
    ]);

    $response->assertSessionHasErrors();
    expect(Booking::count())->toBe(0);
});

test('submitting a slot that is already booked is rejected without creating any bookings', function () {
    $player1 = User::factory()->create(['role' => 'player']);
    $player2 = User::factory()->create(['role' => 'player']);

    $court1 = Court::create([
        'court_name' => 'Court Epsilon',
        'price_per_hour' => 500.00,
        'court_status' => 'available',
    ]);

    $court2 = Court::create([
        'court_name' => 'Court Zeta',
        'price_per_hour' => 500.00,
        'court_status' => 'available',
    ]);

    $targetDate = today()->addDay()->toDateString();

    // Player 1 books Court 1 at 10:00 AM
    Booking::create([
        'user_id' => $player1->id,
        'court_id' => $court1->id,
        'date' => $targetDate,
        'start_time' => '10:00:00',
        'end_time' => '11:00:00',
        'booking_status' => 'confirmed',
    ]);

    // Player 2 attempts to book Court 1 (already booked) and Court 2 (open)
    $response = $this->actingAs($player2)->post(route('bookings.store'), [
        'slots' => [
            [
                'court_id' => $court1->id,
                'date' => $targetDate,
                'time_slot' => '10:00 AM - 11:00 AM',
            ],
            [
                'court_id' => $court2->id,
                'date' => $targetDate,
                'time_slot' => '10:00 AM - 11:00 AM',
            ],
        ],
    ]);

    $response->assertSessionHasErrors();
    // Verify atomic failure: no new bookings created for player 2
    expect(Booking::where('user_id', $player2->id)->count())->toBe(0);
});
