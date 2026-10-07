<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs mb-1" style="color: var(--gz-muted);">
                    <a href="{{ route('admin.open-play.index') }}" class="hover:underline">Open Play & Tournaments</a>
                    <span>/</span>
                    <span>{{ $session->exists ? 'Edit Session' : 'Create Session' }}</span>
                </div>
                <h1 class="gz-font-display font-bold text-xl sm:text-2xl">
                    {{ $session->exists ? 'Edit Session: ' . $session->title : 'Create Open Play / Tournament Session' }}
                </h1>
                <p class="text-xs mt-1" style="color: var(--gz-muted);">
                    Configure communal player pool events, allocate courts, set max slot capacities and per-player participation fees.
                </p>
            </div>
            <a href="{{ route('admin.open-play.index') }}" class="gz-btn-outline gz-btn-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Back to Sessions</span>
            </a>
        </div>
    </x-slot>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl text-sm" style="background: var(--gz-danger-bg); color: var(--gz-danger);" role="alert">
            <p class="font-bold mb-1">Please correct the following errors:</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="gz-panel p-6 max-w-4xl mx-auto">
        <form method="POST" action="{{ $session->exists ? route('admin.open-play.update', $session) : route('admin.open-play.store') }}">
            @csrf
            @if($session->exists)
                @method('PUT')
            @endif

            <div class="space-y-6">
                {{-- Session Title & Type --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label for="title" class="block text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--gz-muted);">
                            Session Title <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="{{ old('title', $session->title) }}"
                            placeholder="e.g. Friday Night Open Play (Intermediate 3.5+)"
                            required
                            class="gz-input w-full text-sm"
                        >
                    </div>

                    <div>
                        <label for="session_type" class="block text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--gz-muted);">
                            Event Type <span class="text-red-500">*</span>
                        </label>
                        <select id="session_type" name="session_type" required class="gz-select w-full text-sm">
                            <option value="open_play" {{ old('session_type', $session->session_type) === 'open_play' ? 'selected' : '' }}>
                                Open Play (Communal Pool)
                            </option>
                            <option value="tournament" {{ old('session_type', $session->session_type) === 'tournament' ? 'selected' : '' }}>
                                Tournament / Bracket
                            </option>
                        </select>
                    </div>
                </div>

                {{-- Date, Start Time, End Time --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="date" class="block text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--gz-muted);">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="date"
                            id="date"
                            name="date"
                            value="{{ old('date', $session->date ? $session->date->format('Y-m-d') : '') }}"
                            required
                            class="gz-input w-full text-sm"
                        >
                    </div>

                    <div>
                        <label for="start_time" class="block text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--gz-muted);">
                            Start Time <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="time"
                            id="start_time"
                            name="start_time"
                            value="{{ old('start_time', $session->start_time ? substr($session->start_time, 0, 5) : '18:00') }}"
                            required
                            class="gz-input w-full text-sm"
                        >
                    </div>

                    <div>
                        <label for="end_time" class="block text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--gz-muted);">
                            End Time <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="time"
                            id="end_time"
                            name="end_time"
                            value="{{ old('end_time', $session->end_time ? substr($session->end_time, 0, 5) : '21:00') }}"
                            required
                            class="gz-input w-full text-sm"
                        >
                    </div>
                </div>

                {{-- Court Allocation --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--gz-muted);">
                        Allocated Courts <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs mb-3" style="color: var(--gz-muted);">
                        Select which courts are reserved for this communal event. These courts will be blocked from private group booking during this time window.
                    </p>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        @foreach($courts as $court)
                            @php
                                $checked = in_array($court->id, old('allocated_courts', $allocatedCourtIds ?? []));
                            @endphp
                            <label class="flex items-center gap-2 p-3 rounded-xl border cursor-pointer hover:bg-black/5 dark:hover:bg-white/5 transition"
                                   style="border-color: var(--gz-border); background: var(--gz-surface);">
                                <input
                                    type="checkbox"
                                    name="allocated_courts[]"
                                    value="{{ $court->id }}"
                                    {{ $checked ? 'checked' : '' }}
                                    class="rounded text-emerald-600 focus:ring-emerald-500"
                                >
                                <span class="text-xs font-bold" style="color: var(--gz-ink);">{{ $court->court_name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('allocated_courts')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Max Capacity, Skill Level & Price Per Slot --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="max_capacity" class="block text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--gz-muted);">
                            Max Capacity (Player Slots) <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            id="max_capacity"
                            name="max_capacity"
                            min="2"
                            max="100"
                            value="{{ old('max_capacity', $session->max_capacity ?? 12) }}"
                            required
                            class="gz-input w-full text-sm"
                        >
                        <p class="text-[11px] mt-1" style="color: var(--gz-muted);">
                            Typically 4 to 6 player slots per allocated court.
                        </p>
                    </div>

                    <div>
                        <label for="skill_level" class="block text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--gz-muted);">
                            Target Skill Level <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="skill_level"
                            name="skill_level"
                            value="{{ old('skill_level', $session->skill_level ?? 'All Levels') }}"
                            placeholder="e.g. Beginner 2.0-3.0, Intermediate 3.5+, All Levels"
                            required
                            class="gz-input w-full text-sm"
                            list="skill_levels_list"
                        >
                        <datalist id="skill_levels_list">
                            <option value="All Levels">
                            <option value="Beginner (2.0 - 2.5)">
                            <option value="Intermediate (3.0 - 3.5)">
                            <option value="Advanced (4.0+)">
                            <option value="Tournament Open">
                        </datalist>
                    </div>

                    <div>
                        <label for="price_per_slot" class="block text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--gz-muted);">
                            Price Per Player Slot (₱) <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            id="price_per_slot"
                            name="price_per_slot"
                            value="{{ old('price_per_slot', $session->price_per_slot ?? 150.00) }}"
                            required
                            class="gz-input w-full text-sm font-mono font-bold"
                        >
                        <p class="text-[11px] mt-1" style="color: var(--gz-muted);">
                            Individual player participation fee (not full court fee).
                        </p>
                    </div>
                </div>

                {{-- If Editing: Status --}}
                @if($session->exists)
                    <div>
                        <label for="session_status" class="block text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--gz-muted);">
                            Session Status
                        </label>
                        <select id="session_status" name="session_status" class="gz-select w-full sm:w-64 text-sm">
                            <option value="scheduled" {{ old('session_status', $session->session_status) === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="ongoing" {{ old('session_status', $session->session_status) === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="completed" {{ old('session_status', $session->session_status) === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('session_status', $session->session_status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                @endif

                {{-- Rules / Details --}}
                <div>
                    <label for="details" class="block text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--gz-muted);">
                        Event Details & Rotation Format (Optional)
                    </label>
                    <textarea
                        id="details"
                        name="details"
                        rows="3"
                        placeholder="e.g. Round-robin rotation format with 11-point games. Paddles and balls provided. Warm up starts 15 mins prior."
                        class="gz-input w-full text-sm"
                    >{{ old('details', $session->details) }}</textarea>
                </div>

                {{-- Actions --}}
                <div class="pt-4 border-t flex items-center justify-end gap-3" style="border-color: var(--gz-border);">
                    <a href="{{ route('admin.open-play.index') }}" class="gz-btn-outline text-xs">
                        Cancel
                    </a>
                    <button type="submit" class="gz-btn-primary text-xs">
                        {{ $session->exists ? 'Save Changes' : 'Publish Open Play Session' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-admin-layout>
