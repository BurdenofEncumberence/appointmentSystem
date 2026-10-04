<x-staff-layout>
    <x-slot name="heading">Today's Customer Attendance</x-slot>

    {{-- Header & Operational Banner --}}
    <div class="gz-panel gz-panel-body mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-block w-2 h-2" style="background: var(--gz-pop);"></span>
                <span class="gz-eyebrow" style="color: var(--gz-pop-dark);">Front desk · Attendance tracker</span>
            </div>
            <h1 class="gz-font-display font-bold text-xl">
                Customers scheduled for today
            </h1>
            <p class="text-sm mt-1" style="color: var(--gz-muted);">
                Track player arrival and attendance: Show or No-Show for {{ $today->format('l, F j, Y') }}.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2 print:hidden">
            <button onclick="window.print()" class="gz-btn-outline gz-btn-sm">
                🖨 Print run-sheet
            </button>
            <a href="{{ route('staff.today') }}" class="gz-btn-primary gz-btn-sm">
                ↺ Refresh
            </a>
        </div>
    </div>

    {{-- Feedback Alerts --}}
    @if(session('status'))
        <div class="gz-status mb-6" role="status" aria-live="polite">
            ✓ {{ session('status') }}
        </div>
    @endif

    {{-- 4 Attendance KPI Counter Ledgers --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 print:hidden">
        {{-- Total Scheduled --}}
        <div class="gz-kpi-card">
            <div class="flex items-center justify-between mb-3">
                <div class="icon-badge" style="width:32px; height:32px; padding:6px;" id="icon-scheduled" aria-hidden="true"></div>
                <span class="gz-badge gz-badge-neutral">Today</span>
            </div>
            <div class="gz-eyebrow mb-1">Scheduled</div>
            <div class="gz-kpi-value" style="font-size: 22px;">
                {{ $totalBookingsToday }}
            </div>
            <p class="text-xs mt-2" style="color: var(--gz-muted);">Confirmed match slots</p>
        </div>

        {{-- Show Count --}}
        <div class="gz-kpi-card">
            <div class="flex items-center justify-between mb-3">
                <div class="icon-badge" style="width:32px; height:32px; padding:6px;" id="icon-show" aria-hidden="true"></div>
                <span class="gz-badge gz-badge-success">Present</span>
            </div>
            <div class="gz-eyebrow mb-1">Show</div>
            <div class="gz-kpi-value" style="font-size: 22px; color: var(--gz-pop-dark);">
                {{ $showCount }}
            </div>
            <p class="text-xs mt-2 font-semibold" style="color: var(--gz-pop-dark);">Arrived &amp; played</p>
        </div>

        {{-- No-Show Count --}}
        <div class="gz-kpi-card">
            <div class="flex items-center justify-between mb-3">
                <div class="icon-badge" style="width:32px; height:32px; padding:6px;" id="icon-noshow" aria-hidden="true"></div>
                <span class="gz-badge {{ $noShowCount > 0 ? 'gz-badge-danger' : 'gz-badge-neutral' }}">Absent</span>
            </div>
            <div class="gz-eyebrow mb-1">No-show</div>
            <div class="gz-kpi-value" style="font-size: 22px; color: {{ $noShowCount > 0 ? 'var(--gz-danger)' : 'var(--gz-ink)' }};">
                {{ $noShowCount }}
            </div>
            <p class="text-xs mt-2" style="color: {{ $noShowCount > 0 ? 'var(--gz-danger)' : 'var(--gz-muted)' }};">
                {{ $noShowCount > 0 ? 'Missed appointment' : 'No missed matches' }}
            </p>
        </div>

        {{-- Awaiting Arrival --}}
        <div class="gz-kpi-card">
            <div class="flex items-center justify-between mb-3">
                <div class="icon-badge" style="width:32px; height:32px; padding:6px;" id="icon-awaiting" aria-hidden="true"></div>
                <span class="gz-badge gz-badge-warning">Arrival</span>
            </div>
            <div class="gz-eyebrow mb-1">Awaiting</div>
            <div class="gz-kpi-value" style="font-size: 22px;">
                {{ $awaitingCount }}
            </div>
            <p class="text-xs mt-2" style="color: var(--gz-muted);">Scheduled for today</p>
        </div>
    </div>

    {{-- Search and Filter Controls --}}
    <div class="gz-panel gz-panel-body mb-6 print:hidden">
        <form method="GET" action="{{ route('staff.today') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            {{-- Search Input --}}
            <div class="flex-1 flex gap-2">
                <input type="text"
                       name="search"
                       value="{{ $searchTerm }}"
                       placeholder="Search customer name, email, or court..."
                       class="gz-input">
                <button type="submit" class="gz-btn-primary gz-btn-sm whitespace-nowrap">
                    Search
                </button>
                @if($searchTerm || $selectedCourt || $selectedStatus)
                    <a href="{{ route('staff.today') }}" class="gz-btn-outline gz-btn-sm whitespace-nowrap">
                        Clear
                    </a>
                @endif
            </div>

            {{-- Court & Attendance Quick Filters --}}
            <div class="flex flex-wrap items-center gap-2">
                {{-- Court Filter --}}
                <select name="court_id" onchange="this.form.submit()" class="gz-input w-auto">
                    <option value="">All courts</option>
                    @foreach($courts as $court)
                        <option value="{{ $court->id }}" @selected($selectedCourt == $court->id)>
                            {{ $court->court_name }}
                        </option>
                    @endforeach
                </select>

                {{-- Attendance Status Filter --}}
                <select name="status" onchange="this.form.submit()" class="gz-input w-auto">
                    <option value="">All attendance</option>
                    <option value="show" @selected($selectedStatus === 'show')>Show (Present)</option>
                    <option value="no_show" @selected($selectedStatus === 'no_show')>No-Show (Absent)</option>
                    <option value="scheduled" @selected($selectedStatus === 'scheduled')>Awaiting Arrival</option>
                </select>
            </div>
        </form>
    </div>

    {{-- Main Customers Table --}}
    <div class="gz-panel">
        <div class="gz-panel-header">
            <div>
                <h2 class="gz-font-display font-bold text-base">Today's customer run-sheet</h2>
                <p class="text-xs" style="color: var(--gz-muted);">
                    Showing {{ $bookings->count() }} customer reservation{{ $bookings->count() === 1 ? '' : 's' }} for {{ $today->format('D, M d, Y') }}
                </p>
            </div>
            <span class="gz-badge gz-badge-neutral">
                {{ $bookings->count() }} match{{ $bookings->count() === 1 ? '' : 'es' }}
            </span>
        </div>

        <div class="gz-panel-body overflow-x-auto">
            @if($bookings->isEmpty())
                <div class="p-8 text-center border border-dashed" style="border-color: var(--gz-border); background: var(--gz-surface);">
                    <p class="font-semibold">No customers found for today</p>
                    <p class="text-sm mt-1" style="color: var(--gz-muted);">
                        @if($searchTerm || $selectedCourt || $selectedStatus)
                            No scheduled customers match your filter parameters. Try clearing the filter.
                        @else
                            No player bookings have been scheduled for today.
                        @endif
                    </p>
                    @if($searchTerm || $selectedCourt || $selectedStatus)
                        <div class="mt-4">
                            <a href="{{ route('staff.today') }}" class="gz-btn-outline gz-btn-sm">
                                Reset filters
                            </a>
                        </div>
                    @endif
                </div>
            @else
                <table class="gz-table">
                    <thead>
                        <tr>
                            <th>Time / Timing</th>
                            <th>Customer / Player</th>
                            <th>Court Assigned</th>
                            <th>Attendance</th>
                            <th class="text-right print:hidden">Staff Action</th>
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
                            <tr>
                                {{-- Time and Live Indicator --}}
                                <td class="whitespace-nowrap">
                                    <div class="text-sm font-semibold">
                                        {{ $startTime->format('g:i A') }} - {{ $endTime->format('g:i A') }}
                                    </div>
                                    <div class="mt-1">
                                        @if($isLiveNow)
                                            <span class="gz-badge gz-badge-success">● Live on court</span>
                                        @elseif($isPast)
                                            <span class="gz-badge gz-badge-neutral">✓ Finished</span>
                                        @else
                                            <span class="gz-badge gz-badge-warning">▲ Upcoming</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Customer Details --}}
                                <td>
                                    <div class="flex items-start gap-2.5">
                                        <div class="w-8 h-8 flex items-center justify-center text-sm font-bold shrink-0" style="background: var(--gz-pop); color: var(--gz-ink);">
                                            {{ strtoupper(substr($booking->user?->first_name ?: $booking->user?->name ?: 'G', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-sm">
                                                {{ $booking->user?->name ?? 'Guest Customer' }}
                                            </div>
                                            <div class="text-xs" style="color: var(--gz-muted);">
                                                {{ $booking->user?->email ?? 'No email on file' }}
                                            </div>
                                            @if($booking->user?->role)
                                                <span class="gz-eyebrow inline-block mt-0.5">
                                                    {{ $booking->user->role }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Court --}}
                                <td>
                                    <div class="font-semibold text-sm">
                                        {{ $booking->court?->court_name ?? 'Court Unassigned' }}
                                    </div>
                                    <div class="text-xs" style="color: var(--gz-muted);">
                                        {{ $booking->court?->size ?: 'Standard Pickleball' }}
                                    </div>
                                </td>

                                {{-- Attendance Status Badge --}}
                                <td>
                                    @if($isShow)
                                        <span class="gz-badge gz-badge-success">✓ Show (Present)</span>
                                    @elseif($isNoShow)
                                        <span class="gz-badge gz-badge-danger">✕ No-show (Absent)</span>
                                    @else
                                        <span class="gz-badge gz-badge-warning">⏱ Awaiting arrival</span>
                                    @endif
                                </td>

                                {{-- Staff Attendance Actions (Show / No Show) --}}
                                <td class="text-right print:hidden">
                                    <div class="flex items-center justify-end gap-2 flex-wrap">
                                        {{-- Mark as SHOW --}}
                                        @if(!$isShow)
                                            <form method="POST" action="{{ route('staff.bookings.status', $booking) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="attendance_status" value="show">
                                                <button type="submit" class="gz-btn-success gz-btn-sm" title="Mark Customer as Present">
                                                    ✓ Show
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Mark as NO-SHOW --}}
                                        @if(!$isNoShow)
                                            <form method="POST" action="{{ route('staff.bookings.status', $booking) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="attendance_status" value="no_show">
                                                <button type="submit" class="gz-btn-danger gz-btn-sm" title="Mark Customer as No-Show">
                                                    ✕ No-show
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Reset to Awaiting if already marked --}}
                                        @if($isShow || $isNoShow)
                                            <form method="POST" action="{{ route('staff.bookings.status', $booking) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="attendance_status" value="scheduled">
                                                <button type="submit" class="gz-btn-outline gz-btn-sm" title="Reset to Awaiting">
                                                    Reset
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
        <div class="p-4 border-t flex flex-col sm:flex-row items-center justify-between text-xs" style="border-color: var(--gz-border); color: var(--gz-muted);">
            <span>KYMNET Arena Front Desk</span>
            <span>Date: {{ $today->format('Y-m-d') }} · Attendance engine</span>
        </div>
    </div>
</x-staff-layout>