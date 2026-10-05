<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $users = DB::table('users')->get();

        foreach ($users as $user) {
            $firstName = User::titleCaseName($user->first_name);
            $middleName = User::titleCaseName($user->middle_name);
            $lastName = User::titleCaseName($user->last_name);
            $name = User::titleCaseName($user->name);

            if (! $firstName && ! $lastName && $name) {
                $nameParts = preg_split('/\s+/', trim($name));
                $firstName = User::titleCaseName(array_shift($nameParts) ?: null);
                $lastName = ! empty($nameParts) ? User::titleCaseName(array_pop($nameParts)) : null;
                $middleName = ! empty($nameParts) ? User::titleCaseName(implode(' ', $nameParts)) : null;
            } elseif (! $name && ($firstName || $lastName)) {
                $parts = array_filter([$firstName, $middleName, $lastName]);
                $name = implode(' ', $parts);
            }

            DB::table('users')->where('id', $user->id)->update([
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'last_name' => $lastName,
                'name' => $name,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op for casing normalization
    }
};
