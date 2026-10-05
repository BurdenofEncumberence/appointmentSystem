<?php

it('returns a successful response and displays enticing court information', function () {
    \App\Models\Court::create([
        'court_name' => 'Center Arena',
        'price_per_hour' => 500,
        'court_status' => 'available',
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Center Arena');
    $response->assertSee('Tournament-Grade Courts');
    $response->assertSee('Joint-Cushion Surface');
    $response->assertSee('Book Your Court in Under 60 Seconds');
    $response->assertSee('Frequently Asked Questions');
});
