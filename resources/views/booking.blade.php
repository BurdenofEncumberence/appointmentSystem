<x-app-layout>
    <x-slot name="header">
        <h1 class="font-pixel text-lg">Reserve a Court</h1>
    </x-slot>

    <div
        x-data="{
            selectedCourt: null,
            selectedDate: '{{ now()->toDateString() }}',
            selectedTimeSlot: null,
            currentMonth: {{ now()->month - 1 }},
            currentYear: {{ now()->year }},
            todayStr: '{{ now()->toDateString() }}',

            courts: @json($courts),
            timeSlots: [
                '6:00 AM - 7:00 AM','7:00 AM - 8:00 AM','8:00 AM - 9:00 AM','9:00 AM - 10:00 AM',
                '10:00 AM - 11:00 AM','11:00 AM - 12:00 PM','12:00 PM - 1:00 PM','1:00 PM - 2:00 PM',
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
                if (this.selectedCourt !== courtId) this.selectedTimeSlot = null;
                this.selectedCourt = courtId;
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
                const mm = String(this.currentMonth + 1).padStart(2, '0');
                const dd = String(day).padStart(2, '0');
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
                const names = ['January','February','March','April','May','June','July','August','September','October','November','December'];
                return names[this.currentMonth] + ' ' + this.currentYear;
            },
            get canPay() {
                return this.selectedCourt !== null && this.selectedDate !== null && this.selectedTimeSlot !== null;
            },
            get selectedCourtName() {
                const c = this.courts.find(c => c.id === this.selectedCourt);
                return c ? c.name : null;
            },
        }"
        class="max-w-6xl mx-auto px-6 py-10"
    >
        <p class="text-lg mb-8" style="color: var(--ink); opacity: 0.7;">
            Pick a court, choose your time, and secure it with online advance payment.
        </p>

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

        {{-- Payment CTA --}}
        <section class="pixel-border p-6 flex flex-col sm:flex-row items-center justify-between gap-4"
                  style="background: var(--ink); color: var(--cream);">
            <div>
                <p class="font-pixel text-sm mb-2">Proceed to payment</p>
                <p class="text-lg" style="opacity: 0.7;" x-show="canPay">
                    <span x-text="selectedCourtName"></span> ·
                    <span x-text="selectedDate"></span> ·
                    <span x-text="selectedTimeSlot"></span>
                </p>
                <p class="text-lg" style="opacity: 0.5;" x-show="!canPay">
                    Choose a court, a date, and an open time slot to continue.
                </p>
            </div>

            {{--
                Posts to the booking store route once BookingController exists.
                Server-side validation is the real source of truth — this Alpine
                gate only stops an obviously incomplete submission from being sent.
            --}}
            <form method="POST" action="{{ route('bookings.store') }}">
                @csrf
                <input type="hidden" name="court_id" :value="selectedCourt">
                <input type="hidden" name="date" :value="selectedDate">
                <input type="hidden" name="time_slot" :value="selectedTimeSlot">
                <button type="submit" :disabled="!canPay" class="pixel-btn font-pixel text-[10px] px-6 py-3"
                        style="background: var(--gold); color: var(--ink);">
                    Pay Now
                </button>
            </form>
        </section>
    </div>
</x-app-layout>