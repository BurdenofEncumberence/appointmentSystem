<x-staff-layout>
    <x-slot name="heading">Today's Customer Attendance</x-slot>

    {{-- Header & Operational Banner --}}
    <div class="pixel-border p-4 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4" style="background: var(--cream);">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-block w-2.5 h-2.5" style="background: var(--jade);"></span>
                <span class="font-pixel text-[10px] uppercase" style="color: var(--jade);">FRONT DESK · ATTENDANCE TRACKER</span>
            </div>
            <p class="font-pixel text-xs" style="color: var(--ink);">
                CUSTOMERS SCHEDULED FOR TODAY
            </p>
            <p class="text-base text-stone-600 mt-1">
                Track player arrival and attendance: Show or No-Show for {{ $today->format('l, F j, Y') }}.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2 print:hidden">
            <button onclick="window.print()" class="pixel-btn text-[9px] py-2 px-3" style="background: var(--gold); color: var(--ink);">
                🖨 PRINT RUNSHEET
            </button>
            <a href="{{ route('staff.today') }}" class="pixel-btn text-[9px] py-2 px-3" style="background: var(--ink); color: var(--cream);">
                ↺ REFRESH
            </a>
        </div>
    </div>

    {{-- Feedback Alerts --}}
    @if(session('status'))
        <div class="pixel-border p-3 mb-6 font-pixel text-xs" style="background: var(--jade); color: var(--cream);">
            ✓ {{ session('status') }}
        </div>
    @endif

    {{-- 4 Attendance KPI Counter Ledgers --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 print:hidden">
        {{-- Total Scheduled --}}
        <div class="pixel-border p-4 flex flex-col justify-between" style="background: var(--cream);">
            <div class="flex items-center justify-between border-b-2 pb-2" style="border-color: var(--ink);">
                <span class="font-pixel text-[9px] uppercase tracking-wider" style="color: var(--ink);">SCHEDULED</span>
                <span class="font-pixel text-[8px] px-1.5 py-0.5" style="background: var(--ink); color: var(--gold);">TODAY</span>
            </div>
            <div class="my-3">
                <div class="font-pixel text-2xl" style="color: var(--ink);">
                    {{ $totalBookingsToday }}
                </div>
            </div>
            <div class="pt-2 border-t text-sm font-bold" style="border-color: rgba(26,22,17,0.15);">
                <span class="font-pixel text-[8px] text-stone-600">CONFIRMED MATCH SLOTS</span>
            </div>
        </div>

        {{-- Show Count --}}
        <div class="pixel-border p-4 flex flex-col justify-between" style="background: var(--cream);">
            <div class="flex items-center justify-between border-b-2 pb-2" style="border-color: var(--ink);">
                <span class="font-pixel text-[9px] uppercase tracking-wider" style="color: var(--ink);">SHOW</span>
                <span class="font-pixel text-[8px] px-1.5 py-0.5" style="background: var(--jade); color: var(--cream);">PRESENT</span>
            </div>
            <div class="my-3">
                <div class="font-pixel text-2xl" style="color: var(--jade);">
                    {{ $showCount }}
                </div>
            </div>
            <div class="pt-2 border-t text-sm font-bold" style="border-color: rgba(26,22,17,0.15);">
                <span class="font-pixel text-[8px] text-emerald-700">ARRIVED & PLAYED</span>
            </div>
        </div>

        {{-- No-Show Count --}}
        <div class="pixel-border p-4 flex flex-col justify-between" style="background: var(--cream);">
            <div class="flex items-center justify-between border-b-2 pb-2" style="border-color: var(--ink);">
                <span class="font-pixel text-[9px] uppercase tracking-wider" style="color: var(--ink);">NO-SHOW</span>
                <span class="font-pixel text-[8px] px-1.5 py-0.5" style="background: var(--red); color: var(--cream);">ABSENT</span>
            </div>
            <div class="my-3">
                <div class="font-pixel text-2xl" style="color: {{ $noShowCount > 0 ? 'var(--red)' : 'var(--ink)' }};">
                    {{ $noShowCount }}
                </div>
            </div>
            <div class="pt-2 border-t text-sm font-bold" style="border-color: rgba(26,22,17,0.15);">
                <span class="font-pixel text-[8px] {{ $noShowCount > 0 ? 'text-red-700' : 'text-stone-500' }}">
                    {{ $noShowCount > 0 ? 'MISSED APPOINTMENT' : 'NO MISSED MATCHES' }}
                </span>
            </div>
        </div>

        {{-- Awaiting Arrival --}}
        <div class="pixel-border p-4 flex flex-col justify-between" style="background: var(--cream);">
            <div class="flex items-center justify-between border-b-2 pb-2" style="border-color: var(--ink);">
                <span class="font-pixel text-[9px] uppercase tracking-wider" style="color: var(--ink);">AWAITING</span>
                <span class="font-pixel text-[8px] px-1.5 py-0.5" style="background: var(--gold); color: var(--ink);">ARRIVAL</span>
            </div>
            <div class="my-3">
                <div class="font-pixel text-2xl" style="color: var(--ink);">
                    {{ $awaitingCount }}
                </div>
            </div>
            <div class="pt-2 border-t text-sm font-bold" style="border-color: rgba(26,22,17,0.15);">
                <span class="font-pixel text-[8px] text-stone-600">SCHEDULED FOR TODAY</span>
            </div>
        </div>
    </div>

    {{-- Search and Filter Controls --}}
    <div class="pixel-border p-4 mb-6 print:hidden" style="background: var(--cream);">
        <form method="GET" action="{{ route('staff.today') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            {{-- Search Input --}}
            <div class="flex-1 flex gap-2">
                <input type="text"
                       name="search"
                       value="{{ $searchTerm }}"
                       placeholder="Search customer name, email, or court..."
                       class="pixel-input text-base">
                <button type="submit" class="pixel-btn text-[9px] py-2 px-4 whitespace-nowrap" style="background: var(--ink); color: var(--cream);">
                    SEARCH
                </button>
                @if($searchTerm || $selectedCourt || $selectedStatus)
                    <a href="{{ route('staff.today') }}" class="pixel-btn text-[9px] py-2 px-3 whitespace-nowrap" style="background: var(--parchment); color: var(--ink);">
                        CLEAR
                    </a>
                @endif
            </div>

            {{-- Court & Attendance Quick Filters --}}
            <div class="flex flex-wrap items-center gap-2">
                {{-- Court Filter --}}
                <select name="court_id" onchange="this.form.submit()" class="pixel-input py-2 text-sm cursor-pointer w-auto">
                    <option value="">-- ALL COURTS --</option>
                    @foreach($courts as $court)
                        <option value="{{ $court->id }}" @selected($selectedCourt == $court->id)>
                            {{ $court->court_name }}
                        </option>
                    @endforeach
                </select>

                {{-- Attendance Status Filter --}}
                <select name="status" onchange="this.form.submit()" class="pixel-input py-2 text-sm cursor-pointer w-auto">
                    <option value="">-- ALL ATTENDANCE --</option>
                    <option value="show" @selected($selectedStatus === 'show')>Show (Present)</option>
                    <option value="no_show" @selected($selectedStatus === 'no_show')>No-Show (Absent)</option>
                    <option value="scheduled" @selected($selectedStatus === 'scheduled')>Awaiting Arrival</option>
                </select>
            </div>
        </form>
    </div>

    {{-- Main Customers Table --}}
    <div class="pixel-border" style="background: var(--cream);">
        <div class="p-4 border-b-2 flex items-center justify-between flex-wrap gap-2" style="background: var(--ink); color: var(--cream); border-color: var(--ink);">
            <div>
                <h2 class="font-pixel text-xs tracking-wider text-[color:var(--gold)]">
                    TODAY'S CUSTOMER RUN-SHEET
                </h2>
                <p class="text-sm text-stone-300 mt-0.5">
                    Showing {{ $bookings->count() }} customer reservation{{ $bookings->count() === 1 ? '' : 's' }} for {{ $today->format('D, M d, Y') }}
                </p>
            </div>
            <span class="font-pixel text-[8px] px-2 py-1" style="background: var(--parchment); color: var(--ink);">
                {{ $bookings->count() }} MATCH{{ $bookings->count() === 1 ? '' : 'ES' }}
            </span>
        </div>

        <div class="p-4 overflow-x-auto">
            @if($bookings->isEmpty())
                <div class="p-12 text-center border-2 border-dashed" style="border-color: var(--ink);">
                    <p class="font-pixel text-xs" style="color: var(--ink);">NO CUSTOMERS FOUND FOR TODAY</p>
                    <p class="text-base text-stone-600 mt-2">
                        @if($searchTerm || $selectedCourt || $selectedStatus)
                            No scheduled customers match your filter parameters. Try clearing the filter.
                        @else
                            No player bookings have been scheduled for today.
                        @endif
                    </p>
                    @if($searchTerm || $selectedCourt || $selectedStatus)
                        <div class="mt-4">
                            <a href="{{ route('staff.today') }}" class="pixel-btn text-[9px] py-1.5 px-3" style="background: var(--gold); color: var(--ink);">
                                RESET FILTERS
                            </a>
                        </div>
                    @endif
                </div>
            @else
                <table class="w-full text-base border-collapse">
                    <thead>
                        <tr class="border-b-2" style="border-color: var(--ink); background: var(--parchment);">
                            <th class="p-3 text-left font-pixel text-[9px]">TIME / TIMING</th>
                            <th class="p-3 text-left font-pixel text-[9px]">CUSTOMER / PLAYER</th>
                            <th class="p-3 text-left font-pixel text-[9px]">COURT ASSIGNED</th>
                            <th class="p-3 text-left font-pixel text-[9px]">ATTENDANCE</th>
                            <th class="p-3 text-right font-pixel text-[9px] print:hidden">STAFF ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            @php
                                $startTime = \Carbon\Carbon::parse($booking->date . ' ' . $booking->start_time);
                                $endTime = \Carbon\Carbon::parse($booking->date . ' ' . $booking->end_time);
                                $isLiveNow = now()->between($startTime, $endTime);
                                $isPast = now()->gt($endTime);
                                $isNoShow = in_array($booking->booking_status, ['no_show', 'no-show']);
                                $isShow = $booking->booking_status === 'show';
                            @endphp
                            <tr class="border-b hover:bg-[color:var(--parchment)] transition-colors" style="border-color: rgba(26,22,17,0.2);">
                                {{-- Time and Live Indicator --}}
                                <td class="p-3 align-top whitespace-nowrap">
                                    <div class="font-pixel text-[10px]" style="color: var(--ink);">
                                        {{ $startTime->format('g:i A') }} - {{ $endTime->format('g:i A') }}
                                    </div>
                                    <div class="mt-1">
                                        @if($isLiveNow)
                                            <span class="font-pixel text-[7px] px-1.5 py-0.5 inline-block animate-pulse" style="background: var(--jade); color: var(--cream);">
                                                ● LIVE ON COURT
                                            </span>
                                        @elseif($isPast)
                                            <span class="font-pixel text-[7px] px-1.5 py-0.5 inline-block" style="background: var(--parchment); color: var(--ink);">
                                                ✓ FINISHED
                                            </span>
                                        @else
                                            <span class="font-pixel text-[7px] px-1.5 py-0.5 inline-block" style="background: var(--gold); color: var(--ink);">
                                                ▲ UPCOMING
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Customer Details --}}
                                <td class="p-3 align-top">
                                    <div class="flex items-start gap-2.5">
                                        <div class="w-8 h-8 flex items-center justify-center font-pixel text-[10px] pixel-border shrink-0" style="background: var(--gold); color: var(--ink);">
                                            {{ strtoupper(substr($booking->user?->first_name ?: $booking->user?->name ?: 'G', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-base leading-tight" style="color: var(--ink);">
                                                {{ $booking->user?->name ?? 'Guest Customer' }}
                                            </div>
                                            <div class="text-xs text-stone-600">
                                                {{ $booking->user?->email ?? 'No email on file' }}
                                            </div>
                                            @if($booking->user?->role)
                                                <span class="inline-block mt-0.5 font-pixel text-[7px] text-stone-500 uppercase">
                                                    [{{ $booking->user->role }}]
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Court --}}
                                <td class="p-3 align-top">
                                    <div class="font-bold font-pixel text-[10px]" style="color: var(--ink);">
                                        {{ $booking->court?->court_name ?? 'Court Unassigned' }}
                                    </div>
                                    <div class="text-xs text-stone-600">
                                        {{ $booking->court?->size ?: 'Standard Pickleball' }}
                                    </div>
                                </td>

                                {{-- Attendance Status Badge --}}
                                <td class="p-3 align-top">
                                    @if($isShow)
                                        <span class="font-pixel text-[8px] px-2 py-0.5 inline-block pixel-border" style="background: var(--jade); color: var(--cream);">
                                            ✓ SHOW (PRESENT)
                                        </span>
                                    @elseif($isNoShow)
                                        <span class="font-pixel text-[8px] px-2 py-0.5 inline-block pixel-border" style="background: var(--red); color: var(--cream);">
                                            ✕ NO-SHOW (ABSENT)
                                        </span>
                                    @else
                                        <span class="font-pixel text-[8px] px-2 py-0.5 inline-block pixel-border" style="background: var(--gold); color: var(--ink);">
                                            ⏱ AWAITING ARRIVAL
                                        </span>
                                    @endif
                                </td>

                                {{-- Staff Attendance Actions (Show / No Show) --}}
                                <td class="p-3 align-top text-right print:hidden">
                                    <div class="flex items-center justify-end gap-2 flex-wrap">
                                        {{-- Mark as SHOW --}}
                                        @if(!$isShow)
                                            <form method="POST" action="{{ route('staff.bookings.status', $booking) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="attendance_status" value="show">
                                                <button type="submit" class="pixel-btn text-[8px] py-1 px-2.5" style="background: var(--jade); color: var(--cream);" title="Mark Customer as Present">
                                                    ✓ SHOW
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Mark as NO-SHOW --}}
                                        @if(!$isNoShow)
                                            <form method="POST" action="{{ route('staff.bookings.status', $booking) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="attendance_status" value="no_show">
                                                <button type="submit" class="pixel-btn text-[8px] py-1 px-2.5" style="background: var(--red); color: var(--cream);" title="Mark Customer as No-Show">
                                                    ✕ NO-SHOW
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Reset to Awaiting if already marked --}}
                                        @if($isShow || $isNoShow)
                                            <form method="POST" action="{{ route('staff.bookings.status', $booking) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="attendance_status" value="scheduled">
                                                <button type="submit" class="pixel-btn text-[7px] py-1 px-2" style="background: var(--parchment); color: var(--ink);" title="Reset to Awaiting">
                                                    RESET
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Footer Run-sheet summary --}}
        <div class="p-3 border-t-2 flex flex-col sm:flex-row items-center justify-between text-xs text-stone-600 font-pixel" style="border-color: var(--ink); background: var(--parchment);">
            <span>KYMNET ARENA FRONT DESK</span>
            <span>DATE: {{ $today->format('Y-m-d') }} · ATTENDANCE ENGINE</span>
        </div>
    </div>
</x-staff-layout>
