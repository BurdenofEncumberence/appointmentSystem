<x-admin-layout>
    <x-slot name="heading">Financial Ledger</x-slot>

    <div class="gz-panel gz-panel-body mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="gz-eyebrow">Financial audit & reporting</span>
            <h1 class="gz-font-display font-bold text-xl mt-1">Settlements & Cashflow Archive</h1>
            <p class="text-sm mt-1" style="color: var(--gz-muted);">
                Historical records, monthly revenue performance, and payment breakdown.
            </p>
        </div>
        <div>
            <span class="gz-badge-outline font-mono text-xs">
                YEAR {{ now()->format('Y') }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="gz-kpi-card">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background: rgba(62, 207, 126, 0.15); color: var(--gz-pop-dark);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 12v-2m0 0c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="gz-badge gz-badge-success">Paid</span>
            </div>
            <div class="gz-eyebrow mb-1">This month</div>
            <div class="gz-kpi-value" style="color: var(--gz-pop-dark); font-size: 24px;">
                ₱{{ number_format($monthlyRevenue, 2) }}
            </div>
            <p class="text-xs mt-2" style="color: var(--gz-muted);">{{ now()->format('F Y') }} audited</p>
        </div>

        <div class="gz-kpi-card">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background: rgba(18, 21, 15, 0.06); color: var(--gz-ink);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <span class="gz-badge gz-badge-neutral">Cumulative</span>
            </div>
            <div class="gz-eyebrow mb-1">Year to date</div>
            <div class="gz-kpi-value" style="font-size: 24px;">
                ₱{{ number_format($yearRevenue, 2) }}
            </div>
            <p class="text-xs mt-2" style="color: var(--gz-muted);">All settled {{ now()->format('Y') }}</p>
        </div>

        <div class="gz-kpi-card">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background: rgba(18, 21, 15, 0.06); color: var(--gz-ink);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <span class="gz-badge gz-badge-neutral">Count</span>
            </div>
            <div class="gz-eyebrow mb-1">Transactions</div>
            <div class="gz-kpi-value" style="font-size: 24px;">
                {{ number_format($transactionCount) }}
            </div>
            <p class="text-xs mt-2" style="color: var(--gz-muted);">Confirmed paid receipts</p>
        </div>
    </div>

    <!-- Channel Breakdown: Online vs Walk-In -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        <div class="gz-kpi-card" style="border-left: 4px solid var(--gz-pop-dark);">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-base" style="background: rgba(62, 207, 126, 0.15); color: var(--gz-pop-dark);">
                        🌐
                    </div>
                    <div>
                        <div class="gz-eyebrow">Online Channel</div>
                        <div class="text-sm font-bold">Online Reservations</div>
                    </div>
                </div>
                <span class="gz-badge gz-badge-neutral">{{ $monthOnlineCount }} this month</span>
            </div>
            <div class="grid grid-cols-2 gap-4 pt-2 border-t" style="border-color: var(--gz-border);">
                <div>
                    <div class="text-xs" style="color: var(--gz-muted);">Total Bookings</div>
                    <div class="text-xl font-bold mt-0.5">{{ number_format($totalOnlineCount) }}</div>
                </div>
                <div>
                    <div class="text-xs" style="color: var(--gz-muted);">Audited Collections</div>
                    <div class="text-xl font-bold mt-0.5" style="color: var(--gz-pop-dark);">₱{{ number_format($onlineRevenue, 2) }}</div>
                    <div class="text-[10px]" style="color: var(--gz-muted);">{{ number_format($onlinePaidTransactions) }} receipts</div>
                </div>
            </div>
        </div>

        <div class="gz-kpi-card" style="border-left: 4px solid var(--red);">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-base" style="background: rgba(179,38,30,0.12); color: var(--red);">
                        🚶
                    </div>
                    <div>
                        <div class="gz-eyebrow" style="color: var(--red);">On-Site Desk</div>
                        <div class="text-sm font-bold">Walk-In Reservations</div>
                    </div>
                </div>
                <span class="gz-badge" style="background: rgba(179,38,30,0.12); color: var(--red); border: 1px solid var(--red);">{{ $monthWalkInCount }} this month</span>
            </div>
            <div class="grid grid-cols-2 gap-4 pt-2 border-t" style="border-color: var(--gz-border);">
                <div>
                    <div class="text-xs" style="color: var(--gz-muted);">Total Bookings</div>
                    <div class="text-xl font-bold mt-0.5">{{ number_format($totalWalkInCount) }}</div>
                </div>
                <div>
                    <div class="text-xs" style="color: var(--gz-muted);">Audited Collections</div>
                    <div class="text-xl font-bold mt-0.5" style="color: var(--red);">₱{{ number_format($walkInRevenue, 2) }}</div>
                    <div class="text-[10px]" style="color: var(--gz-muted);">{{ number_format($walkInPaidTransactions) }} receipts</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 gz-panel">
            <div class="gz-panel-header">
                <div>
                    <h2 class="gz-font-display font-bold text-base">Revenue Trend</h2>
                    <p class="text-xs" style="color: var(--gz-muted);">Last 6 months of historical collections</p>
                </div>
                <span class="gz-badge gz-badge-success">PHP</span>
            </div>

            <div class="gz-panel-body">
                @php
                    $maxRevenue = max($monthlyTotals->max('amount'), 1);
                @endphp
                <div class="flex items-end justify-between gap-3 sm:gap-6 h-56 pt-6 pb-2 border-b" style="border-color: var(--gz-border);">
                    @foreach($monthlyTotals as $month)
                        @php
                            $heightPercent = max(($month['amount'] / $maxRevenue) * 100, $month['amount'] > 0 ? 10 : 3);
                        @endphp
                        <div class="flex-1 flex flex-col items-center h-full justify-end">
                            <div class="text-xs mb-1 text-center whitespace-nowrap" style="color: var(--gz-muted);">
                                ₱{{ number_format($month['amount'], 0) }}
                            </div>

                            <div class="gz-bar-track"
                                 style="height: {{ $heightPercent }}%;"
                                 title="{{ $month['label'] }}: ₱{{ number_format($month['amount'], 2) }}">
                                <div class="gz-bar-fill w-full h-full"></div>
                            </div>

                            <div class="mt-2 text-xs font-semibold text-center">
                                {{ $month['label'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3 flex items-center justify-between text-xs" style="color: var(--gz-muted);">
                    <span>Excludes unconfirmed / cancelled slots</span>
                    <span style="color: var(--gz-pop-dark);">● Paid revenue</span>
                </div>
            </div>
        </div>

        <div class="gz-panel flex flex-col justify-between">
            <div>
                <div class="gz-panel-header">
                    <div>
                        <h2 class="gz-font-display font-bold text-base">Payment Mix</h2>
                        <p class="text-xs" style="color: var(--gz-muted);">By payment method</p>
                    </div>
                    <span class="gz-badge gz-badge-neutral">Share</span>
                </div>

                <div class="gz-panel-body space-y-4">
                    @forelse($methodTotals as $method)
                        @php
                            $maxTotal = max($methodTotals->max('total'), 1);
                            $percentage = round(($method->total / $maxTotal) * 100);
                        @endphp
                        <div class="gz-kpi-card">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-semibold">{{ $method->payment_method ?: 'Direct' }}</span>
                                <span class="text-sm font-bold" style="color: var(--gz-pop-dark);">
                                    ₱{{ number_format($method->total, 2) }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-xs mb-2" style="color: var(--gz-muted);">
                                <span>{{ $method->transactions }} transactions</span>
                                <span>{{ $percentage }}% of max</span>
                            </div>

                            <div class="gz-meter-track">
                                <div class="gz-meter-fill" style="width: {{ $percentage }}%;"></div>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center">
                            <p class="text-sm" style="color: var(--gz-muted);">No payment records to compile mix.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="p-4 text-center border-t" style="border-color: var(--gz-border);">
                <p class="text-xs" style="color: var(--gz-muted);">KYMNET financial verification system</p>
            </div>
        </div>
    </div>

    <div class="gz-panel">
        <div class="gz-panel-header">
            <div>
                <h2 class="gz-font-display font-bold text-base">Master Payment Ledger</h2>
                <p class="text-xs" style="color: var(--gz-muted);">Comprehensive audit trail of all recorded player transactions</p>
            </div>
            <span class="gz-badge gz-badge-neutral">{{ $payments->total() }} total entries</span>
        </div>

        <div class="gz-panel-body overflow-x-auto">
            @if($payments->isEmpty())
                <div class="p-8 text-center border border-dashed" style="border-color: var(--gz-border); background: var(--gz-surface);">
                    <p class="font-semibold">No transactions in ledger</p>
                    <p class="text-sm mt-1" style="color: var(--gz-muted);">Paid bookings will automatically append to this ledger.</p>
                </div>
            @else
                <table class="gz-table">
                    <thead>
                        <tr>
                            <th>Ref No.</th>
                            <th>Player</th>
                            <th>Channel</th>
                            <th>Method</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td class="text-sm">{{ $payment->ref_num ?: 'PAY-#' . $payment->id }}</td>
                                <td>
                                    <div class="font-semibold text-sm">{{ $payment->booking?->user?->name ?? 'Guest User' }}</div>
                                    <div class="text-xs" style="color: var(--gz-muted);">{{ $payment->booking?->user?->email }}</div>
                                </td>
                                <td>
                                    @if($payment->booking?->isWalkIn())
                                        <span class="gz-badge text-[10px]" style="background: rgba(179,38,30,0.12); color: var(--red); border: 1px solid var(--red);">🚶 Walk-In</span>
                                    @else
                                        <span class="gz-badge gz-badge-neutral text-[10px]">🌐 Online</span>
                                    @endif
                                </td>
                                <td class="text-sm font-medium capitalize">{{ $payment->payment_method ?: 'Standard' }}</td>
                                <td class="text-sm" style="color: var(--gz-muted);">
                                    {{ \Carbon\Carbon::parse($payment->date)->format('M d, Y') }}
                                </td>
                                <td>
                                    <span class="gz-badge {{ $payment->payment_status === 'paid' ? 'gz-badge-success' : 'gz-badge-warning' }}">
                                        {{ ucfirst($payment->payment_status) }}
                                    </span>
                                </td>
                                <td class="text-right font-semibold" style="color: var(--gz-pop-dark);">
                                    ₱{{ number_format($payment->amount, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        @if($payments->hasPages())
            <div class="p-4 border-t flex justify-between items-center" style="border-color: var(--gz-border);">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>