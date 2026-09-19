{{--
    resources/views/booking.blade.php

    KYMNET — Court Booking Page
    Stack: Laravel 11 Blade + Tailwind CSS + Alpine.js

    STATE OVERVIEW (Alpine x-data, declared once on the outer wrapper):
      selectedCourt      -> int|null   id of the court chosen from the top cards (1-4)
      selectedDate        -> string|null  'YYYY-MM-DD' of the day chosen in the calendar
      selectedTimeSlot    -> string|null  the time-row label chosen in the grid, e.g. "6:00 AM - 7:00 AM"
      bookedSlots         -> object    lookup: bookedSlots[date][court] = [array of booked time labels]
                                        (this is demo/mock data — in production this would be hydrated
                                        from the backend via @json($bookings) below)

    Everything below is intentionally self-contained (no external Blade @section/@extends assumptions)
    so it can be dropped into a Breeze-style layout or used standalone. Swap the <x-app-layout> wrapper
    at the bottom note if your project already has one.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserve a Court — KYMNET</title>

    {{-- Tailwind (assumes Vite/Tailwind already compiled in the real app; CDN fallback shown for preview) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Pixel-style display font, matching the KYMNET landing page --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --kym-cream: #F1EAD9;
            --kym-cream-dark: #E4DAC0;
            --kym-black: #1C1A16;
            --kym-red: #7A1F1F;
            --kym-red-bright: #9E2A2A;
            --kym-mustard: #D9A94A;
            --kym-green: #2F5D3A;
            --kym-green-bright: #3E7A4C;
        }
        body {
            font-family: 'JetBrains Mono', monospace;
            background-color: var(--kym-cream);
            color: var(--kym-black);
        }
        .font-pixel {
            font-family: 'Press Start 2P', monospace;
        }
        /* Hard pixel-style border + drop shadow, no border-radius anywhere */
        .pixel-border {
            border: 2px solid var(--kym-black);
            box-shadow: 4px 4px 0px 0px var(--kym-black);
            border-radius: 0;
        }
        .pixel-border-sm {
            border: 2px solid var(--kym-black);
            box-shadow: 2px 2px 0px 0px var(--kym-black);
            border-radius: 0;
        }
        .pixel-btn {
            border: 2px solid var(--kym-black);
            box-shadow: 3px 3px 0px 0px var(--kym-black);
            transition: transform 0.05s ease, box-shadow 0.05s ease;
        }
        .pixel-btn:active:not(:disabled) {
            transform: translate(3px, 3px);
            box-shadow: 0px 0px 0px 0px var(--kym-black);
        }
        .pixel-btn:disabled {
            cursor: not-allowed;
            opacity: 0.5;
        }
    </style>
</head>
<body class="min-h-screen">

    {{--
        Root Alpine scope. This single x-data block owns every piece of interactive state
        for the whole booking flow described in the task: court, date, time slot, and the
        derived "can the user pay yet" flag.
    --}}
    <div
        x-data="{
            // --- CORE SELECTION STATE ---
            selectedCourt: null,        // e.g. 1, 2, 3, 4
            selectedDate: '2026-09-19',  // pre-seeded to a default so the grid has something to show
            selectedTimeSlot: null,      // e.g. '6:00 AM - 7:00 AM'

            // --- STATIC DATA (would come from the backend in production) ---
            courts: [
                { id: 1, name: 'Court 1', rate: 500 },
                { id: 2, name: 'Court 2', rate: 500 },
                { id: 3, name: 'Court 3', rate: 500 },
                { id: 4, name: 'Court 4', rate: 500 },
            ],
            timeSlots: [
                '6:00 AM - 7:00 AM',
                '7:00 AM - 8:00 AM',
                '8:00 AM - 9:00 AM',
                '9:00 AM - 10:00 AM',
                '10:00 AM - 11:00 AM',
                '11:00 AM - 12:00 PM',
                '12:00 PM - 1:00 PM',
                '1:00 PM - 2:00 PM',
            ],

            // Mock booked-slot lookup: bookedSlots[date][courtId] = [time labels already taken]
            // In production, replace this literal with the JSON payload injected by the controller,
            // e.g. bookedSlots: @json($bookedSlots ?? []),
            bookedSlots: {
                '2026-09-19': {
                    1: ['6:00 AM - 7:00 AM'],
                    2: ['10:00 AM - 11:00 AM', '11:00 AM - 12:00 PM'],
                    3: [],
                    4: ['1:00 PM - 2:00 PM'],
                },
                '2026-09-20': {
                    1: [],
                    2: [],
                    3: ['9:00 AM - 10:00 AM'],
                    4: [],
                },
            },

            // --- DERIVED HELPERS ---

            // Is this exact (time, court) cell already booked for the selected date?
            isBooked(time, courtId) {
                const dayBookings = this.bookedSlots[this.selectedDate] || {};
                const courtBookings = dayBookings[courtId] || [];
                return courtBookings.includes(time);
            },

            // Is this cell the one the user currently has selected?
            isSelected(time, courtId) {
                return this.selectedCourt === courtId && this.selectedTimeSlot === time;
            },

            // Clicking a cell: ignore booked cells, otherwise set court + time together
            // so a click in the grid also confirms which court's column it belongs to.
            pickSlot(time, courtId) {
                if (this.isBooked(time, courtId)) return;
                this.selectedCourt = courtId;
                this.selectedTimeSlot = time;
            },

            // Clicking a top court card just sets the court and clears any time slot
            // that belonged to a different court, so stale selections can't leak through.
            pickCourt(courtId) {
                if (this.selectedCourt !== courtId) {
                    this.selectedTimeSlot = null;
                }
                this.selectedCourt = courtId;
            },

            // Changing the date invalidates any chosen time slot, since availability is per-day.
            pickDate(date) {
                this.selectedDate = date;
                this.selectedTimeSlot = null;
            },

            // Gate for the Pay Now button: all three selections must be present.
            get canPay() {
                return this.selectedCourt !== null
                    && this.selectedDate !== null
                    && this.selectedTimeSlot !== null;
            },

            get selectedCourtName() {
                const c = this.courts.find(c => c.id === this.selectedCourt);
                return c ? c.name : null;
            },
        }"
        class="min-h-screen flex flex-col"
    >
        {{-- ================= HEADER / NAV ================= --}}
        <header class="border-b-2 border-black bg-[var(--kym-cream)] px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-[var(--kym-red)] pixel-border-sm flex items-center justify-center text-[var(--kym-mustard)] font-pixel text-xs">
                    K
                </div>
                <div>
                    <p class="font-pixel text-sm leading-none">KYMNET</p>
                    <p class="text-[10px] text-[var(--kym-green)] font-bold mt-1">#1 PICKLEBALL COURT ARENA</p>
                </div>
            </div>

            <nav class="hidden md:flex items-center gap-8 text-sm font-bold">
                <a href="{{ url('/') }}" class="hover:text-[var(--kym-red)]">Home</a>
                <a href="{{ url('/booking') }}" class="text-[var(--kym-green)] border-b-2 border-[var(--kym-green)] pb-1">Book Courts</a>
                <a href="{{ url('/bookings') }}" class="hover:text-[var(--kym-red)]">Courts Booked</a>
            </nav>

            <div class="flex items-center gap-4">
                <form method="POST" action="{{ url('/logout') }}">
                    @csrf
                    <button type="submit" class="pixel-btn bg-[var(--kym-black)] text-[var(--kym-cream)] text-xs font-bold px-4 py-2">
                        Logout
                    </button>
                </form>
                <div class="w-9 h-9 pixel-border-sm bg-[var(--kym-mustard)] flex items-center justify-center text-xs font-bold">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
            </div>
        </header>

        <main class="flex-1 px-6 py-10 max-w-6xl mx-auto w-full">

            {{-- ================= PAGE TITLE ================= --}}
            <h1 class="font-pixel text-2xl md:text-3xl mb-2">Reserve a Court</h1>
            <p class="text-sm text-[var(--kym-black)]/70 mb-8">
                Pick a court, choose your time, and secure it with online advance payment.
            </p>

            {{-- ================= COURT SELECTION CARDS ================= --}}
            <section class="mb-10">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <template x-for="court in courts" :key="court.id">
                        <button
                            type="button"
                            @click="pickCourt(court.id)"
                            class="pixel-border bg-white text-left overflow-hidden"
                            :class="selectedCourt === court.id ? 'ring-4 ring-[var(--kym-green)]' : ''"
                        >
                            <div class="h-24 bg-[var(--kym-green)] flex items-end p-3">
                                <span class="font-pixel text-[11px] text-white" x-text="court.name"></span>
                            </div>
                            <div class="p-3 flex items-center justify-between">
                                <div>
                                    <p class="text-[10px] text-[var(--kym-black)]/60">Hourly Rate</p>
                                    <p class="text-sm font-bold" x-text="court.rate + ' PHP /hr'"></p>
                                </div>
                                <span
                                    class="text-xs font-bold px-3 py-1 pixel-border-sm"
                                    :class="selectedCourt === court.id
                                        ? 'bg-[var(--kym-green)] text-white'
                                        : 'bg-[var(--kym-cream)] text-[var(--kym-black)]'"
                                    x-text="selectedCourt === court.id ? 'Selected' : 'Select'"
                                ></span>
                            </div>
                        </button>
                    </template>
                </div>
            </section>

            {{-- ================= UNIFIED DATE + TIME DASHBOARD ================= --}}
            <section class="pixel-border bg-white p-6 mb-10">
                <h2 class="font-pixel text-base mb-1">Set Your Date and Time</h2>
                <p class="text-xs text-[var(--kym-black)]/60 mb-6" x-show="selectedCourt">
                    Viewing availability for <span class="font-bold" x-text="selectedCourtName"></span>
                </p>
                <p class="text-xs text-[var(--kym-black)]/60 mb-6" x-show="!selectedCourt">
                    Select a court above, then choose an open time slot below.
                </p>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                    {{-- ---- CALENDAR (left, 4/12 cols) ---- --}}
                    <div class="lg:col-span-4">
                        <div class="pixel-border-sm p-4">
                            <p class="text-xs font-bold mb-3">Choose a date</p>
                            <div class="grid grid-cols-7 gap-1 text-[10px] text-center mb-2 text-[var(--kym-black)]/50">
                                <span>S</span><span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span>
                            </div>
                            {{-- Demo static month grid; wire to a real date library in production --}}
                            <div class="grid grid-cols-7 gap-1 text-xs">
                                <template x-for="day in [null,null,1,2,3,4,5]" :key="'a'+day">
                                    <span class="h-8 flex items-center justify-center text-[var(--kym-black)]/30" x-text="day"></span>
                                </template>
                                <template x-for="day in [6,7,8,9,10,11,12]" :key="'b'+day">
                                    <span class="h-8 flex items-center justify-center" x-text="day"></span>
                                </template>
                                <template x-for="day in [13,14,15,16,17,18,19]" :key="'c'+day">
                                    <button
                                        type="button"
                                        @click="pickDate('2026-09-' + String(day).padStart(2,'0'))"
                                        class="h-8 flex items-center justify-center pixel-border-sm"
                                        :class="selectedDate === '2026-09-' + String(day).padStart(2,'0')
                                            ? 'bg-[var(--kym-green)] text-white'
                                            : 'bg-white hover:bg-[var(--kym-cream-dark)]'"
                                        x-text="day"
                                    ></button>
                                </template>
                                <template x-for="day in [20,21,22,23,24,25,26]" :key="'d'+day">
                                    <button
                                        type="button"
                                        @click="pickDate('2026-09-' + String(day).padStart(2,'0'))"
                                        class="h-8 flex items-center justify-center pixel-border-sm"
                                        :class="selectedDate === '2026-09-' + String(day).padStart(2,'0')
                                            ? 'bg-[var(--kym-green)] text-white'
                                            : 'bg-white hover:bg-[var(--kym-cream-dark)]'"
                                        x-text="day"
                                    ></button>
                                </template>
                            </div>
                            <p class="text-[10px] mt-4 text-[var(--kym-black)]/60">
                                Selected: <span class="font-bold" x-text="selectedDate"></span>
                            </p>
                        </div>
                    </div>

                    {{-- ---- TIME-SLOT MATRIX (right, 8/12 cols) ---- --}}
                    <div class="lg:col-span-8">
                        <div class="pixel-border-sm overflow-x-auto">
                            <table class="w-full text-xs border-collapse">
                                <thead>
                                    <tr class="bg-[var(--kym-black)] text-[var(--kym-cream)]">
                                        <th class="p-2 text-left font-bold">Time</th>
                                        <template x-for="court in courts" :key="'head'+court.id">
                                            <th
                                                class="p-2 font-bold transition-colors"
                                                :class="selectedCourt === court.id ? 'bg-[var(--kym-green)]' : ''"
                                                x-text="court.name"
                                            ></th>
                                        </template>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="time in timeSlots" :key="time">
                                        <tr class="border-t border-black/10">
                                            <td class="p-2 font-bold whitespace-nowrap" x-text="time"></td>
                                            <template x-for="court in courts" :key="time + '-' + court.id">
                                                <td
                                                    class="p-1 transition-colors"
                                                    :class="selectedCourt === court.id ? 'bg-[var(--kym-green)]/10' : ''"
                                                >
                                                    <button
                                                        type="button"
                                                        @click="pickSlot(time, court.id)"
                                                        :disabled="isBooked(time, court.id)"
                                                        class="w-full h-9 text-[10px] font-bold pixel-border-sm"
                                                        :class="
                                                            isBooked(time, court.id)
                                                                ? 'bg-gray-300 text-gray-500 cursor-not-allowed border-gray-400 shadow-none'
                                                                : (isSelected(time, court.id)
                                                                    ? 'bg-green-500 text-white'
                                                                    : 'bg-white hover:bg-[var(--kym-cream-dark)]')
                                                        "
                                                        x-text="isBooked(time, court.id) ? 'Booked' : (isSelected(time, court.id) ? 'Selected' : 'Open')"
                                                    ></button>
                                                </td>
                                            </template>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        {{-- Legend --}}
                        <div class="flex items-center gap-6 mt-4 text-[10px]">
                            <span class="flex items-center gap-2">
                                <span class="w-3 h-3 bg-white pixel-border-sm inline-block"></span> Available
                            </span>
                            <span class="flex items-center gap-2">
                                <span class="w-3 h-3 bg-gray-300 pixel-border-sm inline-block"></span> Booked
                            </span>
                            <span class="flex items-center gap-2">
                                <span class="w-3 h-3 bg-green-500 pixel-border-sm inline-block"></span> Selected
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ================= PAYMENT SUMMARY / CTA ================= --}}
            <section class="pixel-border bg-[var(--kym-black)] text-[var(--kym-cream)] p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <p class="font-pixel text-sm mb-2">Proceed to payment</p>
                    <p class="text-xs text-[var(--kym-cream)]/70" x-show="canPay">
                        <span x-text="selectedCourtName"></span> ·
                        <span x-text="selectedDate"></span> ·
                        <span x-text="selectedTimeSlot"></span>
                    </p>
                    <p class="text-xs text-[var(--kym-cream)]/50" x-show="!canPay">
                        Choose a court, a date, and an open time slot to continue.
                    </p>
                </div>

                {{--
                    This form posts the confirmed selection to the backend booking endpoint.
                    Route/controller validation still applies server-side — this client-side
                    gate only prevents an incomplete submission, it is not the source of truth.
                --}}
                <form method="POST" action="{{ url('/bookings') }}">
                    @csrf
                    <input type="hidden" name="court_id" :value="selectedCourt">
                    <input type="hidden" name="date" :value="selectedDate">
                    <input type="hidden" name="time_slot" :value="selectedTimeSlot">
                    <button
                        type="submit"
                        :disabled="!canPay"
                        class="pixel-btn bg-[var(--kym-mustard)] text-[var(--kym-black)] font-bold text-sm px-6 py-3"
                    >
                        Pay Now
                    </button>
                </form>
            </section>
        </main>

        {{-- ================= FOOTER ================= --}}
        <footer class="bg-[var(--kym-black)] text-[var(--kym-cream)] px-6 py-10 mt-auto">
            <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <p class="font-pixel text-sm mb-2">KYMNET</p>
                    <p class="text-xs text-[var(--kym-cream)]/60">
                        Premium court booking software designed for modern players.
                        Real-time availability, hassle-free reservations, and instant match setups.
                    </p>
                </div>
                <div>
                    <p class="text-xs font-bold mb-3">Explore</p>
                    <ul class="text-xs text-[var(--kym-cream)]/60 space-y-2">
                        <li><a href="#" class="hover:text-white">Find Courts</a></li>
                        <li><a href="#" class="hover:text-white">Host Tournament</a></li>
                        <li><a href="#" class="hover:text-white">Find Partners</a></li>
                    </ul>
                </div>
                <div>
                    <p class="text-xs font-bold mb-3">For Owners</p>
                    <ul class="text-xs text-[var(--kym-cream)]/60 space-y-2">
                        <li><a href="#" class="hover:text-white">List a Club</a></li>
                        <li><a href="#" class="hover:text-white">Software Features</a></li>
                        <li><a href="#" class="hover:text-white">Pricing</a></li>
                    </ul>
                </div>
                <div>
                    <p class="text-xs font-bold mb-3">Company</p>
                    <ul class="text-xs text-[var(--kym-cream)]/60 space-y-2">
                        <li><a href="#" class="hover:text-white">About Us</a></li>
                        <li><a href="#" class="hover:text-white">Contact Support</a></li>
                        <li><a href="#" class="hover:text-white">Careers</a></li>
                    </ul>
                </div>
            </div>
            <div class="max-w-6xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2 mt-8 pt-6 border-t border-white/10 text-[10px] text-[var(--kym-cream)]/40">
                <p>&copy; 2026 KYMNET Inc. All rights reserved.</p>
                <div class="flex gap-4">
                    <a href="#" class="hover:text-white">Privacy Policy</a>
                    <a href="#" class="hover:text-white">Terms of Service</a>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>