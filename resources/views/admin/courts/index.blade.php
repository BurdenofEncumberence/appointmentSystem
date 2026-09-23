<x-admin-layout>
    <x-slot name="heading">Court Inventory</x-slot>

    {{-- Top Action Bar --}}
    <div class="pixel-border p-4 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4" style="background: var(--cream);">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-block w-2.5 h-2.5" style="background: var(--jade);"></span>
                <span class="font-pixel text-[10px] uppercase" style="color: var(--jade);">ARENA ASSETS & CONFIGURATION</span>
            </div>
            <p class="font-pixel text-xs" style="color: var(--ink);">
                COURT ROSTER & HOURLY RATES
            </p>
            <p class="text-base text-stone-600 mt-1">
                Manage court availability, operational status, and pricing rules.
            </p>
        </div>
        <a href="{{ route('admin.courts.create') }}" class="pixel-btn text-[10px] py-2 px-3" style="background: var(--jade); color: var(--cream);">
            + ADD NEW COURT
        </a>
    </div>

    {{-- Session Feedback Messages --}}
    @if(session('status'))
        <div class="pixel-border p-3 mb-6 font-pixel text-xs" style="background: var(--jade); color: var(--cream);">
            ✓ {{ session('status') }}
        </div>
    @endif
    @if(session('error'))
        <div class="pixel-border p-3 mb-6 font-pixel text-xs" style="background: var(--red); color: var(--cream);">
            ⚠ {{ session('error') }}
        </div>
    @endif

    {{-- KPI Metric Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="pixel-border p-4 flex flex-col justify-between" style="background: var(--cream);">
            <div class="flex items-center justify-between border-b-2 pb-2" style="border-color: var(--ink);">
                <span class="font-pixel text-[9px] uppercase tracking-wider" style="color: var(--ink);">TOTAL COURTS</span>
                <span class="font-pixel text-[8px] px-1.5 py-0.5" style="background: var(--ink); color: var(--cream);">ALL</span>
            </div>
            <div class="my-4">
                <div class="font-pixel text-2xl" style="color: var(--ink);">
                    {{ $courts->count() }}
                </div>
            </div>
            <div class="pt-2 border-t text-sm font-bold" style="border-color: rgba(26,22,17,0.15);">
                <span class="font-pixel text-[8px] text-stone-600">REGISTERED ARENAS</span>
            </div>
        </div>

        <div class="pixel-border p-4 flex flex-col justify-between" style="background: var(--cream);">
            <div class="flex items-center justify-between border-b-2 pb-2" style="border-color: var(--ink);">
                <span class="font-pixel text-[9px] uppercase tracking-wider" style="color: var(--ink);">AVAILABLE</span>
                <span class="font-pixel text-[8px] px-1.5 py-0.5" style="background: var(--jade); color: var(--cream);">ACTIVE</span>
            </div>
            <div class="my-4">
                <div class="font-pixel text-2xl" style="color: var(--jade);">
                    {{ $courts->where('court_status', 'available')->count() }}
                </div>
            </div>
            <div class="pt-2 border-t text-sm font-bold" style="border-color: rgba(26,22,17,0.15);">
                <span class="font-pixel text-[8px]" style="color: var(--jade);">OPEN FOR BOOKINGS</span>
            </div>
        </div>

        <div class="pixel-border p-4 flex flex-col justify-between" style="background: var(--cream);">
            <div class="flex items-center justify-between border-b-2 pb-2" style="border-color: var(--ink);">
                <span class="font-pixel text-[9px] uppercase tracking-wider" style="color: var(--ink);">MAINTENANCE</span>
                <span class="font-pixel text-[8px] px-1.5 py-0.5" style="background: var(--gold); color: var(--ink);">HOLD</span>
            </div>
            <div class="my-4">
                <div class="font-pixel text-2xl" style="color: {{ $courts->where('court_status', '!=', 'available')->count() > 0 ? 'var(--gold)' : 'var(--ink)' }};">
                    {{ $courts->where('court_status', '!=', 'available')->count() }}
                </div>
            </div>
            <div class="pt-2 border-t text-sm font-bold" style="border-color: rgba(26,22,17,0.15);">
                <span class="font-pixel text-[8px] text-stone-600">OFFLINE / REPAIR</span>
            </div>
        </div>
    </div>

    {{-- Courts Inventory Table --}}
    <div class="pixel-border" style="background: var(--cream);">
        <div class="p-4 border-b-2 flex items-center justify-between flex-wrap gap-2" style="background: var(--ink); color: var(--cream); border-color: var(--ink);">
            <div>
                <h2 class="font-pixel text-xs tracking-wider text-[color:var(--gold)]">
                    COURT INVENTORY MATRIX
                </h2>
                <p class="text-sm text-stone-300 mt-0.5">Active configuration for the public booking engine</p>
            </div>
            <span class="font-pixel text-[8px] px-2 py-1" style="background: var(--parchment); color: var(--ink);">
                {{ $courts->count() }} COURTS
            </span>
        </div>

        <div class="p-4 overflow-x-auto">
            @if($courts->isEmpty())
                <div class="p-8 text-center border-2 border-dashed" style="border-color: var(--ink);">
                    <p class="font-pixel text-xs" style="color: var(--ink);">NO COURTS FOUND</p>
                    <p class="text-base text-stone-600 mt-2">Add your first court to open public appointments.</p>
                </div>
            @else
                <table class="w-full text-base border-collapse">
                    <thead>
                        <tr class="border-b-2" style="border-color: var(--ink); background: var(--parchment);">
                            <th class="p-2.5 text-left font-pixel text-[9px]">COURT</th>
                            <th class="p-2.5 text-left font-pixel text-[9px]">DIMENSIONS</th>
                            <th class="p-2.5 text-left font-pixel text-[9px]">RATE / HOUR</th>
                            <th class="p-2.5 text-left font-pixel text-[9px]">TOTAL BOOKINGS</th>
                            <th class="p-2.5 text-left font-pixel text-[9px]">STATUS</th>
                            <th class="p-2.5 text-right font-pixel text-[9px]">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($courts as $court)
                            <tr class="border-b" style="border-color: rgba(26,22,17,0.2);">
                                <td class="p-2.5 font-bold">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 flex items-center justify-center font-pixel text-[10px] pixel-border" style="background: var(--gold); color: var(--ink);">
                                            {{ strtoupper(substr($court->court_name, 0, 1)) }}
                                        </div>
                                        <span class="font-pixel text-[11px]">{{ $court->court_name }}</span>
                                    </div>
                                </td>
                                <td class="p-2.5 text-stone-700">
                                    {{ $court->size ?: 'Standard' }}
                                </td>
                                <td class="p-2.5 font-pixel text-[10px]" style="color: var(--jade);">
                                    ₱{{ number_format($court->price_per_hour, 2) }}
                                </td>
                                <td class="p-2.5 font-pixel text-[9px]">
                                    {{ $court->bookings_count }}
                                </td>
                                <td class="p-2.5">
                                    @if($court->court_status === 'available')
                                        <span class="font-pixel text-[8px] px-2 py-0.5 inline-block pixel-border" style="background: var(--jade); color: var(--cream);">
                                            AVAILABLE
                                        </span>
                                    @elseif($court->court_status === 'maintenance')
                                        <span class="font-pixel text-[8px] px-2 py-0.5 inline-block pixel-border" style="background: var(--gold); color: var(--ink);">
                                            MAINTENANCE
                                        </span>
                                    @else
                                        <span class="font-pixel text-[8px] px-2 py-0.5 inline-block pixel-border" style="background: var(--red); color: var(--cream);">
                                            CLOSED
                                        </span>
                                    @endif
                                </td>
                                <td class="p-2.5 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.courts.edit', $court) }}" class="pixel-btn text-[8px] py-1 px-2.5 mr-2" style="background: var(--parchment); color: var(--ink);">
                                        EDIT
                                    </a>
                                    @if($court->bookings_count === 0)
                                        <form class="inline" method="POST" action="{{ route('admin.courts.destroy', $court) }}" onsubmit="return confirm('Remove this court from inventory?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="pixel-btn text-[8px] py-1 px-2.5" style="background: var(--red); color: var(--cream);">
                                                DELETE
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-admin-layout>
