<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Court;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function create()
    {
        if (Court::count() === 0) {
            (new \Database\Seeders\CourtSeeder())->run();
        }

        $courts = Court::query()
            ->where('court_status', 'available')
            ->get()
            ->map(fn (Court $court) => [
                'id' => $court->id,
                'name' => $court->court_name,
                'rate' => (float) $court->price_per_hour,
            ]);

        $bookings = Booking::query()
            ->where('date', '>=', now()->toDateString())
            ->where('booking_status', '!=', 'cancelled')
            ->get();

        $bookedSlots = [];
        foreach ($bookings as $booking) {
            $formattedSlot = \Carbon\Carbon::parse($booking->start_time)->format('g:i A') . ' - ' . \Carbon\Carbon::parse($booking->end_time)->format('g:i A');
            $dateStr = \Carbon\Carbon::parse($booking->date)->toDateString();
            $bookedSlots[$dateStr][$booking->court_id][] = $formattedSlot;
        }

        return view('booking', [
            'courts' => $courts,
            'bookedSlots' => $bookedSlots,
        ]);
    }

    public function store(StoreBookingRequest $request)
    {
        [$startTime, $endTime] = $request->parsedTimes();

        $court = Court::findOrFail($request->input('court_id'));

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'court_id' => $court->id,
            'event_id' => null,
            'date' => $request->input('date'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'booking_status' => 'confirmed',
        ]);

        $start = \Illuminate\Support\Carbon::parse($startTime);
        $end = \Illuminate\Support\Carbon::parse($endTime);
        $hours = max(1, $start->diffInMinutes($end) / 60);
        $amount = round($court->price_per_hour * $hours, 2);

        Payment::create([
            'booking_id' => $booking->id,
            'payment_method' => 'online',
            'payment_status' => 'paid',
            'amount' => $amount,
            'ref_num' => 'PAY-' . strtoupper(Str::random(8)),
            'date' => now()->toDateString(),
            'time' => now()->format('H:i:s'),
        ]);

        return redirect()
            ->route('bookings.index')
            ->with('status', 'Booking fully paid and confirmed for ' . $request->input('date') . '.');
    }

    public function index()
    {
        $bookings = Booking::with('court')
            ->where('user_id', Auth::id())
            ->orderByDesc('date')
            ->orderByDesc('start_time')
            ->get();

        return view('bookings-index', [
            'bookings' => $bookings,
        ]);
    }
}