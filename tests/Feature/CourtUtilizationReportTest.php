<?php

use App\Models\Booking;
use App\Models\Court;
use App\Models\Payment;
use App\Models\User;
use App\Services\CourtUtilizationService;
use Illuminate\Support\Carbon;

test('court utilization service calculates capacity and utilization rates accurately', function () {
    $service = new CourtUtilizationService();

    // Create 2 courts
    $court1 = Court::create([
        'court_name' => 'Court Center',
        'price_per_hour' => 500,
        'court_status' => 'available',
    ]);

    $court2 = Court::create([
        'court_name' => 'Court West',
        'price_per_hour' => 600,
        'court_status' => 'available',
    ]);

    $player = User::factory()->create(['role' => 'player']);

    // Book 4 hours on Court 1 today
    for ($i = 0; $i < 4; $i++) {
        $b = Booking::create([
            'user_id' => $player->id,
            'court_id' => $court1->id,
            'date' => today()->toDateString(),
            'start_time' => sprintf('%02d:00:00', 8 + $i),
            'end_time' => sprintf('%02d:00:00', 9 + $i),
            'booking_status' => 'confirmed',
            'booking_type' => 'online',
        ]);

        Payment::create([
            'booking_id' => $b->id,
            'amount' => 500,
            'payment_status' => 'paid',
            'payment_method' => 'gcash',
            'date' => today()->toDateString(),
            'time' => '10:00:00',
            'ref_num' => 'PAY-UTIL-' . ($i + 1),
        ]);
    }

    // Cancelled booking on Court 2 should not count
    Booking::create([
        'user_id' => $player->id,
        'court_id' => $court2->id,
        'date' => today()->toDateString(),
        'start_time' => '14:00:00',
        'end_time' => '15:00:00',
        'booking_status' => 'cancelled',
        'booking_type' => 'online',
    ]);

    $facility = $service->getFacilityMetrics(Carbon::today());

    // 2 courts * 16 hours daily capacity = 32 hours
    expect($facility['total_courts_count'])->toBe(2);
    expect($facility['today_capacity_hours'])->toBe(32);
    expect($facility['today_booked_hours'])->toBe(4.0);
    // (4 / 32) * 100 = 12.5%
    expect($facility['today_utilization_rate'])->toBe(12.5);
    expect($facility['busiest_court_name'])->toBe('Court Center');

    $breakdown = $service->getCourtBreakdown(Carbon::today());
    $c1Metrics = $breakdown->firstWhere('id', $court1->id);
    expect($c1Metrics)->not->toBeNull();
    expect($c1Metrics['today_booked_hours'])->toBe(4.0);
    // Court 1: 4 hours / 16 hours = 25.0%
    expect($c1Metrics['today_utilization_rate'])->toBe(25.0);
    expect($c1Metrics['revenue_generated'])->toBe(2000.0);

    $c2Metrics = $breakdown->firstWhere('id', $court2->id);
    expect($c2Metrics['today_booked_hours'])->toBe(0.0);
    expect($c2Metrics['today_utilization_rate'])->toBe(0.0);
});

test('admin dashboard renders court utilization metrics and capacity analytics', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $court = Court::create([
        'court_name' => 'Championship Court',
        'price_per_hour' => 700,
        'court_status' => 'available',
    ]);

    $player = User::factory()->create(['role' => 'player']);

    $booking = Booking::create([
        'user_id' => $player->id,
        'court_id' => $court->id,
        'date' => today()->toDateString(),
        'start_time' => '10:00:00',
        'end_time' => '11:00:00',
        'booking_status' => 'confirmed',
        'booking_type' => 'online',
    ]);

    Payment::create([
        'booking_id' => $booking->id,
        'amount' => 700,
        'payment_status' => 'paid',
        'payment_method' => 'card',
        'date' => today()->toDateString(),
        'time' => '10:00:00',
        'ref_num' => 'PAY-CHAMP-1',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertViewHas('utilization');
    $response->assertViewHas('courtUtilization');

    // Assert court utilization report strings
    $response->assertSee('Court utilization');
    $response->assertSee('Court Utilization Metrics');
    $response->assertSee('Championship Court');
    $response->assertSee('Monthly Utilization');
    $response->assertSee('Busiest Court');
    $response->assertSee('Peak Booking Hour');
});

test('admin finance view includes court utilization and revenue yield analysis table', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $court = Court::create([
        'court_name' => 'Stadium Arena',
        'price_per_hour' => 800,
        'court_status' => 'available',
    ]);

    $player = User::factory()->create(['role' => 'player']);

    $booking = Booking::create([
        'user_id' => $player->id,
        'court_id' => $court->id,
        'date' => today()->toDateString(),
        'start_time' => '18:00:00',
        'end_time' => '19:00:00',
        'booking_status' => 'confirmed',
        'booking_type' => 'online',
    ]);

    Payment::create([
        'booking_id' => $booking->id,
        'amount' => 800,
        'payment_status' => 'paid',
        'payment_method' => 'gcash',
        'date' => today()->toDateString(),
        'time' => '18:00:00',
        'ref_num' => 'PAY-STAD-1',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.finance'));

    $response->assertOk();
    $response->assertViewHas('utilization');
    $response->assertViewHas('courtUtilization');

    // Assert financial yield and utilization report strings
    $response->assertSee('Court Utilization & Revenue Yield Analysis', false);
    $response->assertSee('Facility MTD Utilization');
    $response->assertSee('Monthly Operating Capacity');
    $response->assertSee('Stadium Arena');
    $response->assertSee('RevPACH (Yield/hr)', false);
    $response->assertSee('Booked Hours (MTD)');
});

test('admin courts index renders fleet utilization metric and table column', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Court::create([
        'court_name' => 'Court Prime',
        'price_per_hour' => 500,
        'court_status' => 'available',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.courts.index'));

    $response->assertOk();
    $response->assertViewHas('utilization');
    $response->assertViewHas('courtUtilization');

    $response->assertSee('Fleet Utilization');
    $response->assertSee('Utilization (MTD)');
    $response->assertSee('Court Prime');
});
