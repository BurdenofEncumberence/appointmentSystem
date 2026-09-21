<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Court;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function create()
    {
        $courts = Court::query()
            ->where('court_status', 'available')
            ->get()
            ->map(fn (Court $court) => [
                'id' => $court->id,
                'name' => $court->court_name,
                'rate' => (float) $court->price_per_hour,
            ]);

        return view('booking', [
            'courts' => $courts,
        ]);
    }

    public function store(StoreBookingRequest $request)
    {
        [$startTime, $endTime] = $request->parsedTimes();

        Booking::create([
            'user_id' => Auth::id(),
            'court_id' => $request->input('court_id'),
            'event_id' => null,
            'date' => $request->input('date'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'booking_status' => 'pending',
        ]);

        return redirect()
            ->route('bookings.index')
            ->with('status', 'Booking confirmed for ' . $request->input('date') . '.');
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