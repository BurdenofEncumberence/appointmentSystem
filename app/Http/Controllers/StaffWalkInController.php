<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Court;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StaffWalkInController extends Controller
{
    /**
     * Standard 1-hour club match time slots.
     */
    protected array $standardTimeSlots = [
        '6:00 AM - 7:00 AM',
        '7:00 AM - 8:00 AM',
        '8:00 AM - 9:00 AM',
        '9:00 AM - 10:00 AM',
        '10:00 AM - 11:00 AM',
        '11:00 AM - 12:00 PM',
        '12:00 PM - 1:00 PM',
        '1:00 PM - 2:00 PM',
        '2:00 PM - 3:00 PM',
        '3:00 PM - 4:00 PM',
        '4:00 PM - 5:00 PM',
        '5:00 PM - 6:00 PM',
        '6:00 PM - 7:00 PM',
        '7:00 PM - 8:00 PM',
        '8:00 PM - 9:00 PM',
        '9:00 PM - 10:00 PM',
    ];

    /**
     * Display the walk-in booking creation form.
     */
    public function create(Request $request): View
    {
        $selectedDate = $request->input('date', today()->toDateString());
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate)) {
            $selectedDate = today()->toDateString();
        }

        $courts = Court::where('court_status', 'available')
            ->orderBy('court_name')
            ->get();

        $existingBookings = Booking::with('user')
            ->where('date', $selectedDate)
            ->where('booking_status', '!=', 'cancelled')
            ->get();

        // Map booked slots per court
        $bookedSlots = [];
        foreach ($existingBookings as $b) {
            $slotStr = Carbon::parse($b->start_time)->format('g:i A') . ' - ' . Carbon::parse($b->end_time)->format('g:i A');
            $bookedSlots[$b->court_id][] = $slotStr;
        }

        // Recent registered players for quick selection
        $recentPlayers = User::where('role', 'player')
            ->latest()
            ->take(20)
            ->get(['id', 'name', 'first_name', 'last_name', 'email']);

        return view('staff.walk-in', [
            'courts' => $courts,
            'timeSlots' => $this->standardTimeSlots,
            'selectedDate' => $selectedDate,
            'bookedSlots' => $bookedSlots,
            'recentPlayers' => $recentPlayers,
        ]);
    }

    /**
     * Process and store a walk-in customer booking.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'court_id' => ['required', 'integer', 'exists:courts,id'],
            'time_slot' => ['required', 'string'],
            'payment_method' => ['required', 'in:cash,gcash,maya,card,counter'],
            'attendance_status' => ['required', 'in:show,confirmed'],
            'ref_num' => ['nullable', 'string', 'max:100'],
        ]);

        $times = $this->parseSlotTimes($validated['time_slot']);
        if ($times === null) {
            return back()->withInput()->withErrors([
                'time_slot' => "Invalid time slot format '{$validated['time_slot']}'.",
            ]);
        }

        [$startTime, $endTime] = $times;
        $court = Court::findOrFail($validated['court_id']);

        // Check for booking conflict
        $conflict = Booking::where('court_id', $court->id)
            ->where('date', $validated['date'])
            ->where('booking_status', '!=', 'cancelled')
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            })
            ->exists();

        if ($conflict) {
            return back()->withInput()->withErrors([
                'time_slot' => "{$court->court_name} is already booked on {$validated['date']} for {$validated['time_slot']}.",
            ]);
        }

        // Resolve or create User
        $firstName = User::titleCaseName($validated['first_name']);
        $middleName = User::titleCaseName($validated['middle_name'] ?? null);
        $lastName = User::titleCaseName($validated['last_name']);
        $fullName = trim(implode(' ', array_filter([$firstName, $middleName, $lastName])));

        $email = ! empty($validated['email']) ? strtolower(trim($validated['email'])) : null;
        $user = null;

        if ($email) {
            $user = User::where('email', $email)->first();
        }

        if (! $user) {
            if (! $email) {
                $slug = Str::slug($fullName) ?: 'walkin';
                $email = "walkin_{$slug}_" . time() . '_' . random_int(100, 999) . '@kymnet.local';
            }

            $user = User::create([
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'last_name' => $lastName,
                'name' => $fullName,
                'email' => $email,
                'password' => Hash::make(Str::random(16)),
                'role' => 'player',
                'email_verified_at' => now(),
            ]);
        }

        $paymentRef = ! empty($validated['ref_num'])
            ? trim($validated['ref_num'])
            : 'WALK-' . strtoupper(Str::random(8));

        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        $hours = max(1, $start->diffInMinutes($end) / 60);
        $amount = round($court->price_per_hour * $hours, 2);

        DB::transaction(function () use (
            $user,
            $court,
            $validated,
            $startTime,
            $endTime,
            $amount,
            $paymentRef
        ) {
            $booking = Booking::create([
                'user_id' => $user->id,
                'court_id' => $court->id,
                'event_id' => null,
                'date' => $validated['date'],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'booking_status' => $validated['attendance_status'],
            ]);

            Payment::create([
                'booking_id' => $booking->id,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'paid',
                'amount' => $amount,
                'ref_num' => $paymentRef,
                'date' => now()->toDateString(),
                'time' => now()->format('H:i:s'),
            ]);
        });

        $methodLabel = strtoupper($validated['payment_method']);
        $statusText = $validated['attendance_status'] === 'show' ? 'SHOW (Present)' : 'SCHEDULED';

        return redirect()->route('staff.today')->with(
            'status',
            "Walk-in booking confirmed for {$fullName} at {$court->court_name} ({$validated['time_slot']}). Attendance: {$statusText}. Payment: ₱" . number_format($amount, 2) . " via {$methodLabel} [{$paymentRef}]."
        );
    }

    /**
     * Parse "8:00 AM - 9:00 AM" into ['08:00:00', '09:00:00'].
     */
    protected function parseSlotTimes(?string $slot): ?array
    {
        if (empty($slot)) {
            return null;
        }

        $parts = array_map('trim', explode('-', $slot));
        if (count($parts) !== 2) {
            return null;
        }

        try {
            $start = Carbon::parse($parts[0])->format('H:i:s');
            $end = Carbon::parse($parts[1])->format('H:i:s');
        } catch (\Exception $e) {
            return null;
        }

        if ($start >= $end) {
            return null;
        }

        return [$start, $end];
    }
}
