<?php

namespace App\Http\Controllers;

use App\Mail\BookingReceiptMail;
use App\Models\Booking;
use App\Models\Court;
use App\Models\Payment;
use App\Models\User;
use App\Services\PayMongoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

        // Include courts allocated to active Open Play / Tournament sessions
        $openPlaySessions = \App\Models\OpenPlaySession::with('courts')
            ->whereDate('date', $selectedDate)
            ->where('session_status', '!=', 'cancelled')
            ->get();

        $specialSlots = [];
        foreach ($openPlaySessions as $session) {
            $sessionStart = Carbon::parse($session->start_time);
            $sessionEnd = Carbon::parse($session->end_time);

            $cur = $sessionStart->copy();
            while ($cur->lt($sessionEnd)) {
                $next = $cur->copy()->addHour();
                $slotStr = $cur->format('g:i A') . ' - ' . $next->format('g:i A');
                foreach ($session->courts as $allocatedCourt) {
                    $bookedSlots[$allocatedCourt->id][] = $slotStr;
                    $specialSlots[$allocatedCourt->id][$slotStr] = [
                        'type' => $session->session_type,
                        'title' => $session->title,
                    ];
                }
                $cur = $next;
            }
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
            'specialSlots' => $specialSlots,
            'recentPlayers' => $recentPlayers,
        ]);
    }

    /**
     * Process and store a walk-in customer booking.
     */
    public function store(Request $request, PayMongoService $payMongoService): RedirectResponse
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
            'payment_method' => ['required', 'in:cash,paymongo,gcash,maya,card,counter'],
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

        // PayMongo Online Gateway handling
        if ($validated['payment_method'] === 'paymongo') {
            if (! $payMongoService->isConfigured()) {
                return back()->withInput()->withErrors([
                    'payment_method' => 'PayMongo gateway secret key is not configured in the system environment.',
                ]);
            }

            $lineItems = [
                [
                    'name' => "{$court->court_name} Walk-In Booking",
                    'description' => "{$validated['date']} ({$validated['time_slot']})",
                    'amount' => (int) round($amount * 100),
                    'currency' => 'PHP',
                    'quantity' => 1,
                ],
            ];

            $successUrl = route('staff.walkin.paymongo.success') . '?session_id={CHECKOUT_SESSION_ID}&ref=' . $paymentRef;
            $cancelUrl = route('staff.walkin.paymongo.cancel') . '?session_id={CHECKOUT_SESSION_ID}&ref=' . $paymentRef;

            try {
                $checkout = $payMongoService->createCheckoutSession($lineItems, [
                    'description' => "KYMNET Walk-In ({$paymentRef})",
                    'reference_number' => $paymentRef,
                    'success_url' => $successUrl,
                    'cancel_url' => $cancelUrl,
                    'customer' => [
                        'name' => $fullName,
                        'email' => ($user && ! str_ends_with($user->email, '@kymnet.local')) ? $user->email : null,
                        'phone' => $validated['phone'] ?? null,
                    ],
                    'payment_method_types' => [
                        'qrph',
                        'dob',
                        'paymaya',
                        'gcash',
                        'card',
                    ],
                ]);
            } catch (\Throwable $e) {
                report($e);
                return back()->withInput()->withErrors([
                    'payment_method' => 'Unable to connect to PayMongo checkout: ' . $e->getMessage(),
                ]);
            }

            $sessionId = $checkout['id'] ?? null;
            $checkoutUrl = $checkout['checkout_url'] ?? null;

            if (! $sessionId || ! $checkoutUrl) {
                return back()->withInput()->withErrors([
                    'payment_method' => 'Invalid response from PayMongo checkout portal.',
                ]);
            }

            DB::transaction(function () use (
                $user,
                $court,
                $validated,
                $startTime,
                $endTime,
                $amount,
                $paymentRef,
                $sessionId
            ) {
                $booking = Booking::create([
                    'user_id' => $user->id,
                    'court_id' => $court->id,
                    'event_id' => null,
                    'date' => $validated['date'],
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'booking_status' => 'pending',
                    'booking_type' => 'walk_in',
                ]);

                Payment::create([
                    'booking_id' => $booking->id,
                    'payment_method' => 'paymongo',
                    'payment_status' => 'pending',
                    'amount' => $amount,
                    'ref_num' => $paymentRef,
                    'checkout_session_id' => $sessionId,
                    'date' => now()->toDateString(),
                    'time' => now()->format('H:i:s'),
                ]);
            });

            session(['walkin_attendance_' . $paymentRef => $validated['attendance_status']]);

            return redirect()->away($checkoutUrl);
        }

        // Standard counter payments (cash, manual gcash/maya/card)
        $createdBooking = null;

        DB::transaction(function () use (
            $user,
            $court,
            $validated,
            $startTime,
            $endTime,
            $amount,
            $paymentRef,
            &$createdBooking
        ) {
            $createdBooking = Booking::create([
                'user_id' => $user->id,
                'court_id' => $court->id,
                'event_id' => null,
                'date' => $validated['date'],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'booking_status' => $validated['attendance_status'],
                'booking_type' => 'walk_in',
            ]);

            Payment::create([
                'booking_id' => $createdBooking->id,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'paid',
                'amount' => $amount,
                'ref_num' => $paymentRef,
                'date' => now()->toDateString(),
                'time' => now()->format('H:i:s'),
            ]);
        });

        // Dispatch receipt email if user has a valid real email
        if ($user && $user->email && ! str_ends_with($user->email, '@kymnet.local')) {
            try {
                $createdBooking->setRelation('court', $court);
                Mail::to($user->email)->send(new BookingReceiptMail(
                    user: $user,
                    bookings: [$createdBooking],
                    refNum: $paymentRef,
                    paymentMethod: $validated['payment_method'],
                    discountPercent: 0,
                    event: null,
                ));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $methodLabel = strtoupper($validated['payment_method']);
        $statusText = $validated['attendance_status'] === 'show' ? 'SHOW (Present)' : 'SCHEDULED';

        return redirect()->route('staff.today')->with(
            'status',
            "Walk-in booking confirmed for {$fullName} at {$court->court_name} ({$validated['time_slot']}). Attendance: {$statusText}. Payment: ₱" . number_format($amount, 2) . " via {$methodLabel} [{$paymentRef}]."
        );
    }

    /**
     * Handle return from PayMongo hosted checkout on successful walk-in payment.
     */
    public function paymongoSuccess(Request $request, PayMongoService $payMongoService): RedirectResponse
    {
        $sessionId = $request->query('session_id');
        $refNum = $request->query('ref');

        if (! $sessionId && ! $refNum) {
            return redirect()->route('staff.today')
                ->with('status', 'No payment confirmation details provided.');
        }

        try {
            $sessionData = $sessionId ? $payMongoService->getCheckoutSession($sessionId) : [];
        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('staff.today')
                ->with('status', 'Unable to verify checkout status with PayMongo: ' . $e->getMessage());
        }

        $isPaid = $sessionData ? $payMongoService->isSessionPaid($sessionData) : false;

        $payment = Payment::where(function ($q) use ($sessionId, $refNum) {
            if ($sessionId) {
                $q->where('checkout_session_id', $sessionId);
            }
            if ($refNum) {
                $q->orWhere('ref_num', $refNum);
            }
        })->first();

        if (! $payment) {
            return redirect()->route('staff.today')
                ->with('status', 'Payment received, but no matching walk-in booking record was found.');
        }

        $booking = $payment->booking;

        if ($isPaid) {
            $details = $payMongoService->extractPaymentDetails($sessionData);
            $paymongoPaymentId = $details['payment_id'] ?? null;
            $sourceType = $details['source_type'] ?? 'online';

            $attendanceStatus = session()->pull('walkin_attendance_' . ($refNum ?? $payment->ref_num), 'show');

            DB::transaction(function () use ($payment, $booking, $sessionId, $paymongoPaymentId, $sourceType, $attendanceStatus) {
                $payment->update([
                    'payment_status' => 'paid',
                    'checkout_session_id' => $sessionId,
                    'paymongo_payment_id' => $paymongoPaymentId,
                    'payment_method' => 'paymongo_' . $sourceType,
                ]);

                if ($booking) {
                    $booking->update([
                        'booking_status' => $attendanceStatus,
                    ]);
                }
            });

            // Dispatch receipt email if user has a valid real email
            if ($booking && $booking->user && $booking->user->email && ! str_ends_with($booking->user->email, '@kymnet.local')) {
                try {
                    $booking->load('court');
                    $methodLabel = 'PayMongo (' . strtoupper(str_replace('_', ' ', $sourceType)) . ')';
                    Mail::to($booking->user->email)->send(new BookingReceiptMail(
                        user: $booking->user,
                        bookings: [$booking],
                        refNum: $payment->ref_num,
                        paymentMethod: $methodLabel,
                        discountPercent: 0,
                        event: null,
                    ));
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            $methodLabel = strtoupper(str_replace('_', ' ', $sourceType));
            $statusText = $booking && $booking->booking_status === 'show' ? 'SHOW (Present)' : 'SCHEDULED';

            return redirect()->route('staff.today')->with(
                'status',
                "Walk-in booking confirmed via PayMongo ({$methodLabel}) for {$booking?->user?->name}! Attendance: {$statusText}. Ref: {$payment->ref_num}."
            );
        }

        return redirect()->route('staff.today')->with(
            'status',
            "Payment for walk-in booking [{$payment->ref_num}] is pending confirmation."
        );
    }

    /**
     * Handle return when staff or customer cancels PayMongo checkout.
     */
    public function paymongoCancel(Request $request): RedirectResponse
    {
        $sessionId = $request->query('session_id');
        $refNum = $request->query('ref');

        $payment = Payment::query()
            ->when($sessionId, fn ($q) => $q->where('checkout_session_id', $sessionId))
            ->when($refNum && ! $sessionId, fn ($q) => $q->where('ref_num', $refNum))
            ->first();

        if ($payment && $payment->payment_status === 'pending') {
            $booking = $payment->booking;
            DB::transaction(function () use ($payment, $booking) {
                $payment->update(['payment_status' => 'failed']);
                if ($booking && $booking->booking_status === 'pending') {
                    $booking->update(['booking_status' => 'cancelled']);
                }
            });
        }

        return redirect()->route('staff.walkin.create')->with(
            'status',
            'PayMongo checkout session was cancelled. The reserved slot has been released.'
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
