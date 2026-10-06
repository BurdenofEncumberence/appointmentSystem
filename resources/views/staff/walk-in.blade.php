<x-staff-layout title="Front Desk · Walk-In Booking · KYMNET" heading="New Walk-In Booking">
    {{-- Header Banner --}}
    <div class="gz-panel gz-panel-body mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-block w-2 h-2" style="background: var(--gz-pop);"></span>
                <span class="gz-eyebrow" style="color: var(--gz-pop-dark);">Front desk · On-the-spot reservation</span>
            </div>
            <h1 class="gz-font-display font-bold text-xl">
                Walk-in Court Booking
            </h1>
            <p class="text-sm mt-1" style="color: var(--gz-muted);">
                Reserve a court immediately for counter walk-ins and collect payment on arrival.
            </p>
        </div>
        <div class="flex items-center gap-2 print:hidden">
            <a href="{{ route('staff.today') }}" class="gz-btn-outline gz-btn-sm">
                ← Back to Today's Run-Sheet
            </a>
        </div>
    </div>

    {{-- Error / Status Alerts --}}
    @if ($errors->any())
        <div class="mb-6 p-4 border" style="background: rgba(179,38,30,0.08); border-color: var(--red);" role="alert">
            <div class="flex items-center gap-2 mb-2 font-bold text-sm" style="color: var(--red);">
                <span>⚠ Please fix the following errors:</span>
            </div>
            <ul class="text-xs list-disc list-inside space-y-1" style="color: var(--gz-ink);">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <script>
        function walkInBookingData() {
            return {
                selectedDate: @json(old('date', $selectedDate)),
                selectedCourtId: @json((string) old('court_id', $courts->first()->id ?? '')),
                selectedSlot: @json(old('time_slot', '')),
                selectedPaymentMethod: @json(old('payment_method', 'cash')),
                attendanceStatus: @json(old('attendance_status', 'show')),
                firstName: @json(old('first_name', '')),
                middleName: @json(old('middle_name', '')),
                lastName: @json(old('last_name', '')),
                email: @json(old('email', '')),

                courts: @json($courts),
                bookedSlots: @json($bookedSlots),
                recentPlayers: @json($recentPlayers),

                get currentCourt() {
                    return this.courts.find(c => c.id == this.selectedCourtId) || null;
                },

                get courtRate() {
                    return this.currentCourt ? Number(this.currentCourt.price_per_hour) : 0;
                },

                isSlotBooked(slot) {
                    const courtBookings = this.bookedSlots[this.selectedCourtId] || [];
                    return courtBookings.includes(slot);
                },

                selectPlayer(event) {
                    const playerId = event.target.value;
                    if (!playerId) return;
                    const player = this.recentPlayers.find(p => p.id == playerId);
                    if (player) {
                        this.firstName = player.first_name || '';
                        this.lastName = player.last_name || '';
                        this.email = player.email || '';
                    }
                },

                onDateChange() {
                    window.location.href = @json(route('staff.walkin.create')) + '?date=' + this.selectedDate;
                }
            };
        }
    </script>

    <div x-data="walkInBookingData()">
        <form method="POST" action="{{ route('staff.walkin.store') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @csrf

            {{-- LEFT 2 COLUMNS: Booking Options & Slot Selection --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Date & Court Chooser --}}
                <div class="gz-panel gz-panel-body">
                    <h2 class="gz-font-display font-bold text-base mb-4 flex items-center justify-between">
                        <span>1. Date &amp; Court Selection</span>
                        <span class="text-xs font-normal" style="color: var(--gz-muted);">Matches 1-hour blocks</span>
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Date picker --}}
                        <div>
                            <label for="date" class="gz-label">Reservation Date</label>
                            <input
                                id="date"
                                type="date"
                                name="date"
                                min="{{ today()->toDateString() }}"
                                x-model="selectedDate"
                                @change="onDateChange()"
                                class="gz-input"
                                required
                            >
                            <p class="text-[11px] mt-1" style="color: var(--gz-muted);">Changing date refreshes live court availability.</p>
                        </div>

                        {{-- Court picker --}}
                        <div>
                            <label for="court_id" class="gz-label">Court</label>
                            <select
                                id="court_id"
                                name="court_id"
                                x-model="selectedCourtId"
                                class="gz-input"
                                required
                            >
                                @foreach($courts as $court)
                                    <option value="{{ $court->id }}">
                                        {{ $court->court_name }} · ₱{{ number_format($court->price_per_hour, 2) }}/hr
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] mt-1" style="color: var(--gz-muted);">Select the court facility to book.</p>
                        </div>
                    </div>
                </div>

                {{-- Time Slot Picker --}}
                <div class="gz-panel gz-panel-body">
                    <h2 class="gz-font-display font-bold text-base mb-2 flex items-center justify-between">
                        <span>2. Choose Available Time Slot</span>
                        <template x-if="selectedSlot">
                            <span class="gz-badge gz-badge-success" x-text="'Selected: ' + selectedSlot"></span>
                        </template>
                    </h2>
                    <p class="text-xs mb-4" style="color: var(--gz-muted);">
                        Slots already booked for this court on <span class="font-bold text-[color:var(--gz-ink)]" x-text="selectedDate"></span> are disabled automatically.
                    </p>

                    <input type="hidden" name="time_slot" :value="selectedSlot" required>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        @foreach($timeSlots as $slot)
                            <button
                                type="button"
                                @click="if (!isSlotBooked('{{ $slot }}')) { selectedSlot = '{{ $slot }}'; }"
                                :disabled="isSlotBooked('{{ $slot }}')"
                                :class="{
                                    'opacity-40 cursor-not-allowed bg-gray-200 line-through text-gray-500': isSlotBooked('{{ $slot }}'),
                                    'border-2 border-[color:var(--red)] ring-2 ring-[color:var(--red)]/20 font-bold bg-white text-[color:var(--red)]': selectedSlot === '{{ $slot }}' && !isSlotBooked('{{ $slot }}'),
                                    'bg-[color:var(--gz-surface)] hover:border-[color:var(--gz-ink)] text-[color:var(--gz-ink)]': selectedSlot !== '{{ $slot }}' && !isSlotBooked('{{ $slot }}')
                                }"
                                class="p-2.5 text-xs text-center border transition-all flex flex-col items-center justify-center min-h-[58px]"
                                style="border-color: var(--gz-border);"
                            >
                                <span class="font-mono font-medium">{{ $slot }}</span>
                                <span class="text-[10px] mt-0.5" x-text="isSlotBooked('{{ $slot }}') ? 'Unavailable' : (selectedSlot === '{{ $slot }}' ? '✓ Selected' : 'Open')"></span>
                            </button>
                        @endforeach
                    </div>

                    @error('time_slot')
                        <p class="gz-error mt-2" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Customer Details --}}
                <div class="gz-panel gz-panel-body">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                        <h2 class="gz-font-display font-bold text-base">
                            3. Customer Information
                        </h2>

                        {{-- Quick Autofill dropdown --}}
                        @if($recentPlayers->isNotEmpty())
                            <div class="flex items-center gap-2">
                                <label for="quick_player" class="text-xs whitespace-nowrap" style="color: var(--gz-muted);">Autofill Player:</label>
                                <select id="quick_player" @change="selectPlayer($event)" class="text-xs p-1 border bg-white" style="border-color: var(--gz-border);">
                                    <option value="">-- Choose member --</option>
                                    @foreach($recentPlayers as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="gz-label">First Name <span class="text-red-600">*</span></label>
                            <input
                                id="first_name"
                                type="text"
                                name="first_name"
                                x-model="firstName"
                                class="gz-input capitalize"
                                placeholder="e.g. Juan"
                                required
                            >
                            @error('first_name')
                                <p class="gz-error" role="alert">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="last_name" class="gz-label">Last Name <span class="text-red-600">*</span></label>
                            <input
                                id="last_name"
                                type="text"
                                name="last_name"
                                x-model="lastName"
                                class="gz-input capitalize"
                                placeholder="e.g. Dela Cruz"
                                required
                            >
                            @error('last_name')
                                <p class="gz-error" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="middle_name" class="gz-label">Middle Name <span class="text-xs font-normal opacity-70">(optional)</span></label>
                            <input
                                id="middle_name"
                                type="text"
                                name="middle_name"
                                x-model="middleName"
                                class="gz-input capitalize"
                                placeholder="e.g. Santos"
                            >
                        </div>

                        <div>
                            <label for="phone" class="gz-label">Phone / Mobile <span class="text-xs font-normal opacity-70">(optional)</span></label>
                            <input
                                id="phone"
                                type="text"
                                name="phone"
                                value="{{ old('phone') }}"
                                class="gz-input"
                                placeholder="e.g. 0917-123-4567"
                            >
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="email" class="gz-label">Email Address <span class="text-xs font-normal opacity-70">(optional)</span></label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            x-model="email"
                            class="gz-input"
                            placeholder="e.g. customer@example.com"
                        >
                        <p class="text-[11px] mt-1" style="color: var(--gz-muted);">
                            If provided, existing player account is linked. If omitted, a guest walk-in profile is automatically generated.
                        </p>
                        @error('email')
                            <p class="gz-error" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Payment, Attendance & Confirmation --}}
            <div class="space-y-6">
                {{-- Payment & Checkout Card --}}
                <div class="gz-panel gz-panel-body">
                    <h2 class="gz-font-display font-bold text-base mb-4">
                        4. Counter Payment
                    </h2>

                    {{-- Rate Calculation --}}
                    <div class="p-3 border mb-4" style="background: var(--gz-bg); border-color: var(--gz-border);">
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span style="color: var(--gz-muted);">Rate per hour:</span>
                            <span class="font-mono font-bold" x-text="'₱' + courtRate.toFixed(2)"></span>
                        </div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span style="color: var(--gz-muted);">Duration:</span>
                            <span class="font-mono">1 Hour</span>
                        </div>
                        <div class="border-t pt-2 mt-2 flex items-center justify-between" style="border-color: var(--gz-border);">
                            <span class="font-bold text-sm">Total Due:</span>
                            <span class="font-mono font-bold text-lg" style="color: var(--gz-pop-dark);" x-text="'₱' + courtRate.toFixed(2)"></span>
                        </div>
                    </div>

                    {{-- Payment Method Selection --}}
                    <div class="mb-4">
                        <label class="gz-label">Payment Method Collected</label>
                        <div class="grid grid-cols-2 gap-2 mt-1">
                            <label class="flex items-center gap-2 p-2 border cursor-pointer text-xs" style="border-color: var(--gz-border);">
                                <input type="radio" name="payment_method" value="cash" x-model="selectedPaymentMethod">
                                <span class="font-bold">💵 Cash</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 border cursor-pointer text-xs" style="border-color: var(--gz-border);">
                                <input type="radio" name="payment_method" value="gcash" x-model="selectedPaymentMethod">
                                <span class="font-bold">📱 GCash</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 border cursor-pointer text-xs" style="border-color: var(--gz-border);">
                                <input type="radio" name="payment_method" value="maya" x-model="selectedPaymentMethod">
                                <span class="font-bold">💳 Maya</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 border cursor-pointer text-xs" style="border-color: var(--gz-border);">
                                <input type="radio" name="payment_method" value="card" x-model="selectedPaymentMethod">
                                <span class="font-bold">💳 Debit / Card</span>
                            </label>
                        </div>
                    </div>

                    {{-- Payment Receipt Reference --}}
                    <div class="mb-4">
                        <label for="ref_num" class="gz-label">Receipt / Reference # <span class="text-xs font-normal opacity-70">(optional)</span></label>
                        <input
                            id="ref_num"
                            type="text"
                            name="ref_num"
                            value="{{ old('ref_num') }}"
                            class="gz-input font-mono text-xs"
                            placeholder="e.g. CASH-10294 or GCash Ref"
                        >
                        <p class="text-[11px] mt-1" style="color: var(--gz-muted);">Leave empty to auto-generate a walk-in receipt number.</p>
                    </div>

                    {{-- Arrival / Attendance Status --}}
                    <div class="mb-6">
                        <label class="gz-label">Arrival Attendance Status</label>
                        <div class="space-y-2 mt-1">
                            <label class="flex items-start gap-2 p-2 border cursor-pointer text-xs" style="border-color: var(--gz-border);">
                                <input type="radio" name="attendance_status" value="show" x-model="attendanceStatus" class="mt-0.5">
                                <div>
                                    <span class="font-bold" style="color: var(--gz-pop-dark);">✓ SHOW (Present Now)</span>
                                    <p class="text-[10px]" style="color: var(--gz-muted);">Customer is here at the counter, ready to play immediately.</p>
                                </div>
                            </label>
                            <label class="flex items-start gap-2 p-2 border cursor-pointer text-xs" style="border-color: var(--gz-border);">
                                <input type="radio" name="attendance_status" value="confirmed" x-model="attendanceStatus" class="mt-0.5">
                                <div>
                                    <span class="font-bold text-[color:var(--gz-ink)]">⏳ SCHEDULED (Later)</span>
                                    <p class="text-[10px]" style="color: var(--gz-muted);">Reserved ahead of time; awaiting arrival later today.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <button
                        type="submit"
                        :disabled="!selectedSlot"
                        class="gz-btn-primary w-full py-3 text-sm justify-center flex items-center gap-2"
                        :class="{'opacity-50 cursor-not-allowed': !selectedSlot}"
                    >
                        <span>✓ Confirm Walk-In Reservation</span>
                    </button>
                    <p x-show="!selectedSlot" class="text-xs text-center mt-2 text-red-600 font-semibold">
                        Please choose an open time slot above.
                    </p>
                </div>

                {{-- Helpful Counter Instructions Card --}}
                <div class="gz-panel gz-panel-body text-xs space-y-2" style="background: var(--gz-bg); color: var(--gz-muted);">
                    <div class="font-bold text-xs flex items-center gap-1.5" style="color: var(--gz-ink);">
                        <span>ℹ</span> Front Desk Instructions
                    </div>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Walk-ins are booked as 1-hour sessions at standard court pricing.</li>
                        <li>Payments are marked as <strong>PAID</strong> immediately upon submission.</li>
                        <li>The reservation will appear instantly on the daily run-sheet under today's schedule.</li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
</x-staff-layout>
