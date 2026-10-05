<x-app-layout title="Book a Court — KYMNET">
    {{-- Wider wrapper: breaks out of the layout's narrow container --}}
    <div
        class="min-h-screen pb-8"
        style="width: min(92vw, 1280px); position: relative; left: 50%; transform: translateX(-50%);"
        x-data='{
            selectedCourt: null,
            selectedDate: "{{ now()->toDateString() }}",
            selectedTimeSlot: null,
            currentMonth: {{ now()->month - 1 }},
            currentYear: {{ now()->year }},
            todayStr: "{{ now()->toDateString() }}",
            showCalendar: false,
            showLayout: false,
            notice: "",
            submitting: false,
            gcashNumber: "",
            cardNumber: "",
            cardExpiry: "",
            cardCvc: "",

            /* Feedback state: errors only show after the user tries to continue */
            showErrors: false,
            showPayErrors: false,

            courts: @json($courts, JSON_HEX_APOS),
            timeSlots: [
                "6:00 AM - 7:00 AM","7:00 AM - 8:00 AM","8:00 AM - 9:00 AM","9:00 AM - 10:00 AM",
                "10:00 AM - 11:00 AM","11:00 AM - 12:00 PM","12:00 PM - 1:00 PM","1:00 PM - 2:00 PM",
            ],

            bookedSlots: @json($bookedSlots ?? [], JSON_HEX_APOS),

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
                if (this.isSelected(time, courtId)) { this.clearSelection(); return; }
                this.selectedCourt = courtId;
                this.selectedTimeSlot = time;
                this.notice = "";
                this.showErrors = false;
            },
            clearSelection() {
                this.selectedCourt = null;
                this.selectedTimeSlot = null;
                this.notice = "";
            },
            pickCourt(courtId) {
                this.selectedCourt = courtId;
                this.selectedTimeSlot = null;
            },
            pickDate(date) {
                if (date !== this.selectedDate && this.selectedTimeSlot) {
                    this.notice = "Date changed. Please choose a time again.";
                }
                this.selectedDate = date;
                this.selectedTimeSlot = null;
                const d = new Date(date + "T00:00:00");
                this.currentMonth = d.getMonth();
                this.currentYear = d.getFullYear();
            },
            longLabel(day) {
                const d = new Date(this.currentYear, this.currentMonth, day);
                return d.toLocaleDateString("en-US", { weekday: "long", month: "long", day: "numeric", year: "numeric" }) + (this.isToday(day) ? ", today" : "");
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
                this.showCalendar = false;
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

            /* Step 1 message: says exactly what is missing */
            get missingMessage() {
                if (!this.selectedCourt || !this.selectedTimeSlot) {
                    return "Pick an open time slot on a court in the table above to continue.";
                }
                return "";
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
                if (!this.canPay) {
                    this.showErrors = true;
                    this.$nextTick(() => this.$refs.step1Error?.focus());
                    return;
                }
                this.showErrors = false;
                this.step = 2;
                this.$nextTick(() => this.$refs.step2Heading?.focus());
            },
            goBack() {
                this.step = 1;
                this.showPayErrors = false;
                this.$nextTick(() => this.$refs.step1Heading?.focus());
            },
            get gcashValid() {
                return /^09\d{9}$/.test(this.gcashNumber.replace(/\s/g, ""));
            },
            get cardNumberValid() {
                return /^\d{13,19}$/.test(this.cardNumber.replace(/\s/g, ""));
            },
            get cardExpiryValid() {
                const m = /^(\d{2})\/(\d{2})$/.exec(this.cardExpiry);
                if (!m) return false;
                const mm = parseInt(m[1], 10);
                const yy = 2000 + parseInt(m[2], 10);
                if (mm < 1 || mm > 12) return false;
                const now = new Date();
                return yy > now.getFullYear() || (yy === now.getFullYear() && mm >= now.getMonth() + 1);
            },
            get cardCvcValid() {
                return /^\d{3,4}$/.test(this.cardCvc);
            },
            get paymentValid() {
                if (this.paymentMethod === "gcash") return this.gcashValid;
                if (this.paymentMethod === "card") return this.cardNumberValid && this.cardExpiryValid && this.cardCvcValid;
                return this.paymentMethod === "cash";
            },
            formatCard() {
                this.cardNumber = this.cardNumber.replace(/\D/g, "").slice(0, 19).replace(/(.{4})/g, "$1 ").trim();
            },
            formatExpiry() {
                let v = this.cardExpiry.replace(/\D/g, "").slice(0, 4);
                if (v.length > 2) v = v.slice(0, 2) + "/" + v.slice(2);
                this.cardExpiry = v;
            },
            get canConfirm() {
                return this.canPay && this.paymentValid && !this.submitting;
            },
            get confirmHint() {
                if (this.submitting) return "Processing your booking. Please wait.";
                if (this.paymentMethod === null) return "Choose a payment method to continue.";
                if (!this.paymentValid) return "Complete your payment details to continue.";
                return "";
            },
            /* Moves keyboard focus to the first thing the user still has to fix */
            focusFirstProblem() {
                this.$nextTick(() => {
                    if (this.paymentMethod === null) {
                        this.$refs.payGroup?.querySelector("button")?.focus();
                        return;
                    }
                    const ids = this.paymentMethod === "gcash"
                        ? ["gcash_number"]
                        : ["card_number", "card_expiry", "card_cvc"];
                    const ok = {
                        gcash_number: this.gcashValid,
                        card_number: this.cardNumberValid,
                        card_expiry: this.cardExpiryValid,
                        card_cvc: this.cardCvcValid,
                    };
                    const id = ids.find(i => !ok[i]);
                    if (id) document.getElementById(id)?.focus();
                });
            },
            onSubmit(e) {
                if (this.submitting) { e.preventDefault(); return; }
                if (!this.canConfirm) {
                    e.preventDefault();
                    this.showPayErrors = true;
                    this.focusFirstProblem();
                    return;
                }
                this.submitting = true;
            },
        }'
        @keydown.escape.window="showLayout = false; showCalendar = false"
        @pageshow.window="submitting = false"
    >
        {{-- Step marker --}}
        <ol class="flex items-center justify-center gap-2 text-xs mb-6" aria-label="Booking progress">
            <template x-for="(label, i) in ['Choose time', 'Review &amp; pay', 'Confirmed']" :key="label">
                <li class="flex items-center gap-2" :aria-current="step === i + 1 ? 'step' : null">
                    <span class="inline-flex items-center justify-center rounded-full text-[11px] font-bold"
                          :style="step === i + 1
                              ? 'width: 22px; height: 22px; background: var(--gz-pop); color: var(--gz-ink);'
                              : (step > i + 1
                                  ? 'width: 22px; height: 22px; background: var(--gz-pop-dark); color: #fff;'
                                  : 'width: 22px; height: 22px; background: var(--gz-surface); border: 1px solid var(--gz-border); color: var(--gz-muted);')"
                          x-text="step > i + 1 ? '✓' : i + 1"></span>
                    <span class="font-semibold" :style="step === i + 1 ? '' : 'color: var(--gz-muted);'" x-text="label"></span>
                    <span x-show="i < 2" aria-hidden="true" style="color: var(--gz-border);">—</span>
                </li>
            </template>
        </ol>

        <div x-show="step === 1">

        {{-- Intro line: the service fee now lives here since the Rates modal was removed --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <p class="text-sm" style="color: var(--gz-muted);">
                Pick a court and a time, then pay online or at the counter.
            </p>
            <button type="button" @click="showLayout = true" class="gz-btn-outline gz-btn-sm">Court Layout</button>
        </div>

        <section class="gz-panel mb-4" style="padding: 16px;">
            <h2 class="gz-font-display font-bold text-sm mb-1" tabindex="-1" x-ref="step1Heading">Set Your Date and Time</h2>
            <p class="text-xs mb-3" style="color: var(--gz-muted);" role="status" aria-live="polite">
                <span x-show="notice" x-text="notice" style="color: var(--gz-danger);"></span>
                <span x-show="!notice && selectedCourt && selectedTimeSlot">
                    Selected: <span class="font-semibold" style="color: var(--gz-pop-dark);" x-text="selectedCourtName"></span> at <span class="font-semibold" style="color: var(--gz-pop-dark);" x-text="selectedTimeSlot"></span> on <span class="font-semibold" style="color: var(--gz-pop-dark);" x-text="selectedDate"></span>
                </span>
                <span x-show="!notice && (!selectedCourt || !selectedTimeSlot)">
                    Choose a date, then select an open time slot.
                </span>
            </p>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                <div class="lg:col-span-3">
                    <div class="gz-kpi-card" style="padding: 12px;">
                        <div class="flex items-center justify-between mb-2">
                            <button type="button" @click="prevMonth()" class="gz-btn-outline gz-btn-sm" style="padding: 5px 10px;" aria-label="Previous month">‹</button>
                            <p class="gz-font-display font-bold text-xs" x-text="monthLabel" aria-live="polite"></p>
                            <button type="button" @click="nextMonth()" class="gz-btn-outline gz-btn-sm" style="padding: 5px 10px;" aria-label="Next month">›</button>
                        </div>
                        <div class="grid grid-cols-7 gap-1 text-[10px] text-center mb-1" style="color: var(--gz-muted);" aria-hidden="true">
                            <span>S</span><span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span>
                        </div>
                        <div class="grid grid-cols-7 gap-1 text-xs" role="group" aria-label="Choose a date">
                            <template x-for="(day, idx) in calendarDays" :key="idx">
                                <button
                                    type="button"
                                    x-show="day !== null"
                                    @click="pickDay(day)"
                                    :disabled="day === null || isPast(day)"
                                    :aria-current="day !== null && isToday(day) ? 'date' : null"
                                    :aria-pressed="day !== null && selectedDate === dateStringFor(day) ? 'true' : 'false'"
                                    :aria-label="day !== null ? longLabel(day) : null"
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
                    </div>
                </div>

                <div class="lg:col-span-9">
                    {{-- Border turns red when the user tried to continue without a slot --}}
                    <div class="gz-panel overflow-hidden"
                         :style="showErrors && !canPay ? 'border-color: var(--gz-danger);' : ''">
                        <div style="overflow-x: auto;">
                            <table class="gz-table" style="font-size: 12px;">
                                <caption class="sr-only">Court availability by time slot. Select an open slot to book it.</caption>
                                <thead>
                                    <tr>
                                        <th style="padding: 7px 10px;">Time</th>
                                        <template x-for="court in courts" :key="'head'+court.id">
                                            <th class="text-center" style="padding: 7px 8px;"
                                                :style="selectedCourt === court.id ? 'color: var(--gz-pop-dark);' : ''">
                                                <span x-text="court.name"></span>
                                                <span class="block text-[10px] font-normal" style="color: var(--gz-muted);" x-text="'₱' + court.rate + '/hr'"></span>
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
                                                        @click="pickSlot(time, court.id)"
                                                        :disabled="isBooked(time, court.id)"
                                                        :aria-pressed="isSelected(time, court.id) ? 'true' : 'false'"
                                                        :aria-label="court.name + ' at ' + time + ': ' + (isBooked(time, court.id) ? 'booked' : (isSelected(time, court.id) ? 'selected' : 'open'))"
                                                        class="w-full text-[11px] font-semibold rounded-lg transition"
                                                        style="height: 28px;"
                                                        :style="
                                                            isBooked(time, court.id)
                                                                ? 'height: 28px; background: var(--gz-bg); color: var(--gz-muted); cursor: not-allowed; border: 1px solid var(--gz-border);'
                                                                : (isSelected(time, court.id)
                                                                    ? 'height: 28px; background: var(--gz-pop); color: var(--gz-ink); cursor: pointer;'
                                                                    : 'height: 28px; background: var(--gz-surface); border: 1px solid var(--gz-border); cursor: pointer;')
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
                    </div>

                    <div class="flex items-center gap-4 mt-2 text-[11px]" style="color: var(--gz-muted);">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 inline-block rounded" style="background: var(--gz-surface); border: 1px solid var(--gz-border);" aria-hidden="true"></span> Available
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 inline-block rounded" style="background: var(--gz-bg); border: 1px solid var(--gz-border);" aria-hidden="true"></span> Booked
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 inline-block rounded" style="background: var(--gz-pop);" aria-hidden="true"></span> Selected
                        </span>
                    </div>
                </div>
            </div>
        </section>

        {{-- Server-side errors (e.g. slot taken meanwhile). Focused on load so it is announced. --}}
        @if ($errors->any())
            <div class="mb-4 p-3 rounded-xl text-sm" style="background: var(--gz-danger-bg); color: var(--gz-danger); border: 1.5px solid var(--gz-danger);"
                 role="alert" tabindex="-1" x-init="$el.focus()">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li><span aria-hidden="true">⚠</span> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Step 1 alert: shown when "Next" is clicked before a slot is chosen --}}
        <div x-show="showErrors && !canPay" x-cloak x-ref="step1Error" tabindex="-1" role="alert"
             class="mb-4 p-3 rounded-xl text-sm font-semibold"
             style="background: var(--gz-danger-bg); color: var(--gz-danger); border: 1.5px solid var(--gz-danger);">
            <span aria-hidden="true">⚠</span> <span x-text="missingMessage"></span>
        </div>

        <section class="gz-panel flex flex-col sm:flex-row items-center justify-between gap-3" style="padding: 14px 16px;">
            <div>
                <p class="gz-font-display font-bold text-sm mb-0.5">Your booking</p>
                <p class="text-xs" style="color: var(--gz-muted);" x-show="canPay">
                    <span x-text="selectedCourtName"></span> ·
                    <span x-text="selectedDate"></span> ·
                    <span x-text="selectedTimeSlot"></span>
                </p>
                <p class="text-xs" style="color: var(--gz-muted);" x-show="!canPay">
                    Choose a court, a date, and an open time slot to continue.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" x-show="canPay" x-cloak @click="clearSelection()" class="gz-btn-outline gz-btn-sm">Clear</button>
                {{-- aria-disabled (not disabled) so the click still fires and can explain what is missing --}}
                <button type="button" @click="goToReview()"
                        :aria-disabled="!canPay ? 'true' : 'false'"
                        class="gz-btn-primary gz-btn-sm">
                    Next: Review &amp; Pay
                </button>
            </div>
        </section>

        </div>

        {{-- Court Layout modal: just the picture --}}
        <div x-show="showLayout" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             style="background: rgba(0,0,0,0.6);"
             role="dialog" aria-modal="true" aria-label="Court layout"
             @click.self="showLayout = false">
            <div class="gz-panel relative" style="padding: 12px; max-width: 900px; width: 100%; max-height: 92vh; overflow: auto;">
                <button type="button" @click="showLayout = false" class="gz-btn-outline gz-btn-sm absolute" style="top: 10px; right: 10px; padding: 4px 10px; z-index: 1;" aria-label="Close court layout">✕</button>
                <h3 class="gz-font-display font-bold text-sm mb-2">Court Layout</h3>
                {{-- Put your picture at public/images/court-layout.png (or change the path) --}}
                <img src="{{ asset('images/court-layout.png') }}" alt="Court layout showing the position of each court"
                     style="width: 100%; height: auto; border-radius: 10px; display: block;"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <p style="display: none; color: var(--gz-muted);" class="text-sm py-8 text-center">
                    Court layout image not found. Add it at <code>public/images/court-layout.png</code>.
                </p>
            </div>
        </div>

        {{-- Step 2 --}}
        <div x-show="step === 2" x-cloak>
            <h2 class="sr-only" tabindex="-1" x-ref="step2Heading">Review and pay for your booking</h2>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <div class="lg:col-span-5">
                    <div class="gz-panel gz-panel-body">
                        <h3 class="gz-font-display font-bold text-base mb-6">Booking Summary</h3>

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

                        {{-- Outline turns red if the user tries to confirm with no method chosen --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8" role="radiogroup" aria-label="Payment method"
                             x-ref="payGroup"
                             :style="showPayErrors && paymentMethod === null ? 'outline: 2px solid var(--gz-danger); outline-offset: 6px;' : ''">
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

{{-- GCash number is submitted with the form (form attribute). Card details are validated here only and have no name, so they are never sent: use your payment gateway's hosted fields for real card payments. --}}
                        <div x-show="paymentMethod === 'gcash'" x-cloak class="mb-6">
                            <label class="gz-label" for="gcash_number">GCash Mobile Number</label>
                            <input type="tel" id="gcash_number" name="gcash_number" form="booking-form" class="gz-input"
                                   x-model="gcashNumber" inputmode="numeric" autocomplete="tel" placeholder="09XX XXX XXXX"
                                   :style="(gcashNumber !== '' || showPayErrors) && !gcashValid ? 'border-color: var(--gz-danger);' : ''"
                                   :aria-invalid="(gcashNumber !== '' || showPayErrors) && !gcashValid ? 'true' : 'false'" aria-describedby="gcash_error">
                            <p id="gcash_error" class="text-xs mt-1 font-semibold" style="color: var(--gz-danger);" x-show="(gcashNumber !== '' || showPayErrors) && !gcashValid">
                                <span aria-hidden="true">⚠</span> Enter an 11-digit mobile number starting with 09.
                            </p>
                        </div>
                        <div x-show="paymentMethod === 'card'" x-cloak class="mb-6 grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="gz-label" for="card_number">Card Number</label>
                                <input type="text" id="card_number" class="gz-input" x-model="cardNumber" @input="formatCard()"
                                       inputmode="numeric" autocomplete="cc-number" placeholder="0000 0000 0000 0000"
                                       :style="(cardNumber !== '' || showPayErrors) && !cardNumberValid ? 'border-color: var(--gz-danger);' : ''"
                                       :aria-invalid="(cardNumber !== '' || showPayErrors) && !cardNumberValid ? 'true' : 'false'" aria-describedby="card_number_error">
                                <p id="card_number_error" class="text-xs mt-1 font-semibold" style="color: var(--gz-danger);" x-show="(cardNumber !== '' || showPayErrors) && !cardNumberValid">
                                    <span aria-hidden="true">⚠</span> Enter a valid card number (13 to 19 digits).
                                </p>
                            </div>
                            <div>
                                <label class="gz-label" for="card_expiry">Expiry</label>
                                <input type="text" id="card_expiry" class="gz-input" x-model="cardExpiry" @input="formatExpiry()"
                                       inputmode="numeric" autocomplete="cc-exp" placeholder="MM/YY"
                                       :style="(cardExpiry !== '' || showPayErrors) && !cardExpiryValid ? 'border-color: var(--gz-danger);' : ''"
                                       :aria-invalid="(cardExpiry !== '' || showPayErrors) && !cardExpiryValid ? 'true' : 'false'" aria-describedby="card_expiry_error">
                                <p id="card_expiry_error" class="text-xs mt-1 font-semibold" style="color: var(--gz-danger);" x-show="(cardExpiry !== '' || showPayErrors) && !cardExpiryValid">
                                    <span aria-hidden="true">⚠</span> Use MM/YY. The card must not be expired.
                                </p>
                            </div>
                            <div>
                                <label class="gz-label" for="card_cvc">CVC</label>
                                <input type="text" id="card_cvc" class="gz-input" x-model="cardCvc"
                                       inputmode="numeric" autocomplete="cc-csc" maxlength="4" placeholder="123"
                                       :style="(cardCvc !== '' || showPayErrors) && !cardCvcValid ? 'border-color: var(--gz-danger);' : ''"
                                       :aria-invalid="(cardCvc !== '' || showPayErrors) && !cardCvcValid ? 'true' : 'false'" aria-describedby="card_cvc_error">
                                <p id="card_cvc_error" class="text-xs mt-1 font-semibold" style="color: var(--gz-danger);" x-show="(cardCvc !== '' || showPayErrors) && !cardCvcValid">
                                    <span aria-hidden="true">⚠</span> Enter the 3 or 4 digit code.
                                </p>
                            </div>
                        </div>
<div x-show="paymentMethod === 'cash'" x-cloak class="mb-6">
                            <p class="text-sm" style="color: var(--gz-muted);">
                                Pay in person at the KYMNET front desk when you arrive for your session.
                            </p>
                        </div>

                        <form id="booking-form" method="POST" action="{{ route('bookings.store') }}" @submit="onSubmit($event)">
                            @csrf
                            <input type="hidden" name="court_id" :value="selectedCourt">
                            <input type="hidden" name="date" :value="selectedDate">
                            <input type="hidden" name="time_slot" :value="selectedTimeSlot">
                            <input type="hidden" name="payment_method" :value="paymentMethod">

                            {{-- Turns red and shows a warning icon after a failed attempt --}}
                            <p class="text-xs mb-3" role="status" aria-live="polite"
                               :style="showPayErrors && !canConfirm && !submitting ? 'color: var(--gz-danger); font-weight: 600;' : 'color: var(--gz-muted);'"
                               x-text="(showPayErrors && !canConfirm && !submitting ? '⚠ ' : '') + confirmHint"></p>

                            <div class="flex items-center gap-3">
                                <button type="button" @click="goBack()" :disabled="submitting" class="gz-btn-outline">‹ Back</button>
                                {{-- aria-disabled (not disabled) so the click still fires and can explain what is missing --}}
                                <button type="submit" :aria-disabled="!canConfirm ? 'true' : 'false'"
                                        class="gz-btn-primary flex-1 justify-center"
                                        x-text="submitting ? 'Processing…' : (paymentMethod === 'cash' ? 'Confirm Booking' : 'Confirm &amp; Pay')"></button>
                            </div>
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