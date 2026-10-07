<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="gz-font-display font-bold text-xl sm:text-2xl">Open Play & Tournaments</h1>
                <p class="text-xs mt-1" style="color: var(--gz-muted);">
                    Manage communal open play sessions and tournament brackets with per-slot player capacity and court allocations.
                </p>
            </div>
            @if(Auth::user()->isManager())
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.open-play.create') }}" class="gz-btn-primary flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Create Session</span>
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    {{-- Status Flash Alert --}}
    @if (session('status'))
        <div class="mb-4 p-4 rounded-xl text-sm border" style="background: rgba(62, 207, 126, 0.08); border-color: rgba(62, 207, 126, 0.3); color: var(--gz-ink);">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0" style="color: var(--gz-pop-dark);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        </div>
    @endif

    {{-- Metric Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="gz-kpi-card">
            <span class="gz-kpi-label">Upcoming Sessions</span>
            <div class="gz-kpi-value mt-1">{{ number_format($upcomingCount) }}</div>
            <p class="text-[11px] mt-1" style="color: var(--gz-muted);">Active sessions accepting player registrations</p>
        </div>

        <div class="gz-kpi-card">
            <span class="gz-kpi-label">Total Player Registrations</span>
            <div class="gz-kpi-value mt-1">{{ number_format($totalRegistrations) }}</div>
            <p class="text-[11px] mt-1" style="color: var(--gz-muted);">Total player slot tickets issued</p>
        </div>

        <div class="gz-kpi-card">
            <span class="gz-kpi-label">Open Play Revenue</span>
            <div class="gz-kpi-value mt-1">₱{{ number_format($totalOpenPlayRevenue, 2) }}</div>
            <p class="text-[11px] mt-1" style="color: var(--gz-muted);">Total collected participation fees</p>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <div class="gz-panel p-4 mb-6">
        <form method="GET" action="{{ route('admin.open-play.index') }}" class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <label for="filter-status" class="text-xs font-semibold" style="color: var(--gz-muted);">Status:</label>
                <select id="filter-status" name="status" onchange="this.form.submit()" class="gz-select text-xs py-1.5 px-3">
                    <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="scheduled" {{ $statusFilter === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="ongoing" {{ $statusFilter === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ $statusFilter === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $statusFilter === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <label for="filter-type" class="text-xs font-semibold" style="color: var(--gz-muted);">Event Type:</label>
                <select id="filter-type" name="type" onchange="this.form.submit()" class="gz-select text-xs py-1.5 px-3">
                    <option value="all" {{ $typeFilter === 'all' ? 'selected' : '' }}>All Types</option>
                    <option value="open_play" {{ $typeFilter === 'open_play' ? 'selected' : '' }}>Open Play</option>
                    <option value="tournament" {{ $typeFilter === 'tournament' ? 'selected' : '' }}>Tournament</option>
                </select>
            </div>

            @if($statusFilter !== 'all' || $typeFilter !== 'all')
                <a href="{{ route('admin.open-play.index') }}" class="text-xs font-semibold underline ml-auto" style="color: var(--gz-danger);">
                    Reset Filters
                </a>
            @endif
        </form>
    </div>

    {{-- Sessions Table / Card Grid --}}
    <div class="gz-panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="gz-table w-full text-xs">
                <thead>
                    <tr>
                        <th class="text-left py-3 px-4">Session Info</th>
                        <th class="text-left py-3 px-4">Date & Time</th>
                        <th class="text-left py-3 px-4">Allocated Courts</th>
                        <th class="text-left py-3 px-4">Skill Level</th>
                        <th class="text-center py-3 px-4">Capacity & Roster</th>
                        <th class="text-right py-3 px-4">Fee / Slot</th>
                        <th class="text-center py-3 px-4">Status</th>
                        <th class="text-right py-3 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--gz-border);">
                    @forelse($sessions as $session)
                        @php
                            $registeredCount = $session->registered_slots_count;
                            $percent = $session->capacity_percent;
                            $isFull = $session->is_full;
                        @endphp
                        <tr class="hover:bg-black/5 dark:hover:bg-white/5 transition">
                            <td class="py-3 px-4">
                                <div class="font-bold text-sm" style="color: var(--gz-ink);">
                                    <a href="{{ route('admin.open-play.show', $session) }}" class="hover:underline">
                                        {{ $session->title }}
                                    </a>
                                </div>
                                <div class="text-[11px] mt-0.5" style="color: var(--gz-muted);">
                                    <span class="font-mono uppercase font-bold">{{ str_replace('_', ' ', $session->session_type) }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="font-semibold">{{ $session->date->format('M d, Y (D)') }}</div>
                                <div class="text-[11px]" style="color: var(--gz-muted);">{{ $session->time_window }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex flex-wrap gap-1 max-w-[200px]">
                                    @forelse($session->courts as $court)
                                        <span class="gz-badge-outline text-[10px] px-2 py-0.5">
                                            {{ $court->court_name }}
                                        </span>
                                    @empty
                                        <span class="text-[11px] italic" style="color: var(--gz-muted);">No courts allocated</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="gz-badge text-[10px] px-2 py-0.5">
                                    {{ $session->skill_level }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="font-bold {{ $isFull ? 'text-amber-600' : '' }}">
                                        {{ $registeredCount }} / {{ $session->max_capacity }}
                                    </span>
                                    <span class="text-[10px]" style="color: var(--gz-muted);">slots</span>
                                </div>
                                <div class="w-24 mx-auto bg-gray-200 dark:bg-gray-700 h-1.5 rounded-full mt-1.5 overflow-hidden">
                                    <div class="h-full rounded-full {{ $isFull ? 'bg-amber-500' : 'bg-emerald-500' }}" style="width: {{ $percent }}%;"></div>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap font-mono font-bold">
                                ₱{{ number_format($session->price_per_slot, 2) }}
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="gz-badge text-[10px] uppercase font-bold
                                    {{ $session->session_status === 'scheduled' ? 'gz-badge-success' : '' }}
                                    {{ $session->session_status === 'ongoing' ? 'gz-badge-primary' : '' }}
                                    {{ $session->session_status === 'completed' ? 'gz-badge-outline' : '' }}
                                    {{ $session->session_status === 'cancelled' ? 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-400' : '' }}
                                ">
                                    {{ $session->session_status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.open-play.show', $session) }}" class="gz-btn-outline gz-btn-sm" title="View Roster">
                                        Roster
                                    </a>
                                    @if(Auth::user()->isManager())
                                        <a href="{{ route('admin.open-play.edit', $session) }}" class="gz-btn-outline gz-btn-sm" title="Edit Session">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.open-play.destroy', $session) }}" onsubmit="return confirm('Are you sure you want to delete or cancel this session?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="gz-btn-outline gz-btn-sm text-red-600 hover:text-red-700" title="Delete or Cancel">
                                                Cancel
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10" style="color: var(--gz-muted);">
                                <p class="text-sm font-semibold">No Open Play or Tournament sessions found.</p>
                                <p class="text-xs mt-1">
                                    @if(Auth::user()->isManager())
                                        Click "Create Session" above to configure your first communal pickleball event.
                                    @else
                                        Waiting for a manager to configure communal sessions.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sessions->hasPages())
            <div class="p-4 border-t" style="border-color: var(--gz-border);">
                {{ $sessions->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
