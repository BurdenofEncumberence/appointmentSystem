<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('booking_type')->default('online')->after('booking_status');
            $table->index('booking_type');
        });

        // Backfill existing walk-in bookings based on WALK- reference numbers or walk-in user accounts
        DB::table('bookings')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('payments')
                    ->whereColumn('payments.booking_id', 'bookings.id')
                    ->where('payments.ref_num', 'like', 'WALK-%');
            })
            ->orWhereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('users')
                    ->whereColumn('users.id', 'bookings.user_id')
                    ->where('users.email', 'like', 'walkin_%');
            })
            ->update(['booking_type' => 'walk_in']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['booking_type']);
            $table->dropColumn('booking_type');
        });
    }
};
