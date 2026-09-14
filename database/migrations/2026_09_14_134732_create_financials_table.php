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
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('booking_id')->constrained()->onDelete('cascade');
        $table->string('payment_method');
        $table->string('payment_status')->default('pending');
        $table->decimal('amount', 8, 2);
        $table->string('ref_num')->nullable();
        $table->time('time');
        $table->date('date');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financials');
    }
};
