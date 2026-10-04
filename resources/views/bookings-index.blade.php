<x-app-layout>
    <x-slot name="header">
        <h1 class="gz-font-display font-bold text-lg">Courts Booked</h1>
    </x-slot>

    <div class="max-w-6xl mx-auto px-6 py-10">
        @if (session('status'))
            <div class="gz-status mb-6" role="status" aria-live="polite">
                {{ session('status') }}
            </div>
        @endif

        @if ($bookings->isEmpty())
            <p class="text-base" style="color: var(--gz-muted);">You haven't booked a court yet.</p>
        @else
            <div class="gz-panel overflow-x-auto">
                <table class="gz-table">
                    <thead>
                        <tr>
                            <th>Court</th>
                            <th>Date</th>
                            <th>Time</th>
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