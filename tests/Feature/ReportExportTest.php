<?php

use App\Models\Booking;
use App\Models\Court;
use App\Models\Payment;
use App\Models\User;

beforeEach(function () {
    $this->court = Court::create([
        'court_name' => 'Court Alpha',
        'court_status' => 'available',
        'price_per_hour' => 500.00,
    ]);

    $this->player = User::factory()->create(['role' => 'player']);

    $this->booking = Booking::create([
        'court_id' => $this->court->id,
        'user_id' => $this->player->id,
        'date' => today()->toDateString(),
        'start_time' => '09:00',
        'end_time' => '11:00',
        'booking_status' => 'confirmed',
        'booking_type' => 'online',
    ]);

    $this->payment = Payment::create([
        'booking_id' => $this->booking->id,
        'amount' => 1000.00,
        'payment_status' => 'paid',
        'payment_method' => 'paymongo',
        'ref_num' => 'TEST-PAY-001',
        'date' => today()->toDateString(),
        'time' => '09:00:00',
    ]);
});

test('admin can export overall, financial, and utilization reports across all supported timeframes', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $periods = ['this_day', 'this_month', 'last_month', 'this_year'];
    $types = ['overall', 'financial', 'utilization'];

    foreach ($types as $type) {
        foreach ($periods as $period) {
            $response = $this->actingAs($admin)->get(route('admin.reports.export', [
                'type' => $type,
                'period' => $period,
            ]));

            $response->assertOk();
            $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
            $disposition = $response->headers->get('Content-Disposition');
            expect($disposition)->toContain("{$type}-report-{$period}");
        }
    }
});

test('manager can export overall, financial, and utilization reports across all supported timeframes', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $periods = ['this_day', 'this_month', 'last_month', 'this_year'];
    $types = ['overall', 'financial', 'utilization'];

    foreach ($types as $type) {
        foreach ($periods as $period) {
            $response = $this->actingAs($manager)->get(route('admin.reports.export', [
                'type' => $type,
                'period' => $period,
            ]));

            $response->assertOk();
            $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
            $disposition = $response->headers->get('Content-Disposition');
            expect($disposition)->toContain("{$type}-report-{$period}");
        }
    }
});

test('players and staff are forbidden from exporting reports', function () {
    $player = User::factory()->create(['role' => 'player']);
    $staff = User::factory()->create(['role' => 'staff']);

    $this->actingAs($player)
        ->get(route('admin.reports.export', ['type' => 'overall', 'period' => 'this_month']))
        ->assertForbidden();

    $this->actingAs($staff)
        ->get(route('admin.reports.export', ['type' => 'overall', 'period' => 'this_month']))
        ->assertForbidden();
});

test('exporting with invalid parameters returns validation error', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('admin.reports.export', ['type' => 'invalid_type', 'period' => 'this_month']))
        ->assertSessionHasErrors('type');

    $this->actingAs($admin)
        ->get(route('admin.reports.export', ['type' => 'overall', 'period' => 'invalid_period']))
        ->assertSessionHasErrors('period');
});
