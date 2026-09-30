<x-admin-layout>
    <x-slot name="heading">Operations Overview</x-slot>

    <div class="gz-panel gz-panel-body mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-block w-2 h-2 rounded-full" style="background: var(--gz-pop);"></span>
                <span class="gz-eyebrow" style="color: var(--gz-pop-dark);">System online · HQ pulse</span>
            </div>
            <h1 class="gz-font-display font-bold text-xl">
                Welcome, {{ Str::before(Auth::user()->name, ' ') }}.
            </h1>
            <p class="text-sm mt-1" style="color: var(--gz-muted);">
                Operational schedule and activity report for {{ $today->format('l, F j, Y') }}.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if(Auth::user()->hasPermission('manage_courts'))
                <a href="{{ route('admin.courts.create') }}" class="gz-btn-primary gz-btn-sm">
                    + Add court
                </a>
            @endif
            @if(Auth::user()->hasPermission('view_finances'))
                <a href="{{ route('admin.finance') }}" class="gz-btn-outline gz-btn-sm">
                    Financial report
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="gz-kpi-card">
            <div class="flex items-center justify-between mb-3">
                <div class="icon-badge" style="width:32px; height:32px; padding:6px;" id="icon-revenue" aria-hidden="true"></div>
                <span class="gz-badge gz-badge-neutral">PHP</span>
            </div>
            <div class="gz-eyebrow mb-1">Month revenue</div>
            <div class="gz-kpi-value" style="color: var(--gz-pop-dark); font-size: 22px;">
                ₱{{ number_format($monthlyRevenue, 2) }}
            </div>
            <p class="text-xs mt-2 font-semibold" style="color: {{ $revenueChange !== null ? ($revenueChange >= 0 ? 'var(--gz-pop-dark)' : 'var(--gz-danger)') : 'var(--gz-muted)' }};">
                @if($revenueChange !== null)
                    {{ $revenueChange >= 0 ? '▲ +' : '▼ ' }}{{ $revenueChange }}% vs last mo
                @else
                    Base month benchmark
                @endif
            </p>
        </div>

        <div class="gz-kpi-card">
            <div class="flex items-center justify-between mb-3">
                <div class="icon-badge" style="width:32px; height:32px; padding:6px;" id="icon-fleet" aria-hidden="true"></div>
                <span class="gz-badge gz-badge-success">Live</span>
            </div>
            <div class="gz-eyebrow mb-1">Court fleet</div>
            <div class="gz-kpi-value" style="font-size: 22px;">
                {{ $courts->where('court_status', 'available')->count() }}
                <span class="text-sm font-normal" style="color: var(--gz-muted);">/ {{ $courts->count() }}</span>
            </div>
            <p class="text-xs mt-2 font-semibold" style="color: var(--gz-pop-dark);">Ready for reservation</p>
        </div>

        <div class="gz-kpi-card">
            <div class="flex items-center justify-between mb-3">
                <div class="icon-badge" style="width:32px; height:32px; padding:6px;" id="icon-matches" aria-hidden="true"></div>
                <span class="gz-badge gz-badge-neutral">{{ $today->format('M j') }}</span>
            </div>
            <div class="gz-eyebrow mb-1">Today matches</div>
            <div class="gz-kpi-value" style="font-size: 22px;">
                {{ $todayBookings->count() }}
            </div>
            <p class="text-xs mt-2" style="color: var(--gz-muted);">{{ $totalBookings }} total ({{ $confirmedBookingsCount }} confirmed)</p>
        </div>

        <div class="gz-kpi-card">
            <div class="flex items-center justify-between mb-3">
                <div class="icon-badge" style="width:32px; height:32px; padding:6px;" id="icon-audits" aria-hidden="true"></div>
                <span class="gz-badge {{ $pendingPayments > 0 ? 'gz-badge-danger' : 'gz-badge-success' }}">
                    {{ $pendingPayments > 0 ? 'Action' : 'Clear' }}
                </span>
            </div>
            <div class="gz-eyebrow mb-1">Pending audits</div>
            <div class="gz-kpi-value" style="font-size: 22px; color: {{ $pendingPayments > 0 ? 'var(--gz-danger)' : 'var(--gz-pop-dark)' }};">
                {{ $pendingPayments }}
            </div>
            <p class="text-xs mt-2" style="color: {{ $pendingPayments > 0 ? 'var(--gz-danger)' : 'var(--gz-muted)' }};">
                {{ $pendingPayments > 0 ? 'Payments need review' : 'All reconciled' }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 gz-panel">
            <div class="gz-panel-header">
                <div>
                    <h2 class="gz-font-display font-bold text-base">Today's Court Run-Sheet</h2>
                    <p class="text-xs" style="color: var(--gz-muted);">
                        Schedule and active court roster for {{ $today->format('D, M d, Y') }}
                    </p>
                </div>
                <a href="{{ route('admin.courts.index') }}" class="gz-link text-xs">Manage courts →</a>
            </div>

            <div class="gz-panel-body overflow-x-auto">
                @if($todayBookings->isEmpty())
                    <div class="p-8 text-center border border-dashed" style="border-color: var(--gz-border); background: var(--gz-surface);">
                        <p class="font-semibold">No matches slated today</p>
                        <p class="text-sm mt-1" style="color: var(--gz-muted);">All courts are clear or open for reservation.</p>
                    </div>
                @else
                    <table class="gz-table">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Court</th>
                                <th>Player</th>
                                <th>Status</th>
                                <th class="text-right">Fee</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todayBookings as $booking)
                                <tr>
                                    <td class="text-sm whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }}
                                        –
                                        {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}
                                    </td>
                                    <td class="font-semibold text-sm">
                                        {{ $booking->court?->court_name ?? 'Court Removed' }}
                                    </td>
                                    <td>
                                        <div class="font-semibold text-sm">{{ $booking->user?->name ?? 'Guest / Player' }}</div>
                                        <div class="text-xs" style="color: var(--gz-muted);">{{ $booking->user?->email }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = match($booking->booking_status) {
                                                'confirmed' => 'gz-badge-success',
                                                'pending' => 'gz-badge-warning',
                                                'cancelled' => 'gz-badge-danger',
                                                default => 'gz-badge-neutral',
                                            };
                                        @endphp
                                        <span class="gz-badge {{ $badgeClass }}">{{ ucfirst($booking->booking_status) }}</span>
                                    </td>
                                    <td class="text-right text-sm font-semibold">
                                        ₱{{ number_format($booking->court?->price_per_hour ?? 0, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <div class="gz-panel flex flex-col justify-between">
            <div>
                <div class="gz-panel-header">
                    <div>
                        <h2 class="gz-font-display font-bold text-base">Court Health</h2>
                        <p class="text-xs" style="color: var(--gz-muted);">Arena fleet status</p>
                    </div>
                    <span class="gz-badge gz-badge-success">● Active</span>
                </div>

                <div class="gz-panel-body space-y-4">
                    @forelse($courts as $court)
                        <div class="gz-kpi-card">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-sm font-semibold">{{ $court->court_name }}</span>
                                @if($court->court_status === 'available')
                                    <span class="gz-badge gz-badge-success">Online</span>
                                @elseif($court->court_status === 'maintenance')
                                    <span class="gz-badge gz-badge-warning">Maint</span>
                                @else
                                    <span class="gz-badge gz-badge-danger">Closed</span>
                                @endif
                            </div>

                            <div class="flex items-center justify-between text-xs mb-2" style="color: var(--gz-muted);">
                                <span>{{ $court->size ?: 'Standard Pickleball' }}</span>
                                <span class="font-semibold">₱{{ number_format($court->price_per_hour, 2) }}/hr</span>
                            </div>

                            <div class="gz-meter-track">
                                <div class="gz-meter-fill" style="width: {{ $court->court_status === 'available' ? '100' : ($court->court_status === 'maintenance' ? '35' : '0') }}%; {{ $court->court_status !== 'available' && $court->court_status !== 'maintenance' ? 'background: var(--gz-danger);' : '' }} {{ $court->court_status === 'maintenance' ? 'background: var(--gz-warning);' : '' }}"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-center py-6" style="color: var(--gz-muted);">No courts registered in fleet.</p>
                    @endforelse
                </div>
            </div>

            <div class="p-4 border-t" style="border-color: var(--gz-border);">
                <a href="{{ route('admin.courts.index') }}" class="gz-btn-outline w-full justify-center text-sm">
                    Configure arena inventory →
                </a>
            </div>
        </div>
    </div>

    <div class="gz-panel">
        <div class="gz-panel-header">
            <div>
                <h2 class="gz-font-display font-bold text-base">Recent Payment Audit Ledger</h2>
                <p class="text-xs" style="color: var(--gz-muted);">Latest transactions processed across all court bookings</p>
            </div>
            @if(Auth::user()->hasPermission('view_finances'))
                <a href="{{ route('admin.finance') }}" class="gz-link text-xs">Expand to full financials →</a>
            @endif
        </div>

        <div class="gz-panel-body overflow-x-auto">
            @if($recentPayments->isEmpty())
                <div class="p-8 text-center border border-dashed" style="border-color: var(--gz-border); background: var(--gz-surface);">
                    <p class="font-semibold">No recent payments logged</p>
                    <p class="text-sm mt-1" style="color: var(--gz-muted);">New completed customer payments will appear here in real time.</p>
                </div>
            @else
                <table class="gz-table">
                    <thead>
                        <tr>
                            <th>Ref No.</th>
                            <th>Player</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentPayments as $payment)
                            <tr>
                                <td class="text-sm">{{ $payment->ref_num ?: 'PAY-#' . $payment->id }}</td>
                                <td class="text-sm font-semibold">{{ $payment->booking?->user?->name ?? 'Guest User' }}</td>
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
    </div>
</x-admin-layout>