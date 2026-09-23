<?php

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $bookings = Booking::with('court')->where('booking_status', '!=', 'cancelled')->get();

        foreach ($bookings as $booking) {
            if (! Payment::where('booking_id', $booking->id)->exists() && $booking->court) {
                Payment::create([
                    'booking_id' => $booking->id,
                    'payment_method' => 'online',
                    'payment_status' => 'paid',
                    'amount' => $booking->court->price_per_hour,
                    'ref_num' => 'PAY-' . str_pad((string) $booking->id, 6, '0', STR_PAD_LEFT),
                    'date' => now()->toDateString(),
                    'time' => now()->format('H:i:s'),
                ]);
            }
        }
    }

    public function down(): void
    {
        // No-op
    }
};
