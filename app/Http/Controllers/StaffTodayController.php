<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Court;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class StaffTodayController extends Controller
{
    public function index(Request $request): View
    {
        $today = Carbon::today();

        $query = Booking::with(['court', 'user'])
            ->whereDate('date', $today)
            ->where('booking_status', '!=', 'cancelled')
            ->orderBy('start_time');

        // Filter by court if specified
        if ($request->filled('court_id')) {
            $query->where('court_id', $request->input('court_id'));
        }

        // Filter by attendance status (show, no_show, scheduled)
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'show') {
                $query->where('booking_status', 'show');
            } elseif ($status === 'no_show') {
                $query->whereIn('booking_status', ['no_show', 'no-show']);
            } elseif ($status === 'scheduled') {
                $query->whereNotIn('booking_status', ['show', 'no_show', 'no-show']);
            }
        }

        // Search by customer name, email, or court name
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('court', function ($cq) use ($search) {
                    $cq->where('court_name', 'like', "%{$search}%");
                });
            });
        }

        $bookings = $query->get();

        // Baseline attendance statistics for today
        $todayAll = Booking::whereDate('date', $today)->where('booking_status', '!=', 'cancelled')->get();
        $totalBookingsToday = $todayAll->count();
        $showCount = $todayAll->where('booking_status', 'show')->count();
        $noShowCount = $todayAll->filter(fn ($b) => in_array($b->booking_status, ['no_show', 'no-show']))->count();
        $awaitingCount = max(0, $totalBookingsToday - $showCount - $noShowCount);
        $uniqueCustomersCount = $todayAll->pluck('user_id')->unique()->count();

        $courts = Court::orderBy('court_name')->get();

        return view('staff.today', [
            'today' => $today,
            'bookings' => $bookings,
            'totalBookingsToday' => $totalBookingsToday,
            'showCount' => $showCount,
            'noShowCount' => $noShowCount,
            'awaitingCount' => $awaitingCount,
            'uniqueCustomersCount' => $uniqueCustomersCount,
            'courts' => $courts,
            'selectedCourt' => $request->input('court_id'),
            'selectedStatus' => $request->input('status'),
            'searchTerm' => $request->input('search'),
        ]);
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'attendance_status' => ['required', 'in:show,no_show,scheduled'],
        ]);

        $statusValue = match ($validated['attendance_status']) {
            'show' => 'show',
            'no_show' => 'no_show',
            'scheduled' => 'confirmed',
        };

        $booking->update([
            'booking_status' => $statusValue,
        ]);

        $customerName = $booking->user?->name ?? 'Customer';
        $statusLabel = match ($validated['attendance_status']) {
            'show' => 'SHOW (Present)',
            'no_show' => 'NO-SHOW',
            'scheduled' => 'SCHEDULED (Awaiting)',
        };

        return back()->with('status', "Attendance for {$customerName} marked as {$statusLabel}.");
    }
}
