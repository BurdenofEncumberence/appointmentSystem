<x-app-layout title="Open Play & Tournaments — Gaoshou Pickleball">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6">
        {{-- Hero Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="gz-badge gz-badge-success text-xs font-bold uppercase tracking-wider">Communal Pickleball</span>
                    <span class="text-xs" style="color: var(--gz-muted);">•</span>
                    <span class="text-xs font-semibold" style="color: var(--gz-muted);">Per-Player Participation Tickets</span>
                </div>
                <h1 class="gz-font-display font-extrabold text-2xl sm:text-3xl tracking-tight" style="color: var(--gz-ink);">
                    Open Play & Tournaments
                </h1>
                <p class="text-sm mt-1 max-w-2xl" style="color: var(--gz-muted);">
                    Drop in, rotate in communal player pools, or compete in tournaments. Instead of renting an entire private court, you only pay a per-slot participation fee!
                </p>
            </div>
            @auth
                @if(Auth::user()->isPlayer())
                    <div class="flex items-center gap-2 flex-wrap">
                        <a href="{{ route('open-play.host.create') }}" class="gz-btn-primary gz-btn-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>Host an Open Play</span>
                        </a>
                        <a href="{{ route('open-play.host.index') }}" class="gz-btn-outline gz-btn-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <span>My Hosted Sessions</span>
                        </a>
                    </div>
                @endif
            @endauth
        </div>

        {{-- Filter & Search Toolbar --}}
        <div class="gz-panel p-4 mb-8">
            <form method="GET" action="{{ route('open-play.index') }}" class="flex flex-wrap items-center gap-4">
                {{-- Type Filter --}}
                <div class="flex items-center gap-2">
                    <label for="filter-type" class="text-xs font-bold uppercase tracking-wider" style="color: var(--gz-muted);">Type:</label>
                    <select id="filter-type" name="type" onchange="this.form.submit()" class="gz-select text-xs py-1.5 px-3">
                        <option value="all" {{ $typeFilter === 'all' ? 'selected' : '' }}>All Events</option>
                        <option value="open_play" {{ $typeFilter === 'open_play' ? 'selected' : '' }}>Open Play</option>
                        <option value="tournament" {{ $typeFilter === 'tournament' ? 'selected' : '' }}>Tournaments</option>
                    </select>
                </div>

                {{-- Skill Filter --}}
                <div class="flex items-center gap-2">
                    <label for="filter-skill" class="text-xs font-bold uppercase tracking-wider" style="color: var(--gz-muted);">Skill:</label>
                    <select id="filter-skill" name="skill_level" onchange="this.form.submit()" class="gz-select text-xs py-1.5 px-3">
                        <option value="all" {{ $skillFilter === 'all' ? 'selected' : '' }}>All Skill Levels</option>
                        @foreach($availableSkills as $skill)
                            <option value="{{ $skill }}" {{ $skillFilter === $skill ? 'selected' : '' }}>{{ $skill }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Search Box --}}
                <div class="flex-1 min-w-[200px]">
                    <div class="relative">
                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Search by title or format..."
                            class="gz-input w-full text-xs py-1.5 pl-8 pr-3"
                        >
                        <svg class="w-3.5 h-3.5 absolute left-2.5 top-2.5" style="color: var(--gz-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <button type="submit" class="gz-btn-primary gz-btn-sm text-xs">
                    Search
                </button>

                @if($typeFilter !== 'all' || $skillFilter !== 'all' || $search !== '')
                    <a href="{{ route('open-play.index') }}" class="text-xs font-semibold underline" style="color: var(--gz-danger);">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- Sessions Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($sessions as $session)
                @php
                    $isFull = $session->is_full;
                    $remaining = $session->remaining_slots;
                    $percent = $session->capacity_percent;
                @endphp
                <div class="gz-panel flex flex-col justify-between overflow-hidden border hover:border-black/30 dark:hover:border-white/30 transition-all duration-200"
                     style="background: var(--gz-surface); border-color: var(--gz-border);">
                    <div class="p-5">
                        {{-- Badges --}}
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="gz-badge {{ $session->session_type === 'tournament' ? 'gz-badge-primary' : 'gz-badge-outline' }} text-[10px] uppercase font-bold tracking-wider">
                                {{ str_replace('_', ' ', $session->session_type) }}
                            </span>
                            <span class="gz-badge-outline text-[10px] font-semibold px-2 py-0.5">
                                {{ $session->skill_level }}
                            </span>
                        </div>

                        {{-- Title --}}
                        <h2 class="gz-font-display font-bold text-base line-clamp-2 mb-2" style="color: var(--gz-ink);">
                            <a href="{{ route('open-play.show', $session) }}" class="hover:underline">
                                {{ $session->title }}
                            </a>
                        </h2>

                        {{-- Date & Time --}}
                        <div class="space-y-1.5 text-xs mb-4">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 shrink-0" style="color: var(--gz-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="font-bold" style="color: var(--gz-ink);">{{ $session->date->format('l, M j, Y') }}</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 shrink-0" style="color: var(--gz-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-semibold" style="color: var(--gz-ink);">{{ $session->time_window }}</span>
                                <span class="text-xs" style="color: var(--gz-muted);">({{ $session->duration_hours }} hrs)</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 shrink-0" style="color: var(--gz-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span style="color: var(--gz-muted);">Allocated:</span>
                                <span class="font-semibold" style="color: var(--gz-ink);">{{ $session->allocated_courts_label ?: 'Dedicated Courts' }}</span>
                            </div>
                        </div>

                        {{-- Real-Time Capacity Status --}}
                        <div class="p-3 rounded-xl mb-4" style="background: var(--gz-bg); border: 1px solid var(--gz-border);">
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="font-semibold" style="color: var(--gz-muted);">Live Availability:</span>
                                @if($isFull)
                                    <span class="font-bold text-red-600 dark:text-red-400">Sold Out</span>
                                @else
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ $remaining }} of {{ $session->max_capacity }} slots left
                                    </span>
                                @endif
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-300 {{ $isFull ? 'bg-red-500' : ($remaining <= 3 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                     style="width: {{ $percent }}%;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer: Price & CTA --}}
                    <div class="p-4 border-t flex items-center justify-between" style="border-color: var(--gz-border); background: var(--gz-bg);">
                        <div>
                            <div class="text-[10px] uppercase font-bold tracking-wider" style="color: var(--gz-muted);">Participation Fee</div>
                            <div class="font-mono font-extrabold text-lg" style="color: var(--gz-ink);">
                                ₱{{ number_format($session->price_per_slot, 2) }}
                                <span class="text-[11px] font-normal font-sans" style="color: var(--gz-muted);">/ slot</span>
                            </div>
                        </div>

                        <div>
                            @if($isFull)
                                <span class="gz-badge text-xs px-3 py-1.5 bg-gray-200 dark:bg-gray-800 text-gray-500 cursor-not-allowed">
                                    Sold Out
                                </span>
                            @else
                                <a href="{{ route('open-play.show', $session) }}" class="gz-btn-primary gz-btn-sm text-xs">
                                    Reserve Slot →
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full gz-panel p-12 text-center" style="color: var(--gz-muted);">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p class="font-bold text-base" style="color: var(--gz-ink);">No upcoming sessions found</p>
                    <p class="text-xs mt-1 max-w-md mx-auto">
                        There are currently no Open Play or Tournament events matching your filter. Please check back later or view private court reservations.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('booking') }}" class="gz-btn-outline gz-btn-sm text-xs">
                            Book Private Court Instead
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($sessions->hasPages())
            <div class="mt-8">
                {{ $sessions->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
