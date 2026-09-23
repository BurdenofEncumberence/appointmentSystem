<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CourtSeeder::class);

        // Seed Admin account
        User::firstOrCreate(
            ['email' => 'admin@kymnet.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // Seed Staff account
        User::firstOrCreate(
            ['email' => 'staff@kymnet.com'],
            [
                'first_name' => 'Staff',
                'last_name' => 'Member',
                'name' => 'Staff Member',
                'password' => Hash::make('staff123'),
                'role' => 'staff',
            ]
        );

        // Seed Player test account
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'first_name' => 'Test',
                'last_name' => 'User',
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'role' => 'player',
            ]
        );
    }
}
