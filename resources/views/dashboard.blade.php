<x-app-layout>
    <x-slot name="header">
        <h1 class="font-pixel text-lg">Dashboard</h1>
    </x-slot>

    <div class="max-w-6xl mx-auto px-6 py-10">
        <div class="pixel-border p-6" style="background: var(--cream);">
            <p class="text-lg">
                You're logged in, {{ Auth::user()->name }}!
            </p>
            <p class="text-lg mt-4" style="opacity: 0.7;">
                Head to
                <a href="{{ route('booking') }}" class="underline" style="color: var(--jade);">Book Courts</a>
                to reserve a court, or check
                <a href="{{ route('bookings.index') }}" class="underline" style="color: var(--jade);">Courts Booked</a>
                to see your upcoming reservations.
            </p>
        </div>
    </div>
</x-app-layout>