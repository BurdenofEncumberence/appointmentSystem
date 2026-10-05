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

    {{-- 3 KPI Metric Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        {{-- Metric 1: Month Revenue --}}
        <div class="gz-kpi-card">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background: rgba(62, 207, 126, 0.15); color: var(--gz-pop-dark);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 12v-2m0 0c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="gz-badge gz-badge-success">AUDITED</span>
            </div>
            <div class="gz-eyebrow mb-1">Month revenue</div>
            <div class="gz-kpi-value" style="color: var(--gz-pop-dark); font-size: 24px;">
                ₱{{ number_format($monthlyRevenue, 2) }}
            </div>
            <p class="text-xs mt-2 font-semibold flex items-center gap-1" style="color: {{ $revenueChange !== null ? ($revenueChange >= 0 ? 'var(--gz-pop-dark)' : 'var(--gz-danger)') : 'var(--gz-muted)' }};">
                @if($revenueChange !== null)
                    <span>{{ $revenueChange >= 0 ? '▲ +' : '▼ ' }}{{ $revenueChange }}%</span>
                    <span style="color: var(--gz-muted); font-weight: normal;">vs last mo</span>
                @else
                    Base month benchmark
                @endif
            </p>
        </div>

        {{-- Metric 2: Court Fleet --}}
        <div class="gz-kpi-card">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background: rgba(18, 21, 15, 0.06); color: var(--gz-ink);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM4 12h16M12 4v16" />
                    </svg>
                </div>
                <span class="gz-badge gz-badge-success">Live Fleet</span>
            </div>
            <div class="gz-eyebrow mb-1">Court fleet</div>
            <div class="gz-kpi-value" style="font-size: 24px;">
                {{ $courts->where('court_status', 'available')->count() }}
                <span class="text-sm font-normal" style="color: var(--gz-muted);">/ {{ $courts->count() }}</span>
            </div>
            <p class="text-xs mt-2 font-semibold" style="color: var(--gz-pop-dark);">Ready for reservation</p>
        </div>

        {{-- Metric 3: Today Matches --}}
        <div class="gz-kpi-card">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background: rgba(18, 21, 15, 0.06); color: var(--gz-ink);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <span class="gz-badge gz-badge-neutral">{{ $today->format('M j') }}</span>
            </div>
            <div class="gz-eyebrow mb-1">Today matches</div>
            <div class="gz-kpi-value" style="font-size: 24px;">
                {{ $todayBookings->count() }}
            </div>
            <p class="text-xs mt-2" style="color: var(--gz-muted);">
                <span class="font-bold text-[color:var(--gz-ink)]">{{ $todayOnlineCount }} Online</span> · 
                <span class="font-bold text-[color:var(--red)]">{{ $todayWalkInCount }} Walk-in</span>
            </p>
        </div>
    </div>

    {{-- Channel Breakdown Summary: Online vs Walk-In --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
        {{-- Online Channel --}}
        <div class="gz-panel gz-panel-body" style="border-left: 4px solid var(--gz-border);">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="gz-badge gz-badge-neutral text-xs">🌐 Web Portal</span>
                        <span class="text-xs" style="color: var(--gz-muted);">Self-Service</span>
                    </div>
                    <h3 class="gz-font-display font-bold text-base">Online Reservations</h3>
                </div>
                <div class="text-right">
                    <div class="font-mono font-bold text-2xl text-[color:var(--gz-ink)]">
                        {{ number_format($totalOnlineCount) }}
                    </div>
                    <div class="text-[11px]" style="color: var(--gz-muted);">Total reservations</div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t flex items-center justify-between text-xs" style="border-color: var(--gz-border);">
                <div>
                    <span style="color: var(--gz-muted);">Today:</span>
                    <span class="font-bold font-mono ml-1">{{ $todayOnlineCount }} sessions</span>
                </div>
                <div>
                    <span style="color: var(--gz-muted);">This Month:</span>
                    <span class="font-bold font-mono ml-1">{{ $monthOnlineCount }}</span>
                </div>
                <div>
                    <span style="color: var(--gz-muted);">Collections:</span>
                    <span class="font-bold font-mono ml-1" style="color: var(--gz-pop-dark);">₱{{ number_format($onlineRevenue, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Walk-In Channel --}}
        <div class="gz-panel gz-panel-body" style="border-left: 4px solid var(--red);">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="gz-badge text-xs" style="background: rgba(179,38,30,0.12); color: var(--red); border: 1px solid var(--red);">🚶 Front Desk</span>
                        <span class="text-xs" style="color: var(--gz-muted);">On-the-spot</span>
                    </div>
                    <h3 class="gz-font-display font-bold text-base">Walk-In Reservations</h3>
                </div>
                <div class="text-right">
                    <div class="font-mono font-bold text-2xl" style="color: var(--red);">
                        {{ number_format($totalWalkInCount) }}
                    </div>
                    <div class="text-[11px]" style="color: var(--gz-muted);">Total reservations</div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t flex items-center justify-between text-xs" style="border-color: var(--gz-border);">
                <div>
                    <span style="color: var(--gz-muted);">Today:</span>
                    <span class="font-bold font-mono ml-1">{{ $todayWalkInCount }} sessions</span>
                </div>
                <div>
                    <span style="color: var(--gz-muted);">This Month:</span>
                    <span class="font-bold font-mono ml-1">{{ $monthWalkInCount }}</span>
                </div>
                <div>
                    <span style="color: var(--gz-muted);">Collections:</span>
                    <span class="font-bold font-mono ml-1" style="color: var(--red);">₱{{ number_format($walkInRevenue, 2) }}</span>
                </div>
            </div>
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
                                <th>Channel</th>
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
                                    <td>
                                        @if($booking->isWalkIn())
                                            <span class="gz-badge text-[10px]" style="background: rgba(179,38,30,0.12); color: var(--red); border: 1px solid var(--red);">🚶 Walk-In</span>
                                        @else
                                            <span class="gz-badge gz-badge-neutral text-[10px]">🌐 Online</span>
                                        @endif
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
                                                'show' => 'gz-badge-success',
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
                            <th>Channel</th>
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
                                <td>
                                    @if($payment->booking?->isWalkIn())
                                        <span class="gz-badge text-[10px]" style="background: rgba(179,38,30,0.12); color: var(--red); border: 1px solid var(--red);">🚶 Walk-In</span>
                                    @else
                                        <span class="gz-badge gz-badge-neutral text-[10px]">🌐 Online</span>
                                    @endif
                                </td>
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