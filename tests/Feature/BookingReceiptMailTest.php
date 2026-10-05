<?php

use App\Mail\BookingReceiptMail;
use App\Models\Court;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('successful online booking sends an official receipt email to the user', function () {
    Mail::fake();

    $player = User::factory()->create([
        'role' => 'player',
        'email' => 'player@example.com',
        'name' => 'Alex Player',
    ]);

    $court = Court::create([
        'court_name' => 'Championship Court 1',
        'price_per_hour' => 350.00,
        'court_status' => 'available',
    ]);

    $response = $this->actingAs($player)->post(route('bookings.store'), [
        'slots' => [
            [
                'court_id' => $court->id,
                'date' => today()->addDay()->toDateString(),
                'time_slot' => '9:00 AM - 11:00 AM',
            ],
        ],
        'payment_method' => 'gcash',
    ]);

    $response->assertRedirect(route('bookings.index'));

    Mail::assertSent(BookingReceiptMail::class, function ($mail) use ($player, $court) {
        $mail->hasTo($player->email);
        $this->assertSame($player->id, $mail->user->id);
        $this->assertCount(1, $mail->bookings);
        $this->assertEquals(700.00, $mail->totalAmount); // 350 * 2 hours
        $this->assertStringStartsWith('PAY-', $mail->refNum);
        return true;
    });
});

test('successful online booking with event discount applies discount to receipt email', function () {
    Mail::fake();

    $player = User::factory()->create([
        'role' => 'player',
        'email' => 'discounted@example.com',
        'name' => 'Sarah Player',
    ]);

    $court = Court::create([
        'court_name' => 'Championship Court 2',
        'price_per_hour' => 400.00,
        'court_status' => 'available',
    ]);

    $event = Event::create([
        'event_title' => 'Opening Summer Slam',
        'details' => 'Summer tournament discount',
        'start_date' => today()->subDay(),
        'end_date' => today()->addDays(5),
        'discount' => 20,
    ]);

    $response = $this->actingAs($player)->post(route('bookings.store'), [
        'slots' => [
            [
                'court_id' => $court->id,
                'date' => today()->addDay()->toDateString(),
                'time_slot' => '2:00 PM - 3:00 PM',
            ],
        ],
        'event_id' => $event->id,
        'payment_method' => 'online',
    ]);

    $response->assertRedirect(route('bookings.index'));

    Mail::assertSent(BookingReceiptMail::class, function ($mail) use ($player) {
        $this->assertEquals(400.00, $mail->subtotal);
        $this->assertEquals(80.00, $mail->discountAmount);
        $this->assertEquals(320.00, $mail->totalAmount);
        $this->assertEquals(20.0, $mail->discountPercent);
        return true;
    });
});

test('successful walk-in booking sends a receipt email when customer email is provided', function () {
    Mail::fake();

    $staff = User::factory()->create(['role' => 'staff']);

    $court = Court::create([
        'court_name' => 'Court 3',
        'price_per_hour' => 300.00,
        'court_status' => 'available',
    ]);

    $response = $this->actingAs($staff)->post(route('staff.walkin.store'), [
        'first_name' => 'Carlos',
        'middle_name' => '',
        'last_name' => 'Sainz',
        'email' => 'carlos.sainz@example.com',
        'phone' => '09123456789',
        'date' => today()->addDay()->toDateString(),
        'court_id' => $court->id,
        'time_slot' => '9:00 AM - 10:00 AM',
        'payment_method' => 'cash',
        'attendance_status' => 'confirmed',
    ]);

    $response->assertRedirect(route('staff.today'));

    Mail::assertSent(BookingReceiptMail::class, function ($mail) {
        $mail->hasTo('carlos.sainz@example.com');
        $this->assertEquals(300.00, $mail->totalAmount);
        return true;
    });
});

test('booking receipt email renders HTML with transaction details and branding', function () {
    $player = User::factory()->create([
        'role' => 'player',
        'name' => 'Maria Ressa',
        'email' => 'maria@example.com',
    ]);

    $court = Court::create([
        'court_name' => 'Championship Court 4',
        'price_per_hour' => 500.00,
        'court_status' => 'available',
    ]);

    $booking = \App\Models\Booking::create([
        'user_id' => $player->id,
        'court_id' => $court->id,
        'date' => today()->addDay()->toDateString(),
        'start_time' => '16:00:00',
        'end_time' => '18:00:00',
        'booking_status' => 'confirmed',
        'booking_type' => 'online',
    ]);

    $booking->setRelation('court', $court);

    $mailable = new BookingReceiptMail(
        user: $player,
        bookings: [$booking],
        refNum: 'PAY-TEST999',
        paymentMethod: 'GCash',
        discountPercent: 10,
    );

    $rendered = $mailable->render();

    expect($rendered)->toContain('PAY-TEST999')
        ->toContain('Maria Ressa')
        ->toContain('maria@example.com')
        ->toContain('Championship Court 4')
        ->toContain('10% OFF')
        ->toContain('Total Amount Paid');
});
