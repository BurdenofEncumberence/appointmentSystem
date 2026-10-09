<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('open_play_sessions', function (Blueprint $table) {
            $table->decimal('court_fee', 8, 2)->default(0)->after('price_per_slot');
            $table->string('host_payment_status')->default('unpaid')->after('session_status'); // unpaid, pending, paid
            $table->string('host_payment_method')->nullable()->after('host_payment_status'); // paymongo, cash
            $table->timestamp('host_paid_at')->nullable()->after('host_payment_method');
            $table->text('manager_note')->nullable()->after('details');
            $table->string('paymongo_checkout_session_id')->nullable()->after('manager_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('open_play_sessions', function (Blueprint $table) {
            $table->dropColumn([
                'court_fee',
                'host_payment_status',
                'host_payment_method',
                'host_paid_at',
                'manager_note',
                'paymongo_checkout_session_id',
            ]);
        });
    }
};
