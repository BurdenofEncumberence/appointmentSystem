<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AdminFinanceController extends Controller
{
    public function index(): View
    {
        $paidPayments = Payment::where('payment_status', 'paid');
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $monthlyRevenue = (clone $paidPayments)->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])->sum('amount');
        $yearRevenue = (clone $paidPayments)->whereYear('date', now()->year)->sum('amount');

        $monthlyTotals = collect(range(5, 0))->map(function (int $monthsAgo) use ($paidPayments): array {
            $month = now()->subMonths($monthsAgo);

            return [
                'label' => $month->format('M'),
                'amount' => (float) (clone $paidPayments)->whereYear('date', $month->year)->whereMonth('date', $month->month)->sum('amount'),
            ];
        });

        $methodTotals = (clone $paidPayments)
            ->selectRaw('payment_method, SUM(amount) as total, COUNT(*) as transactions')
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get();

        return view('admin.finance', [
            'monthlyRevenue' => $monthlyRevenue,
            'yearRevenue' => $yearRevenue,
            'transactionCount' => (clone $paidPayments)->count(),
            'monthlyTotals' => $monthlyTotals,
            'methodTotals' => $methodTotals,
            'payments' => Payment::with('booking.user')->latest('date')->latest('created_at')->paginate(10),
            'today' => Carbon::today(),
        ]);
    }
}
