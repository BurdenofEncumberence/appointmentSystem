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
        Schema::create('open_play_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('session_type')->default('open_play'); // 'open_play', 'tournament'
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('max_capacity')->default(12); // Total player slot tickets
            $table->string('skill_level')->default('All Levels'); // 'All Levels', 'Beginner (2.0 - 3.0)', 'Intermediate (3.5+)', 'Advanced (4.0+)'
            $table->decimal('price_per_slot', 8, 2); // Participation fee per player
            $table->text('details')->nullable();
            $table->string('session_status')->default('scheduled'); // 'scheduled', 'active', 'completed', 'cancelled'
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('open_play_session_courts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('open_play_session_id')->constrained('open_play_sessions')->cascadeOnDelete();
            $table->foreignId('court_id')->constrained('courts')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['open_play_session_id', 'court_id']);
        });

        Schema::create('open_play_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('open_play_session_id')->constrained('open_play_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('player_name');
            $table->string('player_email');
            $table->string('player_phone')->nullable();
            $table->unsignedInteger('slots_count')->default(1);
            $table->decimal('total_fee', 8, 2);
            $table->string('payment_status')->default('pending'); // 'paid', 'pending', 'cancelled'
            $table->string('payment_method')->default('cash');
            $table->string('ref_num')->unique();
            $table->string('attendance_status')->default('registered'); // 'registered', 'show', 'no_show'
            $table->string('paymongo_checkout_session_id')->nullable();
            $table->string('paymongo_payment_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('open_play_registrations');
        Schema::dropIfExists('open_play_session_courts');
        Schema::dropIfExists('open_play_sessions');
    }
};
