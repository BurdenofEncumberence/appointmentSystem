<x-admin-layout>
    <x-slot name="heading">Financial Ledger</x-slot>

    {{-- Header Banner --}}
    <div class="pixel-border p-4 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4" style="background: var(--cream);">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-block w-2.5 h-2.5" style="background: var(--gold);"></span>
                <span class="font-pixel text-[10px] uppercase" style="color: var(--ink);">FINANCIAL AUDIT & REPORTING</span>
            </div>
            <p class="font-pixel text-xs" style="color: var(--ink);">
                SETTLEMENTS & CASHFLOW ARCHIVE
            </p>
            <p class="text-base text-stone-600 mt-1">
                Historical records, monthly revenue performance, and payment breakdown.
            </p>
        </div>
        <div>
            <span class="font-pixel text-[9px] px-3 py-1.5 pixel-border" style="background: var(--parchment); color: var(--ink);">
                FISCAL YEAR {{ now()->format('Y') }}
            </span>
        </div>
    </div>

    {{-- 3 KPI Financial Metric Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        {{-- Card 1: This Month --}}
        <div class="pixel-border p-4 flex flex-col justify-between" style="background: var(--cream);">
            <div class="flex items-center justify-between border-b-2 pb-2" style="border-color: var(--ink);">
                <span class="font-pixel text-[9px] uppercase tracking-wider" style="color: var(--ink);">THIS MONTH</span>
                <span class="font-pixel text-[8px] px-1.5 py-0.5" style="background: var(--jade); color: var(--cream);">PAID</span>
            </div>
            <div class="my-4">
                <div class="font-pixel text-2xl" style="color: var(--jade);">
                    ₱{{ number_format($monthlyRevenue, 2) }}
                </div>
            </div>
            <div class="pt-2 border-t text-sm font-bold" style="border-color: rgba(26,22,17,0.15);">
                <span class="font-pixel text-[8px] text-stone-600">{{ now()->format('F Y') }} AUDITED</span>
            </div>
        </div>

        {{-- Card 2: Year to Date --}}
        <div class="pixel-border p-4 flex flex-col justify-between" style="background: var(--cream);">
            <div class="flex items-center justify-between border-b-2 pb-2" style="border-color: var(--ink);">
                <span class="font-pixel text-[9px] uppercase tracking-wider" style="color: var(--ink);">YEAR TO DATE</span>
                <span class="font-pixel text-[8px] px-1.5 py-0.5" style="background: var(--gold); color: var(--ink);">CUMULATIVE</span>
            </div>
            <div class="my-4">
                <div class="font-pixel text-2xl" style="color: var(--ink);">
                    ₱{{ number_format($yearRevenue, 2) }}
                </div>
            </div>
            <div class="pt-2 border-t text-sm font-bold" style="border-color: rgba(26,22,17,0.15);">
                <span class="font-pixel text-[8px] text-stone-600">ALL SETTLED {{ now()->format('Y') }}</span>
            </div>
        </div>

        {{-- Card 3: Completed Transactions --}}
        <div class="pixel-border p-4 flex flex-col justify-between" style="background: var(--cream);">
            <div class="flex items-center justify-between border-b-2 pb-2" style="border-color: var(--ink);">
                <span class="font-pixel text-[9px] uppercase tracking-wider" style="color: var(--ink);">TRANSACTIONS</span>
                <span class="font-pixel text-[8px] px-1.5 py-0.5" style="background: var(--ink); color: var(--cream);">COUNT</span>
            </div>
            <div class="my-4">
                <div class="font-pixel text-2xl" style="color: var(--ink);">
                    {{ number_format($transactionCount) }}
                </div>
            </div>
            <div class="pt-2 border-t text-sm font-bold" style="border-color: rgba(26,22,17,0.15);">
                <span class="font-pixel text-[8px] text-stone-600">CONFIRMED PAID RECEIPTS</span>
            </div>
        </div>
    </div>

    {{-- Charts and Distribution Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- 6-Month Retro Pixel Bar Chart (2 Cols) --}}
        <div class="lg:col-span-2 pixel-border" style="background: var(--cream);">
            <div class="p-4 border-b-2 flex items-center justify-between" style="background: var(--ink); color: var(--cream); border-color: var(--ink);">
                <div>
                    <h2 class="font-pixel text-xs tracking-wider text-[color:var(--gold)]">
                        REVENUE TREND (LAST 6 MONTHS)
                    </h2>
                    <p class="text-sm text-stone-300 mt-0.5">Historical monthly collections</p>
                </div>
                <span class="font-pixel text-[8px] px-2 py-0.5" style="background: var(--jade); color: var(--cream);">
                    PHP
                </span>
            </div>

            <div class="p-6">
                @php 
                    $maxRevenue = max($monthlyTotals->max('amount'), 1); 
                @endphp
                <div class="flex items-end justify-between gap-3 sm:gap-6 h-56 pt-6 pb-2 border-b-2" style="border-color: var(--ink);">
                    @foreach($monthlyTotals as $month)
                        @php
                            $heightPercent = max(($month['amount'] / $maxRevenue) * 100, $month['amount'] > 0 ? 10 : 3);
                        @endphp
                        <div class="flex-1 flex flex-col items-center h-full justify-end group">
                            {{-- Value Tag on top of bar --}}
                            <div class="font-pixel text-[7px] sm:text-[8px] mb-1 text-center whitespace-nowrap text-stone-600">
                                ₱{{ number_format($month['amount'], 0) }}
                            </div>

                            {{-- Retro Blocky Bar --}}
                            <div class="w-full max-w-[48px] border-2 border-[color:var(--ink)] flex flex-col justify-end transition-all"
                                 style="height: {{ $heightPercent }}%; background: var(--parchment);"
                                 title="{{ $month['label'] }}: ₱{{ number_format($month['amount'], 2) }}">
                                <div class="w-full h-full" style="background: var(--jade); opacity: 0.85;"></div>
                            </div>

                            {{-- Month Label --}}
                            <div class="mt-2 font-pixel text-[8px] sm:text-[9px] uppercase tracking-wider text-center" style="color: var(--ink);">
                                {{ $month['label'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3 flex items-center justify-between text-xs text-stone-500 font-pixel">
                    <span>* EXCLUDES UNCONFIRMED / CANCELLED SLOTS</span>
                    <span style="color: var(--jade);">■ PAID REVENUE</span>
                </div>
            </div>
        </div>

        {{-- Payment Mix Panel (1 Col) --}}
        <div class="pixel-border flex flex-col justify-between" style="background: var(--cream);">
            <div>
                <div class="p-4 border-b-2 flex items-center justify-between" style="background: var(--ink); color: var(--cream); border-color: var(--ink);">
                    <div>
                        <h2 class="font-pixel text-xs tracking-wider text-[color:var(--gold)]">
                            PAYMENT MIX
                        </h2>
                        <p class="text-sm text-stone-300 mt-0.5">By payment method</p>
                    </div>
                    <span class="font-pixel text-[8px] px-1.5 py-0.5" style="background: var(--parchment); color: var(--ink);">
                        SHARE
                    </span>
                </div>

                <div class="p-4 space-y-4">
                    @forelse($methodTotals as $method)
                        @php
                            $maxTotal = max($methodTotals->max('total'), 1);
                            $percentage = round(($method->total / $maxTotal) * 100);
                        @endphp
                        <div class="p-3 border-2" style="border-color: var(--ink); background: var(--parchment);">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-pixel text-[10px] uppercase" style="color: var(--ink);">
                                    {{ $method->payment_method ?: 'Direct' }}
                                </span>
                                <span class="font-pixel text-[9px]" style="color: var(--jade);">
                                    ₱{{ number_format($method->total, 2) }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-xs text-stone-600 mb-2">
                                <span>{{ $method->transactions }} transactions</span>
                                <span>{{ $percentage }}% of max</span>
                            </div>

                            {{-- Retro Segmented Meter --}}
                            <div class="w-full h-3 border border-[color:var(--ink)] p-0.5 flex gap-1" style="background: var(--cream);">
                                @for($i = 0; $i < 10; $i++)
                                    <div class="h-full flex-1" style="background: var(--gold); opacity: {{ $i < ($percentage / 10) ? '1' : '0.15' }};"></div>
                                @endfor
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <p class="font-pixel text-[9px] text-stone-500">NO PAYMENT RECORDS TO COMPILE MIX.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="p-3 border-t-2 text-center" style="border-color: var(--ink); background: var(--parchment);">
                <p class="font-pixel text-[8px] text-stone-600">KYMNET FINANCIAL VERIFICATION SYSTEM</p>
            </div>
        </div>
    </div>

    {{-- Comprehensive Payment Ledger Table --}}
    <div class="pixel-border" style="background: var(--cream);">
        <div class="p-4 border-b-2 flex items-center justify-between flex-wrap gap-2" style="background: var(--ink); color: var(--cream); border-color: var(--ink);">
            <div>
                <h2 class="font-pixel text-xs tracking-wider text-[color:var(--gold)]">
                    MASTER PAYMENT LEDGER
                </h2>
                <p class="text-sm text-stone-300 mt-0.5">Comprehensive audit trail of all recorded player transactions</p>
            </div>
            <span class="font-pixel text-[8px] px-2 py-1" style="background: var(--parchment); color: var(--ink);">
                {{ $payments->total() }} TOTAL ENTRIES
            </span>
        </div>

        <div class="p-4 overflow-x-auto">
            @if($payments->isEmpty())
                <div class="p-8 text-center border-2 border-dashed" style="border-color: var(--ink);">
                    <p class="font-pixel text-xs" style="color: var(--ink);">NO TRANSACTIONS IN LEDGER</p>
                    <p class="text-base text-stone-600 mt-2">Paid bookings will automatically append to this ledger.</p>
                </div>
            @else
                <table class="w-full text-base border-collapse">
                    <thead>
                        <tr class="border-b-2" style="border-color: var(--ink); background: var(--parchment);">
                            <th class="p-2.5 text-left font-pixel text-[9px]">REF NO.</th>
                            <th class="p-2.5 text-left font-pixel text-[9px]">PLAYER</th>
                            <th class="p-2.5 text-left font-pixel text-[9px]">METHOD</th>
                            <th class="p-2.5 text-left font-pixel text-[9px]">DATE</th>
                            <th class="p-2.5 text-left font-pixel text-[9px]">STATUS</th>
                            <th class="p-2.5 text-right font-pixel text-[9px]">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr class="border-b" style="border-color: rgba(26,22,17,0.2);">
                                <td class="p-2.5 font-pixel text-[9px]" style="color: var(--ink);">
                                    {{ $payment->ref_num ?: 'PAY-#' . $payment->id }}
                                </td>
                                <td class="p-2.5">
                                    <div class="font-bold text-base leading-tight">{{ $payment->booking?->user?->name ?? 'Guest User' }}</div>
                                    <div class="text-xs text-stone-500">{{ $payment->booking?->user?->email }}</div>
                                </td>
                                <td class="p-2.5 font-bold uppercase text-stone-700">
                                    {{ $payment->payment_method ?: 'Standard' }}
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

        @if($payments->hasPages())
            <div class="p-4 border-t-2 flex justify-between items-center" style="border-color: var(--ink); background: var(--parchment);">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
