<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Court;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $courts = Court::withCount([
            'bookings as active_bookings_count' => fn ($query) => $query
                ->whereIn('booking_status', ['pending', 'confirmed'])
                ->whereDate('date', '>=', today()),
        ])->orderBy('court_name')->get();

        $todayBookings = Booking::with(['court', 'user'])
            ->whereDate('date', today())
            ->orderBy('start_time')
            ->get();

        $paidPayments = Payment::where('payment_status', 'paid');

        $monthlyRevenue = (clone $paidPayments)
            ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->sum('amount');

        $lastMonthRevenue = (clone $paidPayments)
            ->whereBetween('date', [now()->subMonth()->startOfMonth()->toDateString(), now()->subMonth()->endOfMonth()->toDateString()])
            ->sum('amount');

        $revenueChange = $lastMonthRevenue > 0
            ? round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100)
            : null;

        $recentPayments = Payment::with('booking.user')
            ->latest('date')
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'courts' => $courts,
            'todayBookings' => $todayBookings,
            'monthlyRevenue' => $monthlyRevenue,
            'revenueChange' => $revenueChange,
            'pendingPayments' => Payment::where('payment_status', 'pending')->count(),
            'totalBookings' => Booking::count(),
            'recentPayments' => $recentPayments,
            'today' => Carbon::today(),
        ]);
    }
}
