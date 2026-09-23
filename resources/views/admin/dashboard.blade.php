<x-admin-layout>
    <x-slot name="heading">Operations Overview</x-slot>

    {{-- Top Action & Pulse Bar --}}
    <div class="pixel-border p-4 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4" style="background: var(--cream);">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-block w-2.5 h-2.5" style="background: var(--jade);"></span>
                <span class="font-pixel text-[10px] uppercase" style="color: var(--jade);">SYSTEM ONLINE · HQ PULSE</span>
            </div>
            <p class="font-pixel text-xs" style="color: var(--ink);">
                WELCOME, {{ strtoupper(Str::before(Auth::user()->name, ' ')) }}.
            </p>
            <p class="text-base text-stone-600 mt-1">
                Operational schedule and activity report for {{ $today->format('l, F j, Y') }}.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if(Auth::user()->hasPermission('manage_courts'))
                <a href="{{ route('admin.courts.create') }}" class="pixel-btn text-[10px] py-2 px-3" style="background: var(--jade); color: var(--cream);">
                    + ADD COURT
                </a>
            @endif
            @if(Auth::user()->hasPermission('view_finances'))
                <a href="{{ route('admin.finance') }}" class="pixel-btn text-[10px] py-2 px-3" style="background: var(--gold); color: var(--ink);">
                    FINANCIAL REPORT
                </a>
            @endif
        </div>
    </div>

    {{-- 4 KPI Metric Ledgers --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Metric 1: Month Revenue --}}
        <div class="pixel-border p-4 flex flex-col justify-between" style="background: var(--cream);">
            <div class="flex items-center justify-between border-b-2 pb-2" style="border-color: var(--ink);">
                <span class="font-pixel text-[9px] uppercase tracking-wider" style="color: var(--ink);">MONTH REVENUE</span>
                <span class="font-pixel text-[8px] px-1.5 py-0.5" style="background: var(--ink); color: var(--gold);">PHP</span>
            </div>
            <div class="my-4">
                <div class="font-pixel text-xl sm:text-2xl" style="color: var(--jade);">
                    ₱{{ number_format($monthlyRevenue, 2) }}
                </div>
            </div>
            <div class="text-sm font-bold pt-2 border-t" style="border-color: rgba(26,22,17,0.15);">
                @if($revenueChange !== null)
                    <span class="font-pixel text-[9px] {{ $revenueChange >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                        {{ $revenueChange >= 0 ? '▲ +' : '▼ ' }}{{ $revenueChange }}% VS LAST MO
                    </span>
                @else
                    <span class="font-pixel text-[8px] text-stone-500">BASE MONTH BENCHMARK</span>
                @endif
            </div>
        </div>

        {{-- Metric 2: Court Fleet --}}
        <div class="pixel-border p-4 flex flex-col justify-between" style="background: var(--cream);">
            <div class="flex items-center justify-between border-b-2 pb-2" style="border-color: var(--ink);">
                <span class="font-pixel text-[9px] uppercase tracking-wider" style="color: var(--ink);">COURT FLEET</span>
                <span class="font-pixel text-[8px] px-1.5 py-0.5" style="background: var(--jade); color: var(--cream);">LIVE</span>
            </div>
            <div class="my-4">
                <div class="font-pixel text-xl sm:text-2xl" style="color: var(--ink);">
                    {{ $courts->where('court_status', 'available')->count() }}
                    <span class="text-sm text-stone-500">/ {{ $courts->count() }}</span>
                </div>
            </div>
            <div class="text-sm font-bold pt-2 border-t" style="border-color: rgba(26,22,17,0.15);">
                <span class="font-pixel text-[8px]" style="color: var(--jade);">READY FOR RESERVATION</span>
            </div>
        </div>

        {{-- Metric 3: Today's Bookings --}}
        <div class="pixel-border p-4 flex flex-col justify-between" style="background: var(--cream);">
            <div class="flex items-center justify-between border-b-2 pb-2" style="border-color: var(--ink);">
                <span class="font-pixel text-[9px] uppercase tracking-wider" style="color: var(--ink);">TODAY MATCHES</span>
                <span class="font-pixel text-[8px] px-1.5 py-0.5" style="background: var(--gold); color: var(--ink);">{{ $today->format('M j') }}</span>
            </div>
            <div class="my-4">
                <div class="font-pixel text-xl sm:text-2xl" style="color: var(--ink);">
                    {{ $todayBookings->count() }}
                </div>
            </div>
            <div class="text-sm font-bold pt-2 border-t" style="border-color: rgba(26,22,17,0.15);">
                <span class="font-pixel text-[8px] text-stone-600">{{ $totalBookings }} TOTAL ({{ $confirmedBookingsCount }} CONFIRMED)</span>
            </div>
        </div>

        {{-- Metric 4: Pending Audits --}}
        <div class="pixel-border p-4 flex flex-col justify-between" style="background: var(--cream);">
            <div class="flex items-center justify-between border-b-2 pb-2" style="border-color: var(--ink);">
                <span class="font-pixel text-[9px] uppercase tracking-wider" style="color: var(--ink);">PENDING AUDITS</span>
                <span class="font-pixel text-[8px] px-1.5 py-0.5" style="background: var(--red); color: var(--cream);">ACTION</span>
            </div>
            <div class="my-4">
                <div class="font-pixel text-xl sm:text-2xl" style="color: {{ $pendingPayments > 0 ? 'var(--red)' : 'var(--jade)' }};">
                    {{ $pendingPayments }}
                </div>
            </div>
            <div class="text-sm font-bold pt-2 border-t" style="border-color: rgba(26,22,17,0.15);">
                <span class="font-pixel text-[8px] {{ $pendingPayments > 0 ? 'text-red-700' : 'text-stone-500' }}">
                    {{ $pendingPayments > 0 ? 'PAYMENTS NEED REVIEW' : 'ALL RECONCILED' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Main Operations Split View --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Run-sheet for Today (2 Columns) --}}
        <div class="lg:col-span-2 pixel-border" style="background: var(--cream);">
            <div class="p-4 border-b-2 flex items-center justify-between flex-wrap gap-2" style="background: var(--ink); color: var(--cream); border-color: var(--ink);">
                <div>
                    <h2 class="font-pixel text-xs tracking-wider text-[color:var(--gold)]">
                        TODAY'S COURT RUN-SHEET
                    </h2>
                    <p class="text-sm text-stone-300 mt-0.5">
                        Schedule and active court roster for {{ $today->format('D, M d, Y') }}
                </div>
                <a href="{{ route('admin.courts.index') }}" class="font-pixel text-[9px] text-[color:var(--gold)] underline hover:text-[color:var(--cream)]">
                    [MANAGE COURTS]
                </a>
            </div>

            <div class="p-4 overflow-x-auto">
                @if($todayBookings->isEmpty())
                    <div class="p-8 text-center border-2 border-dashed" style="border-color: var(--ink);">
                        <p class="font-pixel text-xs" style="color: var(--ink);">NO MATCHES SLATED TODAY</p>
                        <p class="text-base text-stone-600 mt-2">All courts are clear or open for reservation.</p>
                    </div>
                @else
                    <table class="w-full text-base border-collapse">
                        <thead>
                            <tr class="border-b-2" style="border-color: var(--ink); background: var(--parchment);">
                                <th class="p-2.5 text-left font-pixel text-[9px]">TIME</th>
                                <th class="p-2.5 text-left font-pixel text-[9px]">COURT</th>
                                <th class="p-2.5 text-left font-pixel text-[9px]">PLAYER</th>
                                <th class="p-2.5 text-left font-pixel text-[9px]">STATUS</th>
                                <th class="p-2.5 text-right font-pixel text-[9px]">FEE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todayBookings as $booking)
                                <tr class="border-b" style="border-color: rgba(26,22,17,0.2);">
                                    <td class="p-2.5 font-pixel text-[9px] whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }}
                                        -
                                        {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}
                                    </td>
                                    <td class="p-2.5 font-bold">
                                        {{ $booking->court?->court_name ?? 'Court Removed' }}
                                    </td>
                                    <td class="p-2.5">
                                        <div class="font-bold text-base leading-tight">{{ $booking->user?->name ?? 'Guest / Player' }}</div>
                                        <div class="text-xs text-stone-500">{{ $booking->user?->email }}</div>
                                    </td>
                                    <td class="p-2.5">
                                        @php
                                            $badgeBg = match($booking->booking_status) {
                                                'confirmed' => 'var(--jade)',
                                                'pending' => 'var(--gold)',
                                                'cancelled' => 'var(--red)',
                                                default => 'var(--ink)'
                                            };
                                            $badgeText = match($booking->booking_status) {
                                                'pending' => 'var(--ink)',
                                                default => 'var(--cream)'
                                            };
                                        @endphp
                                        <span class="font-pixel text-[8px] px-2 py-0.5 inline-block pixel-border" style="background: {{ $badgeBg }}; color: {{ $badgeText }};">
                                            {{ strtoupper($booking->booking_status) }}
                                        </span>
                                    </td>
                                    <td class="p-2.5 text-right font-pixel text-[9px]">
                                        ₱{{ number_format($booking->court?->price_per_hour ?? 0, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        {{-- Court Fleet Health Panel (1 Column) --}}
        <div class="pixel-border flex flex-col justify-between" style="background: var(--cream);">
            <div>
                <div class="p-4 border-b-2 flex items-center justify-between" style="background: var(--ink); color: var(--cream); border-color: var(--ink);">
                    <div>
                        <h2 class="font-pixel text-xs tracking-wider text-[color:var(--gold)]">
                            COURT HEALTH
                        </h2>
                        <p class="text-sm text-stone-300 mt-0.5">Arena fleet status</p>
                    </div>
                    <span class="font-pixel text-[9px] px-1.5 py-0.5" style="background: var(--jade); color: var(--cream);">
                        ● ACTIVE
                    </span>
                </div>

                <div class="p-4 space-y-4">
                    @forelse($courts as $court)
                        <div class="p-3 border-2" style="border-color: var(--ink); background: var(--parchment);">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="font-pixel text-[10px]" style="color: var(--ink);">
                                    {{ $court->court_name }}
                                </span>
                                @if($court->court_status === 'available')
                                    <span class="font-pixel text-[8px] px-1.5 py-0.5" style="background: var(--jade); color: var(--cream);">
                                        ONLINE
                                    </span>
                                @elseif($court->court_status === 'maintenance')
                                    <span class="font-pixel text-[8px] px-1.5 py-0.5" style="background: var(--gold); color: var(--ink);">
                                        MAINT
                                    </span>
                                @else
                                    <span class="font-pixel text-[8px] px-1.5 py-0.5" style="background: var(--red); color: var(--cream);">
                                        CLOSED
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center justify-between text-sm text-stone-700 mb-2">
                                <span>{{ $court->size ?: 'Standard Pickleball' }}</span>
                                <span class="font-bold">₱{{ number_format($court->price_per_hour, 2) }}/hr</span>
                            </div>

                            {{-- Retro Segmented Meter --}}
                            <div class="w-full h-3 border border-[color:var(--ink)] p-0.5 flex gap-1" style="background: var(--cream);">
                                @for($i = 0; $i < 6; $i++)
                                    <div class="h-full flex-1" style="background: {{ $court->court_status === 'available' ? 'var(--jade)' : ($court->court_status === 'maintenance' ? 'var(--gold)' : 'var(--red)') }}; opacity: {{ $court->court_status === 'available' ? '1' : ($i < 2 ? '1' : '0.2') }};"></div>
                                @endfor
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-stone-500 font-pixel text-center py-6">NO COURTS REGISTERED IN FLEET.</p>
                    @endforelse
                </div>
            </div>

            <div class="p-4 border-t-2" style="border-color: var(--ink); background: var(--parchment);">
                <a href="{{ route('admin.courts.index') }}" class="pixel-btn block text-center text-[9px] py-2" style="background: var(--ink); color: var(--cream);">
                    CONFIGURE ARENA INVENTORY →
                </a>
            </div>
        </div>
    </div>

    {{-- Recent Payments Ledger --}}
    <div class="pixel-border" style="background: var(--cream);">
        <div class="p-4 border-b-2 flex items-center justify-between flex-wrap gap-2" style="background: var(--ink); color: var(--cream); border-color: var(--ink);">
            <div>
                <h2 class="font-pixel text-xs tracking-wider text-[color:var(--gold)]">
                    RECENT PAYMENT AUDIT LEDGER
                </h2>
                <p class="text-sm text-stone-300 mt-0.5">Latest transactions processed across all court bookings</p>
            </div>
            @if(Auth::user()->hasPermission('view_finances'))
                <a href="{{ route('admin.finance') }}" class="font-pixel text-[9px] text-[color:var(--gold)] underline hover:text-[color:var(--cream)]">
                    [EXPAND TO FULL FINANCIALS]
                </a>
            @endif
        </div>

        <div class="p-4 overflow-x-auto">
            @if($recentPayments->isEmpty())
                <div class="p-8 text-center border-2 border-dashed" style="border-color: var(--ink);">
                    <p class="font-pixel text-xs" style="color: var(--ink);">NO RECENT PAYMENTS LOGGED</p>
                    <p class="text-base text-stone-600 mt-2">New completed customer payments will appear here in real time.</p>
                </div>
            @else
                <table class="w-full text-base border-collapse">
                    <thead>
                        <tr class="border-b-2" style="border-color: var(--ink); background: var(--parchment);">
                            <th class="p-2.5 text-left font-pixel text-[9px]">REF NO.</th>
                            <th class="p-2.5 text-left font-pixel text-[9px]">PLAYER</th>
                            <th class="p-2.5 text-left font-pixel text-[9px]">DATE</th>
                            <th class="p-2.5 text-left font-pixel text-[9px]">STATUS</th>
                            <th class="p-2.5 text-right font-pixel text-[9px]">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentPayments as $payment)
                            <tr class="border-b" style="border-color: rgba(26,22,17,0.2);">
                                <td class="p-2.5 font-pixel text-[9px]" style="color: var(--ink);">
                                    {{ $payment->ref_num ?: 'PAY-#' . $payment->id }}
                                </td>
                                <td class="p-2.5">
                                    <span class="font-bold">{{ $payment->booking?->user?->name ?? 'Guest User' }}</span>
                                </td>
                                <td class="p-2.5 text-stone-600">
                                    {{ \Carbon\Carbon::parse($payment->date)->format('M d, Y') }}
                                </td>
                                <td class="p-2.5">
                                    <span class="font-pixel text-[8px] px-2 py-0.5 inline-block pixel-border" style="background: {{ $payment->payment_status === 'paid' ? 'var(--jade)' : 'var(--gold)' }}; color: {{ $payment->payment_status === 'paid' ? 'var(--cream)' : 'var(--ink)' }};">
                                        {{ strtoupper($payment->payment_status) }}
                                    </span>
                                </td>
                                <td class="p-2.5 text-right font-pixel text-[10px]" style="color: var(--jade);">
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
