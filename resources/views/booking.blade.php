<x-app-layout>
    <x-slot name="header">
        <h1 class="gz-font-display font-bold text-lg">Reserve a Court</h1>
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

            bookedSlots: @json($bookedSlots ?? []),

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
                if (this.canPay) {
                    this.step = 2;
                    this.$nextTick(() => this.$refs.step2Heading?.focus());
                }
            },
            goBack() {
                this.step = 1;
                this.$nextTick(() => this.$refs.step1Heading?.focus());
            },
            get canConfirm() {
                return this.canPay && this.paymentMethod !== null;
            },
        }'
        class="gz-container"
    >
        <p class="text-base mb-8" style="color: var(--gz-muted);">
            Pick a court, choose your time, and secure it with online advance payment.
        </p>

        <div x-show="step === 1">

        <section class="gz-panel gz-panel-body mb-8">
            <h2 class="gz-font-display font-bold text-base mb-1" tabindex="-1" x-ref="step1Heading">Set Your Date and Time</h2>
            <p class="text-sm mb-6" style="color: var(--gz-muted);" role="status" aria-live="polite">
                <span x-show="selectedCourt && selectedTimeSlot">
                    Selected: <span class="font-semibold" style="color: var(--gz-pop-dark);" x-text="selectedCourtName"></span> at <span class="font-semibold" style="color: var(--gz-pop-dark);" x-text="selectedTimeSlot"></span> on <span class="font-semibold" style="color: var(--gz-pop-dark);" x-text="selectedDate"></span>
                </span>
                <span x-show="!selectedCourt || !selectedTimeSlot">
                    Choose a date on the calendar, then select an open court time slot below.
                </span>
            </p>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <div class="lg:col-span-4">
                    <div class="gz-kpi-card">
                        <div class="flex items-center justify-between mb-3">
                            <button type="button" @click="prevMonth()" class="gz-btn-outline gz-btn-sm" aria-label="Previous month">‹</button>
                            <p class="gz-font-display font-bold text-sm" x-text="monthLabel" aria-live="polite"></p>
                            <button type="button" @click="nextMonth()" class="gz-btn-outline gz-btn-sm" aria-label="Next month">›</button>
                        </div>
                        <div class="grid grid-cols-7 gap-1 text-xs text-center mb-2" style="color: var(--gz-muted);" aria-hidden="true">
                            <span>S</span><span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span>
                        </div>
                        <div class="grid grid-cols-7 gap-1 text-sm" role="grid" aria-label="Choose a date">
                            <template x-for="(day, idx) in calendarDays" :key="idx">
                                <button
                                    type="button"
                                    x-show="day !== null"
                                    @click="pickDay(day)"
                                    :disabled="day === null || isPast(day)"
                                    :aria-current="day !== null && isToday(day) ? 'date' : null"
                                    :aria-pressed="day !== null && selectedDate === dateStringFor(day) ? 'true' : 'false'"
                                    :aria-label="day !== null ? (isToday(day) ? day + ', today' : day) : null"
                                    class="h-9 flex items-center justify-center rounded-xl"
                                    :style="
                                        day !== null && isPast(day)
                                            ? 'background: transparent; color: var(--gz-border); cursor: not-allowed;'
                                            : (day !== null && selectedDate === dateStringFor(day)
                                                ? 'background: var(--gz-pop); color: var(--gz-ink); font-weight: 700;'
                                                : (day !== null && isToday(day)
                                                    ? 'background: var(--gz-bg); border: 1.5px solid var(--gz-pop-dark); font-weight: 700;'
                                                    : 'background: transparent; border: 1px solid transparent;'))
                                    "
                                    x-text="day"
                                ></button>
                            </template>
                        </div>
                        <p class="text-xs mt-4" style="color: var(--gz-muted);">
                            Selected date: <span class="font-semibold" x-text="selectedDate" style="color: var(--gz-ink);"></span>
                        </p>
                    </div>
                </div>

                <div class="lg:col-span-8">
                    <div class="gz-panel overflow-x-auto">
                        <table class="gz-table">
                            <caption class="sr-only">Court availability by time slot. Select an open slot to book it.</caption>
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <template x-for="court in courts" :key="'head'+court.id">
                                        <th class="text-center"
                                            :style="selectedCourt === court.id ? 'color: var(--gz-pop-dark);' : ''">
                                            <span x-text="court.name"></span>
                                            <span class="block text-[11px] font-normal" style="color: var(--gz-muted);" x-text="'₱' + court.rate + '/hr'"></span>
                                        </th>
                                    </template>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="time in timeSlots" :key="time">
                                    <tr>
                                        <th scope="row" class="text-left font-semibold whitespace-nowrap" x-text="time"></th>
                                        <template x-for="court in courts" :key="time + '-' + court.id">
                                            <td class="p-1.5">
                                                <button
                                                    type="button"
                                                    @click="pickSlot(time, court.id)"
                                                    :disabled="isBooked(time, court.id)"
                                                    :aria-pressed="isSelected(time, court.id) ? 'true' : 'false'"
                                                    :aria-label="court.name + ' at ' + time + ': ' + (isBooked(time, court.id) ? 'booked' : (isSelected(time, court.id) ? 'selected' : 'open'))"
                                                    class="w-full h-9 text-xs font-semibold rounded-xl transition"
                                                    :style="
                                                        isBooked(time, court.id)
                                                            ? 'background: var(--gz-bg); color: var(--gz-muted); cursor: not-allowed; border: 1px solid var(--gz-border);'
                                                            : (isSelected(time, court.id)
                                                                ? 'background: var(--gz-pop); color: var(--gz-ink); cursor: pointer;'
                                                                : 'background: var(--gz-surface); border: 1px solid var(--gz-border); cursor: pointer;')
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

                    <div class="flex items-center gap-6 mt-4 text-xs" style="color: var(--gz-muted);">
                        <span class="flex items-center gap-2">
                            <span class="w-3 h-3 inline-block rounded" style="background: var(--gz-surface); border: 1px solid var(--gz-border);" aria-hidden="true"></span> Available
                        </span>
                        <span class="flex items-center gap-2">
                            <span class="w-3 h-3 inline-block rounded" style="background: var(--gz-bg); border: 1px solid var(--gz-border);" aria-hidden="true"></span> Booked
                        </span>
                        <span class="flex items-center gap-2">
                            <span class="w-3 h-3 inline-block rounded" style="background: var(--gz-pop);" aria-hidden="true"></span> Selected
                        </span>
                    </div>
                </div>
            </div>
        </section>

        @error('time_slot')
            <div class="mb-6 p-4 rounded-2xl" style="background: var(--gz-danger-bg); color: var(--gz-danger);" role="alert">
                {{ $message }}
            </div>
        @enderror

        <section class="gz-panel gz-panel-body flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <p class="gz-font-display font-bold text-sm mb-1">Review your booking</p>
                <p class="text-sm" style="color: var(--gz-muted);" x-show="canPay">
                    <span x-text="selectedCourtName"></span> ·
                    <span x-text="selectedDate"></span> ·
                    <span x-text="selectedTimeSlot"></span>
                </p>
                <p class="text-sm" style="color: var(--gz-muted);" x-show="!canPay">
                    Choose a court, a date, and an open time slot to continue.
                </p>
            </div>

            <button type="button" @click="goToReview()" :disabled="!canPay" class="gz-btn-primary">
                Next
            </button>
        </section>

        </div>

        <div x-show="step === 2" x-cloak>
            <button type="button" @click="goBack()" class="gz-btn-outline mb-8">
                ‹ Back
            </button>

            <h2 class="sr-only" tabindex="-1" x-ref="step2Heading">Review and pay for your booking</h2>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                <div class="lg:col-span-5">
                    <div class="gz-panel gz-panel-body">
                        <h3 class="gz-font-display font-bold text-base mb-6">Reservation Summary</h3>

                        <div class="pb-3 mb-3 border-b" style="border-color: var(--gz-border);">
                            <p class="text-xs" style="color: var(--gz-muted);">Court</p>
                            <p class="text-base font-semibold" x-text="selectedCourtName"></p>
                        </div>
                        <div class="pb-3 mb-3 border-b" style="border-color: var(--gz-border);">
                            <p class="text-xs" style="color: var(--gz-muted);">Date</p>
                            <p class="text-base font-semibold" x-text="selectedDate"></p>
                        </div>
                        <div class="pb-3 mb-3 border-b" style="border-color: var(--gz-border);">
                            <p class="text-xs" style="color: var(--gz-muted);">Time</p>
                            <p class="text-base font-semibold" x-text="selectedTimeSlot"></p>
                        </div>

                        <div class="flex justify-between text-sm mb-2">
                            <span>Court Fee (1 hour)</span>
                            <span x-text="'₱' + selectedCourtRate.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-sm mb-4">
                            <span>Service Fee</span>
                            <span x-text="'₱' + serviceFee.toFixed(2)"></span>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t" style="border-color: var(--gz-border);">
                            <span class="gz-font-display font-bold text-sm">Total Due</span>
                            <span class="gz-font-display font-bold text-lg" style="color: var(--gz-pop-dark);" x-text="'₱' + totalDue.toFixed(2)"></span>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-7">
                    <div class="gz-panel gz-panel-body">
                        <h3 class="gz-font-display font-bold text-base mb-6">Payment Method</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8" role="radiogroup" aria-label="Payment method">
                            <button type="button" @click="paymentMethod = 'gcash'"
                                    role="radio" :aria-checked="paymentMethod === 'gcash' ? 'true' : 'false'"
                                    class="gz-kpi-card text-center"
                                    :style="paymentMethod === 'gcash' ? 'border-color: var(--gz-pop); background: rgba(62,207,126,0.08);' : ''">
                                <span class="text-sm font-semibold block">GCash</span>
                            </button>
                            <button type="button" @click="paymentMethod = 'card'"
                                    role="radio" :aria-checked="paymentMethod === 'card' ? 'true' : 'false'"
                                    class="gz-kpi-card text-center"
                                    :style="paymentMethod === 'card' ? 'border-color: var(--gz-pop); background: rgba(62,207,126,0.08);' : ''">
                                <span class="text-sm font-semibold block">Card</span>
                            </button>
                            <button type="button" @click="paymentMethod = 'cash'"
                                    role="radio" :aria-checked="paymentMethod === 'cash' ? 'true' : 'false'"
                                    class="gz-kpi-card text-center"
                                    :style="paymentMethod === 'cash' ? 'border-color: var(--gz-pop); background: rgba(62,207,126,0.08);' : ''">
                                <span class="text-sm font-semibold block">Cash at Counter</span>
                            </button>
                        </div>

                        <div x-show="paymentMethod === 'gcash'" x-cloak class="mb-6">
                            <label class="gz-label" for="gcash_number">GCash Mobile Number</label>
                            <input type="tel" id="gcash_number" class="gz-input" placeholder="09XX XXX XXXX">
                        </div>
                        <div x-show="paymentMethod === 'card'" x-cloak class="mb-6 grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="gz-label" for="card_number">Card Number</label>
                                <input type="text" id="card_number" class="gz-input" placeholder="0000 0000 0000 0000">
                            </div>
                            <div>
                                <label class="gz-label" for="card_expiry">Expiry</label>
                                <input type="text" id="card_expiry" class="gz-input" placeholder="MM/YY">
                            </div>
                            <div>
                                <label class="gz-label" for="card_cvc">CVC</label>
                                <input type="text" id="card_cvc" class="gz-input" placeholder="123">
                            </div>
                        </div>
                        <div x-show="paymentMethod === 'cash'" x-cloak class="mb-6">
                            <p class="text-sm" style="color: var(--gz-muted);">
                                Pay in person at the KYMNET front desk when you arrive for your session.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('bookings.store') }}">
                            @csrf
                            <input type="hidden" name="court_id" :value="selectedCourt">
                            <input type="hidden" name="date" :value="selectedDate">
                            <input type="hidden" name="time_slot" :value="selectedTimeSlot">
                            <input type="hidden" name="payment_method" :value="paymentMethod">
                            <button type="submit" :disabled="!canConfirm" class="gz-btn-primary w-full justify-center">
                                Confirm & Pay
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>