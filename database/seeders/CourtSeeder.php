<?php

namespace Database\Seeders;

use App\Models\Court;
use Illuminate\Database\Seeder;

class CourtSeeder extends Seeder
{
    public function run(): void
    {
        $courts = [
            ['court_name' => 'Court 1', 'size' => 'Standard', 'price_per_hour' => 500, 'court_status' => 'available'],
            ['court_name' => 'Court 2', 'size' => 'Standard', 'price_per_hour' => 500, 'court_status' => 'available'],
            ['court_name' => 'Court 3', 'size' => 'Standard', 'price_per_hour' => 500, 'court_status' => 'available'],
            ['court_name' => 'Court 4', 'size' => 'Standard', 'price_per_hour' => 500, 'court_status' => 'available'],
        ];

        foreach ($courts as $court) {
            Court::firstOrCreate(
                ['court_name' => $court['court_name']],
                $court
            );
        }
    }
}