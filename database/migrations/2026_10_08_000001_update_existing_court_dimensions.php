<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $courts = DB::table('courts')->get();

        foreach ($courts as $court) {
            $currentSize = strtolower(trim((string) $court->size));
            $courtName = strtolower(trim((string) $court->court_name));

            // Courts designated as Junior / Training (including default Courts 3 and 4)
            if (str_contains($currentSize, 'junior')
                || str_contains($courtName, 'junior')
                || str_contains($courtName, 'training')
                || in_array($court->id, [3, 4])) {
                $newSize = 'Junior (10m x 4.5m)';
            } else {
                $newSize = 'Regular (13.41m x 6.10m)';
            }

            DB::table('courts')->where('id', $court->id)->update([
                'size' => $newSize,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert sizes to Standard if ever rolled back
        DB::table('courts')->update([
            'size' => 'Standard',
        ]);
    }
};
