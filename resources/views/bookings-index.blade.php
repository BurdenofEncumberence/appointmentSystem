<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <h1 class="gz-font-display font-bold text-lg">Courts Booked</h1>
            <a href="{{ route('booking') }}" class="gz-btn-primary gz-btn-sm">
                + Book Another Court
            </a>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto px-6 py-10">
        @if (session('status'))
            <div class="gz-status mb-6" role="status" aria-live="polite">
                {{ session('status') }}
            </div>
        @endif

        @if ($bookings->isEmpty())
            <div class="gz-panel p-8 text-center" style="background: var(--gz-surface);">
                <p class="text-base font-semibold mb-2">You haven't booked a court yet.</p>
                <p class="text-sm mb-6" style="color: var(--gz-muted);">Pick an open court slot, lock in your match time, and rally.</p>
                <a href="{{ route('booking') }}" class="gz-btn-primary">
                    Reserve a Court
                </a>
            </div>
        @else
            <div class="gz-panel overflow-x-auto">
                <table class="gz-table">
                    <thead>
                        <tr>
                            <th>Court</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Transaction Ref</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookings as $booking)
                            <tr>
                                <td class="font-semibold">{{ $booking->court->court_name ?? 'Deleted court' }}</td>
                                <td style="color: var(--gz-muted);">{{ \Illuminate\Support\Carbon::parse($booking->date)->format('M d, Y') }}</td>
                                <td style="color: var(--gz-muted);">
                                    {{ \Illuminate\Support\Carbon::parse($booking->start_time)->format('g:i A') }}
                                    -
                                    {{ \Illuminate\Support\Carbon::parse($booking->end_time)->format('g:i A') }}
                                </td>
                                <td>
                                    @if($booking->payments->isNotEmpty())
                                        <span class="gz-badge gz-badge-neutral text-[11px] font-mono">{{ $booking->payments->first()->ref_num }}</span>
                                    @else
                                        <span class="text-xs" style="color: var(--gz-muted);">-</span>
                                    @endif
                                </td>
                                <td class="font-semibold" style="color: var(--gz-pop-dark);">
                                    @if($booking->payments->isNotEmpty())
                                        ₱{{ number_format($booking->payments->first()->amount, 2) }}
                                    @else
                                        -
                                    @endif
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>