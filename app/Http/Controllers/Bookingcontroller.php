<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Court;
use App\Models\Event;
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

        $events = Event::query()
            ->where('start_date', '<=', now()->addDays(30)->toDateString())
            ->where('end_date', '>=', now()->toDateString())
            ->get()
            ->map(fn (Event $event) => [
                'id' => $event->id,
                'title' => $event->event_title,
                'discount' => (float) $event->discount,
                'startDate' => $event->start_date->toDateString(),
                'endDate' => $event->end_date->toDateString(),
            ]);

        return view('booking', [
            'courts' => $courts,
            'bookedSlots' => $bookedSlots,
            'events' => $events,
        ]);
    }

    public function store(StoreBookingRequest $request)
    {
        $courtsData = $request->input('courts', []);

        // Check if courts array is empty after filtering
        if (empty($courtsData) || !is_array($courtsData)) {
            return back()->withInput()->withErrors(['courts' => 'Please select at least one court.']);
        }

        $bookings = [];
        $totalAmount = 0;

        foreach ($courtsData as $courtData) {
            [$startTime, $endTime] = $request->parsedTimes($courtData['time_slot']);

            $court = Court::findOrFail($courtData['court_id']);
            $bookingDate = $courtData['date'] ?? $request->input('date');

            $booking = Booking::create([
                'user_id' => Auth::id(),
                'court_id' => $court->id,
                'event_id' => $request->input('event_id'),
                'date' => $bookingDate,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'booking_status' => 'confirmed',
            ]);

            $start = \Illuminate\Support\Carbon::parse($startTime);
            $end = \Illuminate\Support\Carbon::parse($endTime);
            $hours = max(1, $start->diffInMinutes($end) / 60);
            $amount = round($court->price_per_hour * $hours, 2);

            $bookings[] = [
                'booking' => $booking,
                'amount' => $amount,
            ];

            $totalAmount += $amount;
        }

        // Apply event discount if applicable
        $discount = 0;
        if ($request->input('event_id')) {
            $event = Event::find($request->input('event_id'));
            if ($event && $event->discount) {
                $discount = $totalAmount * ($event->discount / 100);
                $totalAmount = $totalAmount - $discount;
            }
        }

        // Calculate amount per booking after discount
        $totalOriginalAmount = collect($bookings)->sum('amount');
        $amountPerBooking = [];
        foreach ($bookings as $bookingData) {
            $proportion = $totalOriginalAmount > 0 ? ($bookingData['amount'] / $totalOriginalAmount) : 0;
            $finalAmount = round($totalAmount * $proportion, 2);
            $amountPerBooking[$bookingData['booking']->id] = $finalAmount;
        }

        // Create payments for each booking
        foreach ($bookings as $bookingData) {
            Payment::create([
                'booking_id' => $bookingData['booking']->id,
                'payment_method' => $request->input('payment_method', 'online'),
                'payment_status' => $request->input('payment_method') === 'cash' ? 'pending' : 'paid',
                'amount' => $amountPerBooking[$bookingData['booking']->id],
                'ref_num' => 'PAY-' . strtoupper(Str::random(8)),
                'date' => now()->toDateString(),
                'time' => now()->format('H:i:s'),
            ]);
        }

        $courtCount = count($bookings);
        $dates = collect($courtsData)->pluck('date')->filter()->unique();
        $dateText = $dates->count() === 1 ? ' for ' . $dates->first() : '';

        return redirect()
            ->route('bookings.index')
            ->with('status', $courtCount . ' court(s) booked and confirmed' . $dateText . '.');
    }

    public function index()
    {
        $bookings = Booking::with(['court', 'event'])
            ->where('user_id', Auth::id())
            ->orderByDesc('date')
            ->orderByDesc('start_time')
            ->get();

        return view('bookings-index', [
            'bookings' => $bookings,
        ]);
    }
}