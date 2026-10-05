<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Court;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function create()
    {
        $user = Auth::user();

        if ($user) {
            if ($user->isAdmin() || $user->hasRole('manager')) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->hasRole('staff')) {
                return redirect()->route('staff.today');
            }
        }

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
            $formattedSlot = Carbon::parse($booking->start_time)->format('g:i A') . ' - ' . Carbon::parse($booking->end_time)->format('g:i A');
            $dateStr = Carbon::parse($booking->date)->toDateString();
            $bookedSlots[$dateStr][$booking->court_id][] = $formattedSlot;
        }

        return view('booking', [
            'courts' => $courts,
            'bookedSlots' => $bookedSlots,
        ]);
    }

    public function store(StoreBookingRequest $request)
    {
        $user = Auth::user();

        if ($user && ($user->isAdmin() || $user->hasRole('manager'))) {
            return redirect()->route('admin.dashboard');
        }

        if ($user && $user->hasRole('staff')) {
            return redirect()->route('staff.today');
        }

        $slots = $request->parsedSlots();
        $refNum = 'PAY-' . strtoupper(Str::random(8));
        $paymentMethod = $request->input('payment_method', 'online') ?: 'online';
        $createdBookings = [];

        DB::transaction(function () use ($slots, $refNum, $paymentMethod, &$createdBookings) {
            foreach ($slots as $item) {
                $court = Court::findOrFail($item['court_id']);

                $booking = Booking::create([
                    'user_id' => Auth::id(),
                    'court_id' => $court->id,
                    'event_id' => null,
                    'date' => $item['date'],
                    'start_time' => $item['start_time'],
                    'end_time' => $item['end_time'],
                    'booking_status' => 'confirmed',
                ]);

                $start = Carbon::parse($item['start_time']);
                $end = Carbon::parse($item['end_time']);
                $hours = max(1, $start->diffInMinutes($end) / 60);
                $amount = round($court->price_per_hour * $hours, 2);

                Payment::create([
                    'booking_id' => $booking->id,
                    'payment_method' => $paymentMethod,
                    'payment_status' => 'paid',
                    'amount' => $amount,
                    'ref_num' => $refNum,
                    'date' => now()->toDateString(),
                    'time' => now()->format('H:i:s'),
                ]);

                $createdBookings[] = $booking;
            }
        });

        $count = count($createdBookings);
        $message = $count > 1
            ? "{$count} court reservations fully paid and confirmed under transaction {$refNum}."
            : 'Booking fully paid and confirmed for ' . ($slots[0]['date'] ?? today()->toDateString()) . '.';

        return redirect()
            ->route('bookings.index')
            ->with('status', $message);
    }

    public function index()
    {
        $bookings = Booking::with(['court', 'payments'])
            ->where('user_id', Auth::id())
            ->orderByDesc('date')
            ->orderByDesc('start_time')
            ->get();

        return view('bookings-index', [
            'bookings' => $bookings,
        ]);
    }
}