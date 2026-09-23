<x-app-layout>
    <x-slot name="header">
        <h1 class="font-pixel text-lg">Reserve a Court</h1>
    </x-slot>

    <div
        x-data='{
            selectedCourt: null,
            selectedDate: "{{ now()->toDateString() }}",
            selectedTimeSlot: null,
            currentMonth: {{ now()->month - 1 }},
            currentYear: {{ now()->year }},
            todayStr: "{{ now()->toDateString() }}",

            courts: @json($courts),
            timeSlots: [
                "6:00 AM - 7:00 AM","7:00 AM - 8:00 AM","8:00 AM - 9:00 AM","9:00 AM - 10:00 AM",
                "10:00 AM - 11:00 AM","11:00 AM - 12:00 PM","12:00 PM - 1:00 PM","1:00 PM - 2:00 PM",
            ],

            // Replace with real data from the backend once BookingController exists:
            // bookedSlots: @json($bookedSlots ?? []),
            bookedSlots: {},

            isBooked(time, courtId) {
                const dayBookings = this.bookedSlots[this.selectedDate] || {};
                const courtBookings = dayBookings[courtId] || [];
                return courtBookings.includes(time);
            },
            isSelected(time, courtId) {
                return this.selectedCourt === courtId && this.selectedTimeSlot === time;
            },
            pickSlot(time, courtId) {
                if (this.isBooked(time, courtId)) return;
                this.selectedCourt = courtId;
                this.selectedTimeSlot = time;
            },
            pickCourt(courtId) {
                if (this.selectedCourt === courtId) {
                    this.selectedCourt = null;
                    this.selectedTimeSlot = null;
                    return;
                }
                this.selectedCourt = courtId;
                this.selectedTimeSlot = null;
            },
            pickDate(date) {
                this.selectedDate = date;
                this.selectedTimeSlot = null;
            },
            get calendarDays() {
                const firstDay = new Date(this.currentYear, this.currentMonth, 1).getDay();
                const daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                const days = [];
                for (let i = 0; i < firstDay; i++) days.push(null);
                for (let d = 1; d <= daysInMonth; d++) days.push(d);
                return days;
            },
            dateStringFor(day) {
                const mm = String(this.currentMonth + 1).padStart(2, "0");
                const dd = String(day).padStart(2, "0");
                return `${this.currentYear}-${mm}-${dd}`;
            },
            isPast(day) {
                return this.dateStringFor(day) < this.todayStr;
            },
            isToday(day) {
                return this.dateStringFor(day) === this.todayStr;
            },
            pickDay(day) {
                if (this.isPast(day)) return;
                this.pickDate(this.dateStringFor(day));
            },
            prevMonth() {
                if (this.currentMonth === 0) {
                    this.currentMonth = 11;
                    this.currentYear--;
                } else {
                    this.currentMonth--;
                }
            },
            nextMonth() {
                if (this.currentMonth === 11) {
                    this.currentMonth = 0;
                    this.currentYear++;
                } else {
                    this.currentMonth++;
                }
            },
            get monthLabel() {
                const names = ["January","February","March","April","May","June","July","August","September","October","November","December"];
                return names[this.currentMonth] + " " + this.currentYear;
            },
            get canPay() {
                return this.selectedCourt !== null && this.selectedDate !== null && this.selectedTimeSlot !== null;
            },
            get selectedCourtName() {
                const c = this.courts.find(c => c.id === this.selectedCourt);
                return c ? c.name : null;
            },

            step: 1,
            paymentMethod: null,
            serviceFee: 25,

            get selectedCourtRate() {
                const c = this.courts.find(c => c.id === this.selectedCourt);
                return c ? c.rate : 0;
            },
            get totalDue() {
                return this.selectedCourtRate + this.serviceFee;
            },
            goToReview() {
                if (this.canPay) this.step = 2;
            },
            goBack() {
                this.step = 1;
            },
            get canConfirm() {
                return this.canPay && this.paymentMethod !== null;
            },

            // Placeholder GCash QR — a decorative pixel pattern with three real
            // QR-style finder squares (corners) and random noise in between.
            // Purely visual: not a working QR code until the real GCash/PayMongo
            // integration is added later.
            gcashQrSize: 17,
            gcashQr: [],
            buildGcashQr() {
                const size = this.gcashQrSize;
                const isFinderCell = (r, c) => {
                    const zones = [[0, 0], [0, size - 7], [size - 7, 0]];
                    for (const [zr, zc] of zones) {
                        if (r >= zr && r < zr + 7 && c >= zc && c < zc + 7) {
                            const lr = r - zr, lc = c - zc;
                            const isBorder = lr === 0 || lr === 6 || lc === 0 || lc === 6;
                            const isInner = lr >= 2 && lr <= 4 && lc >= 2 && lc <= 4;
                            return (isBorder || isInner) ? 1 : 0;
                        }
                    }
                    return null;
                };
                const cells = [];
                for (let r = 0; r < size; r++) {
                    for (let c = 0; c < size; c++) {
                        const finder = isFinderCell(r, c);
                        cells.push(finder !== null ? finder : (Math.random() > 0.55 ? 1 : 0));
                    }
                }
                this.gcashQr = cells;
            },
            init() {
                this.buildGcashQr();
            },
        }'
        class="max-w-6xl mx-auto px-6 py-10"
    >
        <p class="text-lg mb-8" style="color: var(--ink); opacity: 0.7;">
            Pick a court, choose your time, and secure it with online advance payment.
        </p>

        <div x-show="step === 1">

        {{-- Court selection cards --}}
        <section class="mb-10">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <template x-for="court in courts" :key="court.id">
                    <button
                        type="button"
                        @click="pickCourt(court.id)"
                        class="pixel-border text-left overflow-hidden"
                        style="background: var(--cream);"
                    >
                        <div class="h-24 flex items-end p-3" style="background: var(--jade);">
                            <span class="font-pixel text-[11px]" style="color: var(--cream);" x-text="court.name"></span>
                        </div>
                        <div class="p-3 flex items-center justify-between">
                            <div>
                                <p class="text-base" style="opacity: 0.6;">Hourly Rate</p>
                                <p class="text-lg font-bold" x-text="court.rate + ' PHP /hr'"></p>
                            </div>
                            <span
                                class="text-[10px] font-pixel px-3 py-1 pixel-border"
                                :style="selectedCourt === court.id ? 'background: var(--jade); color: var(--cream);' : 'background: var(--parchment); color: var(--ink);'"
                                x-text="selectedCourt === court.id ? 'Selected' : 'Select'"
                            ></span>
                        </div>
                    </button>
                </template>
            </div>
        </section>

        {{-- Date + time --}}
        <section class="pixel-border p-6 mb-10" style="background: var(--cream);">
            <h2 class="font-pixel text-base mb-1">Set Your Date and Time</h2>
            <p class="text-lg mb-6" style="opacity: 0.6;" x-show="selectedCourt">
                Viewing availability for <span class="font-bold" x-text="selectedCourtName"></span>
            </p>
            <p class="text-lg mb-6" style="opacity: 0.6;" x-show="!selectedCourt">
                Select a court above, then choose an open time slot below.
            </p>

            <div
                class="grid grid-cols-1 lg:grid-cols-12 gap-8"
                :style="!selectedCourt ? 'opacity: 0.4; pointer-events: none;' : ''"
            >
                <div class="lg:col-span-4">
                    <div class="pixel-border p-4">
                        <div class="flex items-center justify-between mb-3">
                            <button type="button" @click="prevMonth()" class="pixel-btn text-[10px] px-2 py-1" style="background: var(--parchment);">‹</button>
                            <p class="font-pixel text-[10px]" x-text="monthLabel"></p>
                            <button type="button" @click="nextMonth()" class="pixel-btn text-[10px] px-2 py-1" style="background: var(--parchment);">›</button>
                        </div>
                        <div class="grid grid-cols-7 gap-1 text-base text-center mb-2" style="opacity: 0.5;">
                            <span>S</span><span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span>
                        </div>
                        <div class="grid grid-cols-7 gap-1 text-base">
                            <template x-for="(day, idx) in calendarDays" :key="idx">
                                <button
                                    type="button"
                                    x-show="day !== null"
                                    @click="pickDay(day)"
                                    :disabled="day === null || isPast(day) || !selectedCourt"
                                    class="h-9 flex items-center justify-center pixel-border"
                                    :style="
                                        day !== null && isPast(day)
                                            ? 'background: #ccc; color: #888; box-shadow: none; cursor: not-allowed;'
                                            : (day !== null && selectedDate === dateStringFor(day)
                                                ? 'background: var(--jade); color: var(--cream);'
                                                : (day !== null && isToday(day)
                                                    ? 'background: var(--parchment); border-color: var(--red); border-width: 2px;'
                                                    : 'background: var(--cream);'))
                                    "
                                    x-text="day"
                                ></button>
                            </template>
                        </div>
                        <p class="text-base mt-4" style="opacity: 0.6;">
                            Selected: <span class="font-bold" x-text="selectedDate"></span>
                        </p>
                    </div>
                </div>

                <div class="lg:col-span-8">
                    <div class="pixel-border overflow-x-auto">
                        <table class="w-full text-base border-collapse">
                            <thead>
                                <tr style="background: var(--ink); color: var(--cream);">
                                    <th class="p-2 text-left font-pixel text-[9px]">Time</th>
                                    <template x-for="court in courts" :key="'head'+court.id">
                                        <th class="p-2 font-pixel text-[9px]"
                                            :style="selectedCourt === court.id ? 'background: var(--jade);' : ''"
                                            x-text="court.name"></th>
                                    </template>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="time in timeSlots" :key="time">
                                    <tr style="border-top: 1px solid var(--ink);">
                                        <td class="p-2 font-bold whitespace-nowrap" x-text="time"></td>
                                        <template x-for="court in courts" :key="time + '-' + court.id">
                                            <td class="p-1">
                                                <button
                                                    type="button"
                                                    @click="pickSlot(time, court.id)"
                                                    :disabled="isBooked(time, court.id) || !selectedCourt"
                                                    class="w-full h-9 text-[10px] font-pixel pixel-border"
                                                    :style="
                                                        isBooked(time, court.id)
                                                            ? 'background: #ccc; color: #888; box-shadow: none; cursor: not-allowed;'
                                                            : (isSelected(time, court.id)
                                                                ? 'background: var(--jade); color: var(--cream);'
                                                                : 'background: var(--parchment);')
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

                    <div class="flex items-center gap-6 mt-4 text-base">
                        <span class="flex items-center gap-2">
                            <span class="w-3 h-3 inline-block pixel-border" style="background: var(--parchment);"></span> Available
                        </span>
                        <span class="flex items-center gap-2">
                            <span class="w-3 h-3 inline-block pixel-border" style="background: #ccc;"></span> Booked
                        </span>
                        <span class="flex items-center gap-2">
                            <span class="w-3 h-3 inline-block pixel-border" style="background: var(--jade);"></span> Selected
                        </span>
                    </div>
                </div>
            </div>
        </section>

        @error('time_slot')
            <div class="pixel-border p-4 mb-6" style="background: var(--red); color: var(--cream);">
                {{ $message }}
            </div>
        @enderror

        {{-- Step 1 CTA: just moves to the review step, nothing is saved yet --}}
        <section class="pixel-border p-6 flex flex-col sm:flex-row items-center justify-between gap-4"
                  style="background: var(--ink); color: var(--cream);">
            <div>
                <p class="font-pixel text-sm mb-2">Review your booking</p>
                <p class="text-lg" style="opacity: 0.7;" x-show="canPay">
                    <span x-text="selectedCourtName"></span> ·
                    <span x-text="selectedDate"></span> ·
                    <span x-text="selectedTimeSlot"></span>
                </p>
                <p class="text-lg" style="opacity: 0.5;" x-show="!canPay">
                    Choose a court, a date, and an open time slot to continue.
                </p>
            </div>

            <button type="button" @click="goToReview()" :disabled="!canPay"
                    class="pixel-btn font-pixel text-[10px] px-6 py-3"
                    style="background: var(--gold); color: var(--ink);">
                Next
            </button>
        </section>

        </div>
        {{-- end step 1 --}}

        {{-- ================= STEP 2: SUMMARY + PAYMENT ================= --}}
        <div x-show="step === 2" x-cloak>
            <button type="button" @click="goBack()" class="pixel-btn text-[10px] font-pixel mb-8" style="background: var(--parchment);">
                ‹ Back
            </button>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                {{-- Reservation summary --}}
                <div class="lg:col-span-5">
                    <div class="pixel-border p-6" style="background: var(--cream);">
                        <h2 class="font-pixel text-base mb-6">Reservation Summary</h2>

                        <div class="ledger-row pb-3 mb-3">
                            <p class="text-base" style="opacity: 0.6;">Court</p>
                            <p class="text-lg font-bold" x-text="selectedCourtName"></p>
                        </div>
                        <div class="ledger-row pb-3 mb-3">
                            <p class="text-base" style="opacity: 0.6;">Date</p>
                            <p class="text-lg font-bold" x-text="selectedDate"></p>
                        </div>
                        <div class="ledger-row pb-3 mb-3">
                            <p class="text-base" style="opacity: 0.6;">Time</p>
                            <p class="text-lg font-bold" x-text="selectedTimeSlot"></p>
                        </div>

                        <div class="flex justify-between text-lg mb-2">
                            <span>Court Fee (1 hour)</span>
                            <span x-text="'₱' + selectedCourtRate.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-lg mb-4">
                            <span>Service Fee</span>
                            <span x-text="'₱' + serviceFee.toFixed(2)"></span>
                        </div>

                        <div class="flex justify-between items-center pt-4" style="border-top: 3px solid var(--ink);">
                            <span class="font-pixel text-[10px]">Total Due</span>
                            <span class="font-pixel text-base" style="color: var(--red);" x-text="'₱' + totalDue.toFixed(2)"></span>
                        </div>
                    </div>
                </div>

                {{-- Payment method --}}
                <div class="lg:col-span-7">
                    <div class="pixel-border p-6" style="background: var(--cream);">
                        <h2 class="font-pixel text-base mb-6">Payment Method</h2>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                            <button type="button" @click="paymentMethod = 'gcash'"
                                    class="pixel-border p-4 text-center"
                                    :style="paymentMethod === 'gcash' ? 'background: var(--jade); color: var(--cream);' : 'background: var(--parchment);'">
                                <span class="font-pixel text-[10px] block">GCash</span>
                            </button>
                            <button type="button" @click="paymentMethod = 'card'"
                                    class="pixel-border p-4 text-center"
                                    :style="paymentMethod === 'card' ? 'background: var(--jade); color: var(--cream);' : 'background: var(--parchment);'">
                                <span class="font-pixel text-[10px] block">Card</span>
                            </button>
                            <button type="button" @click="paymentMethod = 'cash'"
                                    class="pixel-border p-4 text-center"
                                    :style="paymentMethod === 'cash' ? 'background: var(--jade); color: var(--cream);' : 'background: var(--parchment);'">
                                <span class="font-pixel text-[10px] block">Cash at Counter</span>
                            </button>
                        </div>

                        {{-- GCash: QR code or manual number entry --}}
                        <div x-show="paymentMethod === 'gcash'" x-cloak class="mb-6 text-center">
                            <div
                                class="pixel-border mx-auto"
                                style="width: 204px; height: 204px; display: grid; grid-template-columns: repeat(17, 1fr); grid-template-rows: repeat(17, 1fr); background: var(--cream);"
                            >
                                <template x-for="(cell, idx) in gcashQr" :key="idx">
                                    <div :style="cell ? 'background: var(--ink);' : 'background: var(--cream);'"></div>
                                </template>
                            </div>
                            <p class="font-pixel text-[10px] mt-4">Scan with GCash</p>
                            <p class="text-base mt-2" style="opacity: 0.5;">or enter your number</p>
                            <div class="mt-4 text-left max-w-xs mx-auto">
                                <x-input-label value="GCash Mobile Number" />
                                <input type="tel" class="pixel-input mt-1" placeholder="09XX XXX XXXX">
                            </div>
                        </div>

                        <div x-show="paymentMethod === 'card'" x-cloak class="mb-6 grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <x-input-label value="Card Number" />
                                <input type="text" class="pixel-input mt-1" placeholder="0000 0000 0000 0000">
                            </div>
                            <div>
                                <x-input-label value="Expiry" />
                                <input type="text" class="pixel-input mt-1" placeholder="MM/YY">
                            </div>
                            <div>
                                <x-input-label value="CVC" />
                                <input type="text" class="pixel-input mt-1" placeholder="123">
                            </div>
                        </div>
                        <div x-show="paymentMethod === 'cash'" x-cloak class="mb-6">
                            <p class="text-lg" style="opacity: 0.7;">
                                Pay in person at the KYMNET front desk when you arrive for your session.
                            </p>
                        </div>

                        {{--
                            This still posts to the exact same endpoint as before. Payment details
                            entered above aren't sent to the backend yet — actually charging a card
                            or verifying a GCash payment needs a real payment gateway integration,
                            which is separate backend work, not something this form can do alone.
                        --}}
                        <form method="POST" action="{{ route('bookings.store') }}">
                            @csrf
                            <input type="hidden" name="court_id" :value="selectedCourt">
                            <input type="hidden" name="date" :value="selectedDate">
                            <input type="hidden" name="time_slot" :value="selectedTimeSlot">
                            <input type="hidden" name="payment_method" :value="paymentMethod">
                            <button type="submit" :disabled="!canConfirm"
                                    class="pixel-btn font-pixel text-[10px] px-6 py-3 w-full"
                                    style="background: var(--gold); color: var(--ink);">
                                Confirm &amp; Pay
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>