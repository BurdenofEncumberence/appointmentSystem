<x-app-layout title="Reserve Courts — KYMNET">
    <div
        x-data='{
            selectedDate: "{{ now()->toDateString() }}",
            currentMonth: {{ now()->month - 1 }},
            currentYear: {{ now()->year }},
            todayStr: "{{ now()->toDateString() }}",

            courts: @json($courts),
            timeSlots: [
                "6:00 AM - 7:00 AM","7:00 AM - 8:00 AM","8:00 AM - 9:00 AM","9:00 AM - 10:00 AM",
                "10:00 AM - 11:00 AM","11:00 AM - 12:00 PM","12:00 PM - 1:00 PM","1:00 PM - 2:00 PM",
            ],

            bookedSlots: @json($bookedSlots ?? []),

            // Multi-slot selection array
            selectedSlots: [],

            isBooked(time, courtId, date = null) {
                const d = date || this.selectedDate;
                const dayBookings = this.bookedSlots[d] || {};
                const courtBookings = dayBookings[courtId] || [];
                return courtBookings.includes(time);
            },

            isSelected(time, courtId, date = null) {
                const d = date || this.selectedDate;
                return this.selectedSlots.some(s => s.courtId === courtId && s.time === time && s.date === d);
            },

            toggleSlot(time, courtId) {
                if (this.isBooked(time, courtId)) return;
                const d = this.selectedDate;
                const idx = this.selectedSlots.findIndex(s => s.courtId === courtId && s.time === time && s.date === d);
                if (idx > -1) {
                    this.selectedSlots.splice(idx, 1);
                } else {
                    const court = this.courts.find(c => c.id === courtId);
                    this.selectedSlots.push({
                        courtId: courtId,
                        courtName: court ? court.name : ("Court " + courtId),
                        rate: court ? Number(court.rate) : 0,
                        date: d,
                        time: time
                    });
                }
            },

            removeSlot(index) {
                this.selectedSlots.splice(index, 1);
                if (this.selectedSlots.length === 0 && this.step === 2) {
                    this.step = 1;
                }
            },

            clearSlots() {
                this.selectedSlots = [];
            },

            events: @json($events ?? []),
            selectedEvent: null,
            get selectedEventObj() {
                return this.events.find(e => e.id === this.selectedEvent);
            },
            get eventDiscountPercent() {
                return this.selectedEventObj ? (Number(this.selectedEventObj.discount) || 0) : 0;
            },
            get discountAmount() {
                return this.eventDiscountPercent > 0 ? (this.courtsSubtotal * (this.eventDiscountPercent / 100)) : 0;
            },

            // Legacy helpers
            get selectedCourt() {
                return this.selectedSlots.length > 0 ? this.selectedSlots[0].courtId : null;
            },
            get selectedTimeSlot() {
                return this.selectedSlots.length > 0 ? this.selectedSlots[0].time : null;
            },
            get selectedCourtName() {
                return this.selectedSlots.length > 0 ? this.selectedSlots[0].courtName : null;
            },

            get courtsSubtotal() {
                return this.selectedSlots.reduce((acc, s) => acc + (Number(s.rate) || 0), 0);
            },

            serviceFee: 25,

            get totalDue() {
                if (this.selectedSlots.length === 0) return 0;
                const sub = this.courtsSubtotal - this.discountAmount;
                return Math.max(0, sub + this.serviceFee);
            },

            get canPay() {
                return this.selectedSlots.length > 0;
            },

            step: 1,
            paymentMethod: null,

            get canConfirm() {
                return this.canPay && this.paymentMethod !== null;
            },

            pickDate(date) {
                this.selectedDate = date;
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
        }'
    >
        <div class="flex items-center justify-between flex-wrap gap-2 mb-4">
            <div>
                <h1 class="gz-font-display font-bold text-xl">Reserve Courts</h1>
                <p class="text-sm" style="color: var(--gz-muted);">
                    Select multiple courts or time slots across your preferred dates and book them all in one single checkout.
                </p>
            </div>
            <div x-show="selectedSlots.length > 0" class="flex items-center gap-3">
                <span class="gz-badge gz-badge-success" x-text="selectedSlots.length + (selectedSlots.length === 1 ? ' session selected' : ' sessions selected')"></span>
                <button type="button" @click="clearSlots()" class="text-xs font-semibold underline" style="color: var(--gz-danger);">
                    Clear Selection
                </button>
            </div>
        </div>

        {{-- Validation Error Alerts --}}
        @if ($errors->any())
            <div class="mb-4 p-4 rounded-xl text-sm" style="background: var(--gz-danger-bg); color: var(--gz-danger);" role="alert">
                <p class="font-bold mb-1">Please check your reservation selection:</p>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- STEP 1: Date, Court & Time Slots Grid --}}
        <div x-show="step === 1">
            <section class="gz-panel mb-4" style="padding: 16px;">
                <div class="flex items-center justify-between flex-wrap gap-2 mb-3">
                    <div>
                        <h2 class="gz-font-display font-bold text-sm" tabindex="-1" x-ref="step1Heading">
                            Pick Courts & Time Slots
                        </h2>
                        <p class="text-xs mt-0.5" style="color: var(--gz-muted);">
                            Click any open slot to add it. You can select multiple courts and times.
                        </p>
                    </div>
                    <div class="flex items-center gap-4 text-[11px]" style="color: var(--gz-muted);">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 inline-block rounded" style="background: var(--gz-surface); border: 1px solid var(--gz-border);" aria-hidden="true"></span> Open
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 inline-block rounded" style="background: var(--gz-pop);" aria-hidden="true"></span> Selected
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 inline-block rounded" style="background: var(--gz-bg); border: 1px solid var(--gz-border);" aria-hidden="true"></span> Booked
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                    {{-- Calendar on Left Column --}}
                    <div class="lg:col-span-4">
                        <div class="gz-kpi-card" style="padding: 12px;">
                            <div class="flex items-center justify-between mb-2">
                                <button type="button" @click="prevMonth()" class="gz-btn-outline gz-btn-sm" style="padding: 5px 10px;" aria-label="Previous month">‹</button>
                                <p class="gz-font-display font-bold text-xs" x-text="monthLabel" aria-live="polite"></p>
                                <button type="button" @click="nextMonth()" class="gz-btn-outline gz-btn-sm" style="padding: 5px 10px;" aria-label="Next month">›</button>
                            </div>
                            <div class="grid grid-cols-7 gap-1 text-[10px] text-center mb-1" style="color: var(--gz-muted);" aria-hidden="true">
                                <span>S</span><span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span>
                            </div>
                            <div class="grid grid-cols-7 gap-1 text-xs" role="grid" aria-label="Choose a date">
                                <template x-for="(day, idx) in calendarDays" :key="idx">
                                    <button
                                        type="button"
                                        x-show="day !== null"
                                        @click="pickDay(day)"
                                        :disabled="day === null || isPast(day)"
                                        :aria-current="day !== null && isToday(day) ? 'date' : null"
                                        :aria-pressed="day !== null && selectedDate === dateStringFor(day) ? 'true' : 'false'"
                                        :aria-label="day !== null ? (isToday(day) ? day + ', today' : day) : null"
                                        class="h-7 flex items-center justify-center rounded-lg"
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
                            <div class="mt-3 pt-2 border-t flex items-center justify-between text-[11px]" style="border-color: var(--gz-border);">
                                <span style="color: var(--gz-muted);">Viewing Schedule for:</span>
                                <span class="font-bold" x-text="selectedDate" style="color: var(--gz-ink);"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Court Availability Table --}}
                    <div class="lg:col-span-8">
                        <div class="gz-panel overflow-hidden">
                            <div style="max-height: 280px; overflow-y: auto; overflow-x: auto;">
                                <table class="gz-table" style="font-size: 12px;">
                                    <caption class="sr-only">Court availability by time slot. Click to select multiple.</caption>
                                    <thead style="position: sticky; top: 0; z-index: 2; background: var(--gz-surface);">
                                        <tr>
                                            <th style="padding: 7px 10px;">Time</th>
                                            <template x-for="court in courts" :key="'head'+court.id">
                                                <th class="text-center" style="padding: 7px 8px;">
                                                    <span x-text="court.name"></span>
                                                    <span class="block text-[10px] font-normal" style="color: var(--gz-muted);" x-text="'₱' + Number(court.rate).toFixed(2) + '/hr'"></span>
                                                </th>
                                            </template>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="time in timeSlots" :key="time">
                                            <tr>
                                                <th scope="row" class="text-left font-semibold whitespace-nowrap" style="padding: 5px 10px;" x-text="time"></th>
                                                <template x-for="court in courts" :key="time + '-' + court.id">
                                                    <td style="padding: 4px;">
                                                        <button
                                                            type="button"
                                                            @click="toggleSlot(time, court.id)"
                                                            :disabled="isBooked(time, court.id)"
                                                            :aria-pressed="isSelected(time, court.id) ? 'true' : 'false'"
                                                            class="w-full text-[11px] font-semibold rounded-lg transition"
                                                            style="height: 28px;"
                                                            :style="
                                                                isBooked(time, court.id)
                                                                    ? 'height: 28px; background: var(--gz-bg); color: var(--gz-muted); cursor: not-allowed; border: 1px solid var(--gz-border);'
                                                                    : (isSelected(time, court.id)
                                                                        ? 'height: 28px; background: var(--gz-pop); color: var(--gz-ink); font-weight: 700; cursor: pointer;'
                                                                        : 'height: 28px; background: var(--gz-surface); border: 1px solid var(--gz-border); cursor: pointer;')
                                                            "
                                                            x-text="isBooked(time, court.id) ? 'Booked' : (isSelected(time, court.id) ? '✓ Selected' : 'Open')"
                                                        ></button>
                                                    </td>
                                                </template>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Selected Slots Tray (Chips & Summary) --}}
                <div x-show="selectedSlots.length > 0" x-cloak class="mt-4 pt-4 border-t" style="border-color: var(--gz-border);">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold" style="color: var(--gz-ink);">
                            Selected Sessions in Cart (<span x-text="selectedSlots.length"></span>):
                        </span>
                        <span class="text-xs" style="color: var(--gz-muted);">
                            Court Subtotal: <strong class="text-sm font-bold" style="color: var(--gz-pop-dark);" x-text="'₱' + courtsSubtotal.toFixed(2)"></strong>
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 max-h-32 overflow-y-auto p-1">
                        <template x-for="(slot, idx) in selectedSlots" :key="slot.courtId + '-' + slot.date + '-' + slot.time">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold"
                                  style="background: rgba(62,207,126,0.12); border: 1px solid var(--gz-pop); color: var(--gz-ink);">
                                <span x-text="slot.courtName"></span>
                                <span class="font-normal" style="color: var(--gz-muted);" x-text="'· ' + slot.date"></span>
                                <span class="font-normal" style="color: var(--gz-muted);" x-text="'· ' + slot.time"></span>
                                <span class="font-bold ml-1" style="color: var(--gz-pop-dark);" x-text="'₱' + Number(slot.rate).toFixed(2)"></span>
                                <button type="button" @click="removeSlot(idx)" class="ml-1 text-xs opacity-60 hover:opacity-100 hover:text-red-600 font-bold" title="Remove session">
                                    ✕
                                </button>
                            </span>
                        </template>
                    </div>
                </div>
            </section>

            {{-- Checkout Bar --}}
            <section class="gz-panel flex flex-col sm:flex-row items-center justify-between gap-3" style="padding: 14px 16px;">
                <div>
                    <p class="gz-font-display font-bold text-sm mb-0.5">Booking Cart Summary</p>
                    <p class="text-xs" style="color: var(--gz-muted);" x-show="canPay">
                        <span x-text="selectedSlots.length + (selectedSlots.length === 1 ? ' session' : ' sessions')"></span> across your chosen court(s).
                        Subtotal: <span class="font-semibold" style="color: var(--gz-ink);" x-text="'₱' + courtsSubtotal.toFixed(2)"></span> ·
                        Est. Total (incl. service fee): <span class="font-bold" style="color: var(--gz-pop-dark);" x-text="'₱' + totalDue.toFixed(2)"></span>
                    </p>
                    <p class="text-xs" style="color: var(--gz-muted);" x-show="!canPay">
                        Select one or more open court time slots above to continue.
                    </p>
                </div>

                <button type="button" @click="goToReview()" :disabled="!canPay" class="gz-btn-primary gz-btn-sm whitespace-nowrap">
                    Review & Pay (<span x-text="selectedSlots.length"></span>) →
                </button>
            </section>
        </div>

        {{-- STEP 2: Review All Bookings & Process Single Transaction Payment --}}
        <div x-show="step === 2" x-cloak>
            <div class="flex items-center justify-between mb-4">
                <button type="button" @click="goBack()" class="gz-btn-outline gz-btn-sm">
                    ‹ Back to Court Selection
                </button>
                <span class="text-xs" style="color: var(--gz-muted);">
                    Step 2 of 2: Review & Single Checkout
                </span>
            </div>

            <h2 class="sr-only" tabindex="-1" x-ref="step2Heading">Review and pay for your bookings</h2>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                {{-- Left: Itemized Reservation Summary --}}
                <div class="lg:col-span-5">
                    <div class="gz-panel gz-panel-body">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="gz-font-display font-bold text-base">Reservation Summary</h3>
                            <span class="gz-badge gz-badge-neutral" x-text="selectedSlots.length + (selectedSlots.length === 1 ? ' item' : ' items')"></span>
                        </div>

                        {{-- Itemized Sessions List --}}
                        <div class="max-h-60 overflow-y-auto mb-4 divide-y" style="border-color: var(--gz-border);">
                            <template x-for="(slot, idx) in selectedSlots" :key="'sum-'+slot.courtId+'-'+slot.date+'-'+slot.time">
                                <div class="py-2.5 flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-sm" x-text="slot.courtName"></p>
                                        <p class="text-xs" style="color: var(--gz-muted);" x-text="slot.date + ' · ' + slot.time"></p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="font-semibold text-sm" x-text="'₱' + Number(slot.rate).toFixed(2)"></p>
                                        <button type="button" @click="removeSlot(idx)" class="text-[11px] underline opacity-70 hover:opacity-100 hover:text-red-600">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Event Discounts --}}
                        <template x-if="events && events.length > 0">
                            <div class="pt-3 border-t mb-3" style="border-color: var(--gz-border);">
                                <label class="text-xs font-bold uppercase tracking-wider block mb-2" style="color: var(--gz-pop-dark);">Special Event Promotion</label>
                                <select x-model="selectedEvent" class="gz-input text-xs w-full py-1.5 mb-2">
                                    <option :value="null">No promotional discount</option>
                                    <template x-for="event in events" :key="event.id">
                                        <option :value="event.id" x-text="event.title + ' (' + event.discount + '% OFF)'"></option>
                                    </template>
                                </select>
                            </div>
                        </template>

                        {{-- Financial Totals --}}
                        <div class="pt-3 border-t space-y-2 text-sm" style="border-color: var(--gz-border);">
                            <div class="flex justify-between">
                                <span style="color: var(--gz-muted);">Court Subtotal</span>
                                <span class="font-semibold" x-text="'₱' + courtsSubtotal.toFixed(2)"></span>
                            </div>
                            <template x-if="discountAmount > 0">
                                <div class="flex justify-between" style="color: var(--gz-pop-dark);">
                                    <span>Event Discount (<span x-text="eventDiscountPercent + '%'"></span>)</span>
                                    <span class="font-semibold" x-text="'-₱' + discountAmount.toFixed(2)"></span>
                                </div>
                            </template>
                            <div class="flex justify-between">
                                <span style="color: var(--gz-muted);">Transaction Service Fee</span>
                                <span class="font-semibold" x-text="'₱' + serviceFee.toFixed(2)"></span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-4 mt-4 border-t" style="border-color: var(--gz-border);">
                            <div>
                                <span class="gz-font-display font-bold text-sm block">Total Due</span>
                                <span class="text-[11px]" style="color: var(--gz-muted);">Single transaction</span>
                            </div>
                            <span class="gz-font-display font-bold text-xl" style="color: var(--gz-pop-dark);" x-text="'₱' + totalDue.toFixed(2)"></span>
                        </div>
                    </div>
                </div>

                {{-- Right: Payment Method & Submission Form --}}
                <div class="lg:col-span-7">
                    <div class="gz-panel gz-panel-body">
                        <h3 class="gz-font-display font-bold text-base mb-6">Payment Method</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6" role="radiogroup" aria-label="Payment method">
                            <button type="button" @click="paymentMethod = 'gcash'"
                                    role="radio" :aria-checked="paymentMethod === 'gcash' ? 'true' : 'false'"
                                    class="gz-kpi-card text-center"
                                    :style="paymentMethod === 'gcash' ? 'border-color: var(--gz-pop); background: rgba(62,207,126,0.08);' : ''">
                                <div class="icon-badge mx-auto mb-2" style="width:32px; height:32px; padding:6px;" id="icon-pay-gcash" aria-hidden="true"></div>
                                <span class="text-sm font-semibold block">GCash</span>
                            </button>
                            <button type="button" @click="paymentMethod = 'card'"
                                    role="radio" :aria-checked="paymentMethod === 'card' ? 'true' : 'false'"
                                    class="gz-kpi-card text-center"
                                    :style="paymentMethod === 'card' ? 'border-color: var(--gz-pop); background: rgba(62,207,126,0.08);' : ''">
                                <div class="icon-badge mx-auto mb-2" style="width:32px; height:32px; padding:6px;" id="icon-pay-card" aria-hidden="true"></div>
                                <span class="text-sm font-semibold block">Card</span>
                            </button>
                            <button type="button" @click="paymentMethod = 'cash'"
                                    role="radio" :aria-checked="paymentMethod === 'cash' ? 'true' : 'false'"
                                    class="gz-kpi-card text-center"
                                    :style="paymentMethod === 'cash' ? 'border-color: var(--gz-pop); background: rgba(62,207,126,0.08);' : ''">
                                <div class="icon-badge mx-auto mb-2" style="width:32px; height:32px; padding:6px;" id="icon-pay-cash" aria-hidden="true"></div>
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
                                Pay in person at the KYMNET front desk when you arrive for your first session.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('bookings.store') }}">
                            @csrf
                            {{-- JSON payload for multiple slots --}}
                            <input type="hidden" name="slots" :value="JSON.stringify(selectedSlots.map(s => ({ court_id: s.courtId, date: s.date, time_slot: s.time })))">

                            {{-- Legacy inputs for backward compatibility if 1 slot --}}
                            <input type="hidden" name="court_id" :value="selectedCourt">
                            <input type="hidden" name="date" :value="selectedSlots.length > 0 ? selectedSlots[0].date : selectedDate">
                            <input type="hidden" name="time_slot" :value="selectedTimeSlot">

                            <input type="hidden" name="payment_method" :value="paymentMethod">
                            <input type="hidden" name="event_id" :value="selectedEvent">

                            <button type="submit" :disabled="!canConfirm" class="gz-btn-primary w-full justify-center">
                                Confirm & Pay (<span x-text="'₱' + totalDue.toFixed(2)"></span>)
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const glyphs = {
                'icon-pay-gcash': [
                    "..GGGG..",".G....G.",".G.GG.G.",".G.GG.G.",
                    ".G.GG.G.",".G....G.","..GGGG..","........"
                ],
                'icon-pay-card': [
                    "GGGGGGGG","G......G","GGGGGGGG","G......G",
                    "G.GG...G","G......G","GGGGGGGG","........"
                ],
                'icon-pay-cash': [
                    "........","..GGGG..",".G....G.",".G.GG.G.",
                    ".G.GG...",".G....G.","..GGGG..","........"
                ]
            };
            Object.keys(glyphs).forEach(id => {
                if (typeof window.renderPixelGrid === 'function') {
                    window.renderPixelGrid(id, glyphs[id], { '.': 'transparent', 'G': '#3ECF7E' });
                }
            });
        })();
    </script>
</x-app-layout>