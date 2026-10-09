<x-app-layout title="Host an Open Play Session — KYMNET">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-6">
        {{-- Breadcrumb & Title --}}
        <div class="mb-6">
            <div class="flex items-center gap-2 text-xs mb-2" style="color: var(--gz-muted);">
                <a href="{{ route('open-play.index') }}" class="hover:underline">Open Play</a>
                <span>/</span>
                <span class="font-semibold" style="color: var(--gz-ink);">Host an Open Play</span>
            </div>
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h1 class="gz-font-display font-extrabold text-2xl sm:text-3xl tracking-tight" style="color: var(--gz-ink);">
                        Host an Open Play Session
                    </h1>
                    <p class="text-sm mt-1" style="color: var(--gz-muted);">
                        Submit your session details for manager approval. Once approved, you can pay the court fee to secure your appointment.
                    </p>
                </div>
                <a href="{{ route('open-play.index') }}" class="gz-btn-outline gz-btn-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Back to Sessions</span>
                </a>
            </div>
        </div>

        {{-- How It Works Card --}}
        <div class="gz-panel p-4 mb-6" style="background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.2);">
            <div class="flex items-start gap-3">
                <div class="p-2 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-xs space-y-1" style="color: var(--gz-ink);">
                    <p class="font-bold text-sm">How Hosting Works</p>
                    <p style="color: var(--gz-muted);">
                        1. <strong>Submit Request:</strong> Select date, time, and courts. Only Open Play sessions may be hosted by players.
                    </p>
                    <p style="color: var(--gz-muted);">
                        2. <strong>Manager Review:</strong> A facility manager inspects court schedule and accepts or declines your request.
                    </p>
                    <p style="color: var(--gz-muted);">
                        3. <strong>Court Payment:</strong> Once accepted, you pay the court hire fee (online via PayMongo or cash at desk) to secure the appointment and publish it for players to join.
                    </p>
                </div>
            </div>
        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl text-sm" style="background: var(--gz-danger-bg); color: var(--gz-danger);" role="alert">
                <p class="font-bold mb-1">Please correct the following errors:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Hosting Form --}}
        <div class="gz-panel p-6">
            <form method="POST" action="{{ route('open-play.host.store') }}" id="hostForm">
                @csrf
                <input type="hidden" name="session_type" value="open_play">

                <div class="space-y-6">
                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--gz-muted);">
                            Session Title <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="{{ old('title', $session->title) }}"
                            placeholder="e.g. Saturday Morning Open Play (All Levels)"
                            required
                            class="gz-input w-full text-sm"
                        >
                        <p class="text-[11px] mt-1" style="color: var(--gz-muted);">
                            Give your communal play session a descriptive title.
                        </p>
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
                                min="{{ today()->toDateString() }}"
                                value="{{ old('date', $session->date ? $session->date->format('Y-m-d') : today()->addDay()->toDateString()) }}"
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

                    {{-- Court Selection --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold uppercase tracking-wider" style="color: var(--gz-muted);">
                                Select Courts to Reserve <span class="text-red-500">*</span>
                            </label>
                            <span class="text-[11px]" style="color: var(--gz-muted);">Hourly rates apply for appointment fee</span>
                        </div>
                        <p class="text-xs mb-3" style="color: var(--gz-muted);">
                            Choose one or more courts for your Open Play session. Court hire fees will be calculated based on duration and hourly rates.
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($courts as $court)
                                @php
                                    $checked = in_array($court->id, old('allocated_courts', []));
                                @endphp
                                <label class="court-choice flex items-start gap-3 p-3.5 rounded-xl border cursor-pointer hover:bg-black/5 dark:hover:bg-white/5 transition"
                                       style="border-color: var(--gz-border); background: var(--gz-surface);">
                                    <input
                                        type="checkbox"
                                        name="allocated_courts[]"
                                        value="{{ $court->id }}"
                                        data-rate="{{ $court->price_per_hour }}"
                                        {{ $checked ? 'checked' : '' }}
                                        class="court-checkbox mt-0.5 rounded text-emerald-600 focus:ring-emerald-500"
                                    >
                                    <div class="flex-1 min-w-0">
                                        <div class="text-xs font-bold" style="color: var(--gz-ink);">{{ $court->court_name }}</div>
                                        <div class="text-[11px] mt-0.5" style="color: var(--gz-muted);">
                                            ₱{{ number_format($court->price_per_hour, 2) }}/hr
                                            @if($court->court_type)
                                                • {{ ucfirst($court->court_type) }}
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Capacity, Ticket Price, Skill Level --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="max_capacity" class="block text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--gz-muted);">
                                Max Player Capacity <span class="text-red-500">*</span>
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
                            <p class="text-[11px] mt-1" style="color: var(--gz-muted);">Total players allowed to sign up.</p>
                        </div>

                        <div>
                            <label for="price_per_slot" class="block text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--gz-muted);">
                                Fee Per Player (₱) <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="price_per_slot"
                                name="price_per_slot"
                                min="0"
                                step="0.01"
                                value="{{ old('price_per_slot', $session->price_per_slot ?? 150.00) }}"
                                required
                                class="gz-input w-full text-sm"
                            >
                            <p class="text-[11px] mt-1" style="color: var(--gz-muted);">Ticket cost per participating player.</p>
                        </div>

                        <div>
                            <label for="skill_level" class="block text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--gz-muted);">
                                Skill Level <span class="text-red-500">*</span>
                            </label>
                            <select id="skill_level" name="skill_level" required class="gz-select w-full text-sm">
                                @php $currentSkill = old('skill_level', $session->skill_level ?? 'All Levels'); @endphp
                                <option value="All Levels" {{ $currentSkill === 'All Levels' ? 'selected' : '' }}>All Levels</option>
                                <option value="Beginner (1.0 - 2.5)" {{ $currentSkill === 'Beginner (1.0 - 2.5)' ? 'selected' : '' }}>Beginner (1.0 - 2.5)</option>
                                <option value="Intermediate (3.0 - 3.5)" {{ $currentSkill === 'Intermediate (3.0 - 3.5)' ? 'selected' : '' }}>Intermediate (3.0 - 3.5)</option>
                                <option value="Advanced (4.0+)" {{ $currentSkill === 'Advanced (4.0+)' ? 'selected' : '' }}>Advanced (4.0+)</option>
                            </select>
                            <p class="text-[11px] mt-1" style="color: var(--gz-muted);">Expected player level range.</p>
                        </div>
                    </div>

                    {{-- Details / Description --}}
                    <div>
                        <label for="details" class="block text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--gz-muted);">
                            Session Notes & Guidelines (Optional)
                        </label>
                        <textarea
                            id="details"
                            name="details"
                            rows="3"
                            placeholder="Provide rotational format (e.g. King of the Court, round-robin), paddle rules, or what to bring..."
                            class="gz-input w-full text-sm"
                        >{{ old('details', $session->details) }}</textarea>
                    </div>

                    {{-- Live Fee Estimation Card --}}
                    <div class="p-4 rounded-xl border" style="background: var(--gz-surface); border-color: var(--gz-border);">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider" style="color: var(--gz-muted);">Estimated Court Appointment Fee</span>
                                <p class="text-xs mt-0.5" style="color: var(--gz-muted);">
                                    Calculated as <span id="summary-duration">3.0</span> hrs × <span id="summary-courts-count">0</span> court(s). Due upon manager approval.
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="gz-font-display text-2xl font-extrabold text-emerald-600 dark:text-emerald-400" id="estimated-total">
                                    ₱0.00
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Form Submission --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t" style="border-color: var(--gz-border);">
                        <a href="{{ route('open-play.index') }}" class="gz-btn-outline text-sm">
                            Cancel
                        </a>
                        <button type="submit" class="gz-btn-primary text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Submit Request for Manager Approval</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Live Fee Calculator Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const startTimeInput = document.getElementById('start_time');
            const endTimeInput = document.getElementById('end_time');
            const courtCheckboxes = document.querySelectorAll('.court-checkbox');
            const summaryDuration = document.getElementById('summary-duration');
            const summaryCourtsCount = document.getElementById('summary-courts-count');
            const estimatedTotal = document.getElementById('estimated-total');

            function calculateFee() {
                const startTime = startTimeInput.value;
                const endTime = endTimeInput.value;

                let durationHours = 0;
                if (startTime && endTime) {
                    const [sH, sM] = startTime.split(':').map(Number);
                    const [eH, eM] = endTime.split(':').map(Number);
                    const startMin = sH * 60 + sM;
                    const endMin = eH * 60 + eM;
                    if (endMin > startMin) {
                        durationHours = (endMin - startMin) / 60;
                    }
                }

                let totalHourlyRate = 0;
                let courtsCount = 0;
                courtCheckboxes.forEach(cb => {
                    if (cb.checked) {
                        courtsCount++;
                        totalHourlyRate += parseFloat(cb.dataset.rate || 0);
                    }
                });

                summaryDuration.textContent = durationHours.toFixed(1);
                summaryCourtsCount.textContent = courtsCount;

                const fee = durationHours * totalHourlyRate;
                estimatedTotal.textContent = '₱' + fee.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            startTimeInput.addEventListener('change', calculateFee);
            endTimeInput.addEventListener('change', calculateFee);
            courtCheckboxes.forEach(cb => cb.addEventListener('change', calculateFee));

            calculateFee();
        });
    </script>
</x-app-layout>
