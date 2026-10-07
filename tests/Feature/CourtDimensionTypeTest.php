<?php

use App\Models\Booking;
use App\Models\Court;
use App\Models\User;

test('court form renders fixed dropdown options for Regular (60x60) and Junior (30x30)', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('admin.courts.create'));

    $response->assertStatus(200);
    $response->assertSee('id="size"', false);
    $response->assertSee('name="size"', false);
    $response->assertSee('Regular (60x60)');
    $response->assertSee('Junior (30x30)');
});

test('admin can create a court with Regular (60x60) or Junior (30x30) dimensions', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $responseRegular = $this->actingAs($admin)->post(route('admin.courts.store'), [
        'court_name' => 'Center Court Alpha',
        'size' => 'Regular (60x60)',
        'price_per_hour' => 550.00,
        'court_status' => 'available',
    ]);

    $responseRegular->assertRedirect(route('admin.courts.index'));
    $this->assertDatabaseHas('courts', [
        'court_name' => 'Center Court Alpha',
        'size' => 'Regular (60x60)',
    ]);

    $responseJunior = $this->actingAs($admin)->post(route('admin.courts.store'), [
        'court_name' => 'Junior Training Arena',
        'size' => 'Junior (30x30)',
        'price_per_hour' => 400.00,
        'court_status' => 'available',
    ]);

    $responseJunior->assertRedirect(route('admin.courts.index'));
    $this->assertDatabaseHas('courts', [
        'court_name' => 'Junior Training Arena',
        'size' => 'Junior (30x30)',
    ]);
});

test('court creation fails validation when size is omitted', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->post(route('admin.courts.store'), [
        'court_name' => 'No Dimension Court',
        'size' => '',
        'price_per_hour' => 500.00,
        'court_status' => 'available',
    ]);

    $response->assertSessionHasErrors('size');
});

test('players see court dimensions on booking schedule and summary', function () {
    $player = User::factory()->create(['role' => 'player']);

    Court::create([
        'court_name' => 'Main Stadium Court',
        'size' => 'Regular (60x60)',
        'price_per_hour' => 600.00,
        'court_status' => 'available',
    ]);

    Court::create([
        'court_name' => 'Academy Junior Court',
        'size' => 'Junior (30x30)',
        'price_per_hour' => 350.00,
        'court_status' => 'available',
    ]);

    $response = $this->actingAs($player)->get(route('booking'));

    $response->assertStatus(200);
    $response->assertSee('Main Stadium Court');
    $response->assertSee('Academy Junior Court');
    $response->assertSee('Regular (60x60)');
    $response->assertSee('Junior (30x30)');
});

test('players see court dimensions on public welcome page and booking history', function () {
    $court = Court::create([
        'court_name' => 'Pro Championship Court',
        'size' => 'Regular (60x60)',
        'price_per_hour' => 700.00,
        'court_status' => 'available',
    ]);

    // Public welcome page
    $publicResponse = $this->get('/');
    $publicResponse->assertStatus(200);
    $publicResponse->assertSee('Pro Championship Court');
    $publicResponse->assertSee('Regular (60x60)');

    // Player booking history
    $player = User::factory()->create(['role' => 'player']);
    Booking::create([
        'user_id' => $player->id,
        'court_id' => $court->id,
        'reference_number' => 'REF-DIM-123',
        'date' => today()->addDay()->toDateString(),
        'start_time' => '09:00:00',
        'end_time' => '10:00:00',
        'booking_status' => 'confirmed',
    ]);

    $historyResponse = $this->actingAs($player)->get(route('bookings.index'));
    $historyResponse->assertStatus(200);
    $historyResponse->assertSee('Pro Championship Court');
    $historyResponse->assertSee('Regular (60x60)');
});
