<x-app-layout>
    <x-slot name="header">
        <h2 class="font-pixel text-lg">Courts Booked</h2>
    </x-slot>

    <div class="max-w-6xl mx-auto px-6 py-10">
        @if (session('status'))
            <div class="pixel-border p-4 mb-6" style="background: var(--jade); color: var(--cream);">
                {{ session('status') }}
            </div>
        @endif

        @if ($bookings->isEmpty())
            <p class="text-lg" style="opacity: 0.7;">You haven't booked a court yet.</p>
        @else
            <div class="pixel-border overflow-x-auto" style="background: var(--cream);">
                <table class="w-full text-base border-collapse">
                    <thead>
                        <tr style="background: var(--ink); color: var(--cream);">
                            <th class="p-3 text-left font-pixel text-[9px]">Court</th>
                            <th class="p-3 text-left font-pixel text-[9px]">Date</th>
                            <th class="p-3 text-left font-pixel text-[9px]">Time</th>
                            <th class="p-3 text-left font-pixel text-[9px]">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookings as $booking)
                            <tr style="border-top: 1px solid var(--ink);">
                                <td class="p-3">{{ $booking->court->court_name ?? 'Deleted court' }}</td>
                                <td class="p-3">{{ \Illuminate\Support\Carbon::parse($booking->date)->format('M d, Y') }}</td>
                                <td class="p-3">
                                    {{ \Illuminate\Support\Carbon::parse($booking->start_time)->format('g:i A') }}
                                    -
                                    {{ \Illuminate\Support\Carbon::parse($booking->end_time)->format('g:i A') }}
                                </td>
                                <td class="p-3">
                                    <span class="text-[10px] font-pixel px-3 py-1 pixel-border" style="background: var(--parchment);">
                                        {{ ucfirst($booking->booking_status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>