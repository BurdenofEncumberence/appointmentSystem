<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Mail\BookingReceiptMail;
use App\Models\Booking;
use App\Models\Court;
use App\Models\Event;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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
        $eventId = $request->input('event_id');
        $createdBookings = [];

        // Calculate potential event discount
        $discountPercent = 0;
        if ($eventId) {
            $event = Event::find($eventId);
            if ($event && $event->discount) {
                $discountPercent = (float) $event->discount;
            }
        }

        DB::transaction(function () use ($slots, $refNum, $paymentMethod, $eventId, $discountPercent, &$createdBookings) {
            foreach ($slots as $item) {
                $court = Court::findOrFail($item['court_id']);

                $booking = Booking::create([
                    'user_id' => Auth::id(),
                    'court_id' => $court->id,
                    'event_id' => $eventId,
                    'date' => $item['date'],
                    'start_time' => $item['start_time'],
                    'end_time' => $item['end_time'],
                    'booking_status' => 'confirmed',
                    'booking_type' => 'online',
                ]);

                $start = Carbon::parse($item['start_time']);
                $end = Carbon::parse($item['end_time']);
                $hours = max(1, $start->diffInMinutes($end) / 60);
                $rawAmount = round($court->price_per_hour * $hours, 2);
                $amount = $discountPercent > 0
                    ? round($rawAmount * (1 - ($discountPercent / 100)), 2)
                    : $rawAmount;

                Payment::create([
                    'booking_id' => $booking->id,
                    'payment_method' => $paymentMethod,
                    'payment_status' => $paymentMethod === 'cash' ? 'pending' : 'paid',
                    'amount' => $amount,
                    'ref_num' => $refNum,
                    'date' => now()->toDateString(),
                    'time' => now()->format('H:i:s'),
                ]);

                $createdBookings[] = $booking;
            }
        });

        // Dispatch official booking receipt email to customer
        $user = Auth::user();
        if ($user && $user->email) {
            try {
                foreach ($createdBookings as $b) {
                    $b->loadMissing('court');
                }

                Mail::to($user->email)->send(new BookingReceiptMail(
                    user: $user,
                    bookings: $createdBookings,
                    refNum: $refNum,
                    paymentMethod: $paymentMethod,
                    discountPercent: $discountPercent,
                    event: isset($event) ? $event : null,
                ));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $count = count($createdBookings);
        $message = $count > 1
            ? "{$count} court reservations confirmed under transaction {$refNum}."
            : 'Booking confirmed for ' . ($slots[0]['date'] ?? today()->toDateString()) . '.';

        return redirect()
            ->route('bookings.index')
            ->with('status', $message);
    }

    public function index()
    {
        $bookings = Booking::with(['court', 'payments', 'event'])
            ->where('user_id', Auth::id())
            ->orderByDesc('date')
            ->orderByDesc('start_time')
            ->get();

        return view('bookings-index', [
            'bookings' => $bookings,
        ]);
    }
}
