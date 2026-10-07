<?php

namespace Database\Seeders;

use App\Models\Court;
use Illuminate\Database\Seeder;

class CourtSeeder extends Seeder
{
    public function run(): void
    {
        $courts = [
            ['court_name' => 'Court 1', 'size' => 'Regular (13.41m x 6.10m)', 'price_per_hour' => 500, 'court_status' => 'available'],
            ['court_name' => 'Court 2', 'size' => 'Regular (13.41m x 6.10m)', 'price_per_hour' => 500, 'court_status' => 'available'],
            ['court_name' => 'Court 3', 'size' => 'Junior (10m x 4.5m)', 'price_per_hour' => 400, 'court_status' => 'available'],
            ['court_name' => 'Court 4', 'size' => 'Junior (10m x 4.5m)', 'price_per_hour' => 400, 'court_status' => 'available'],
        ];

        foreach ($courts as $court) {
            Court::updateOrCreate(
                ['court_name' => $court['court_name']],
                $court
            );
        }
    }
}