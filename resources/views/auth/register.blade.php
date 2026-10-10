<x-guest-layout title="Register — KYMNET" label="Register" card-class="max-w-[620px]">
    <h1 class="gz-font-display font-bold text-2xl mb-1">Create your account.</h1>
    <p class="gz-hint mb-6">Takes about a minute. No cap.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <fieldset class="border-0 p-0 m-0">
            <legend class="sr-only">Full name</legend>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="gz-label">First name</label>
                    <input id="first_name" class="gz-input capitalize" type="text" name="first_name" value="{{ old('first_name', $pending['first_name'] ?? '') }}" required autofocus autocomplete="given-name" placeholder="First name">
                    @error('first_name')
                        <p class="gz-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="last_name" class="gz-label">Last name</label>
                    <input id="last_name" class="gz-input capitalize" type="text" name="last_name" value="{{ old('last_name', $pending['last_name'] ?? '') }}" required autocomplete="family-name" placeholder="Last name">
                    @error('last_name')
                        <p class="gz-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4">
                <label for="middle_name" class="gz-label">Middle name <span class="text-xs font-normal opacity-70">(optional)</span></label>
                <input id="middle_name" class="gz-input capitalize" type="text" name="middle_name" value="{{ old('middle_name', $pending['middle_name'] ?? '') }}" autocomplete="additional-name" placeholder="Middle name">
                @error('middle_name')
                    <p class="gz-error" role="alert">{{ $message }}</p>
                @enderror
            </div>
        </fieldset>

        <div class="mt-4">
            <label for="email" class="gz-label">Email</label>
            <input id="email" class="gz-input" type="email" name="email" value="{{ old('email', $pending['email'] ?? '') }}" required autocomplete="username">
            @error('email')
                <p class="gz-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
            <div x-data="{ showPassword: false }">
                <label for="password" class="gz-label">Password</label>
                <div class="relative">
                    <input
                        id="password"
                        class="gz-input pr-11"
                        :type="showPassword ? 'text' : 'password'"
                        name="password"
                        required
                        autocomplete="new-password"
                        aria-describedby="password-hint"
                    >
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 flex items-center px-3"
                        style="color: var(--gz-muted);"
                        :aria-label="showPassword ? 'Hide password' : 'Show password'"
                        :aria-pressed="showPassword.toString()"
                    >
                        <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8Z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <svg x-show="showPassword" x-cloak xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 11 7 11 7a13.16 13.16 0 0 1-1.67 2.68"></path>
                            <path d="M6.61 6.61A13.53 13.53 0 0 0 1 12s4 7 11 7a9.74 9.74 0 0 0 5.39-1.61"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>
                    </button>
                </div>
                <p id="password-hint" class="gz-hint text-xs mt-1">At least 8 characters.</p>
                @error('password')
                    <p class="gz-error" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="gz-label">Confirm password</label>
                <input id="password_confirmation" class="gz-input" type="password" name="password_confirmation" required autocomplete="new-password">
                @error('password_confirmation')
                    <p class="gz-error" role="alert">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Terms and Conditions & Privacy Policy Agreement --}}
        <div class="mt-5 pt-1" x-data="{ modal: null, agreed: {{ old('terms') ? 'true' : 'false' }} }">
            <div class="flex items-start gap-3 p-3.5 rounded-xl border border-[color:var(--gz-border)] bg-[color:var(--gz-bg)]/40 hover:bg-[color:var(--gz-bg)]/70 transition-colors">
                <input
                    id="terms"
                    type="checkbox"
                    name="terms"
                    value="1"
                    x-model="agreed"
                    required
                    class="mt-1 h-4 w-4 rounded border-stone-300 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-0 cursor-pointer"
                >
                <label for="terms" class="text-xs sm:text-sm text-stone-700 dark:text-stone-300 leading-relaxed cursor-pointer select-none">
                    I have read, understood, and agree to the
                    <button
                        type="button"
                        @click.prevent="modal = 'terms'"
                        class="font-semibold text-emerald-600 dark:text-emerald-400 hover:underline inline underline-offset-2 focus:outline-none"
                    >
                        Terms & Conditions
                    </button>
                    and the
                    <button
                        type="button"
                        @click.prevent="modal = 'privacy'"
                        class="font-semibold text-emerald-600 dark:text-emerald-400 hover:underline inline underline-offset-2 focus:outline-none"
                    >
                        Privacy Policy
                    </button>.
                </label>
            </div>
            @error('terms')
                <p class="gz-error mt-1.5 text-xs" role="alert">{{ $message }}</p>
            @enderror

            {{-- Terms and Conditions Modal --}}
            <template x-teleport="body">
                <div
                    x-show="modal === 'terms'"
                    x-cloak
                    @keydown.escape.window="modal = null"
                    class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 bg-black/75 backdrop-blur-sm overflow-y-auto"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                >
                    <div
                        @click.outside="modal = null"
                        class="relative w-full max-w-xl my-auto p-6 sm:p-8 rounded-2xl flex flex-col max-h-[85vh] shadow-2xl"
                        style="background-color: #FCFBF7 !important; border: 2px solid #12150F !important; box-shadow: 6px 8px 0px #12150F !important; color: #12150F !important; border-radius: 20px !important;"
                    >
                        <div class="flex items-center justify-between border-b pb-4 mb-4" style="border-color: #E4E0D4 !important;">
                            <div class="flex items-center gap-2.5">
                                <span class="text-xs font-bold px-2.5 py-1 rounded" style="background-color: #12150F !important; color: #E5A823 !important; letter-spacing: 0.5px;">Policy</span>
                                <h3 class="gz-font-display font-bold text-xl" style="color: #12150F !important; margin: 0;">Terms and Conditions</h3>
                            </div>
                            <button
                                type="button"
                                @click="modal = null"
                                aria-label="Close modal"
                                class="w-8 h-8 flex items-center justify-center rounded-lg text-lg font-bold hover:bg-stone-200 transition-colors"
                                style="color: #12150F !important;"
                            >✕</button>
                        </div>

                        <div class="overflow-y-auto space-y-4 text-sm leading-relaxed pr-2 flex-1" style="color: #374151 !important; max-height: 52vh;">
                            <div>
                                <h4 class="font-bold text-sm mb-1" style="color: #12150F !important;">1. Acceptance of Terms</h4>
                                <p style="color: #4B5563 !important; margin: 0;">By creating an account and booking courts with KYMNET, you agree to comply with and be bound by all terms, conditions, and court policies.</p>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm mb-1" style="color: #12150F !important;">2. Reservations & Payments</h4>
                                <p style="color: #4B5563 !important; margin: 0;">All reservations must be completed through our authorized online platform or front desk. Confirmed bookings are non-transferable without front desk authorization.</p>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm mb-1" style="color: #12150F !important;">3. Check-In & Attendance</h4>
                                <p style="color: #4B5563 !important; margin: 0;">Please check in at the front desk 10–15 minutes before your scheduled slot. Slots may be marked as a no-show if unverified after 15 minutes.</p>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm mb-1" style="color: #12150F !important;">4. Court Conduct & Equipment</h4>
                                <p style="color: #4B5563 !important; margin: 0;">Players must wear proper non-marking court shoes and observe fair play. Damage to nets or equipment through negligence is the player's responsibility.</p>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm mb-1" style="color: #12150F !important;">5. Liability Waiver</h4>
                                <p style="color: #4B5563 !important; margin: 0;">Participation in racquet sports is voluntary. KYMNET is not liable for personal property lost or accidental injuries during normal gameplay.</p>
                            </div>
                        </div>

                        <div class="mt-5 pt-4 border-t flex items-center justify-between gap-3" style="border-color: #E4E0D4 !important;">
                            <a href="{{ route('terms') }}" target="_blank" class="text-xs font-semibold hover:underline" style="color: #A67512 !important;">Open full page ↗</a>
                            <div class="flex gap-2">
                                <button type="button" @click="modal = null" class="gz-btn-outline gz-btn-sm text-xs">Close</button>
                                <button type="button" @click="agreed = true; modal = null" class="gz-btn-primary gz-btn-sm text-xs">I Agree & Accept</button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Privacy Policy Modal --}}
            <template x-teleport="body">
                <div
                    x-show="modal === 'privacy'"
                    x-cloak
                    @keydown.escape.window="modal = null"
                    class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 bg-black/75 backdrop-blur-sm overflow-y-auto"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                >
                    <div
                        @click.outside="modal = null"
                        class="relative w-full max-w-xl my-auto p-6 sm:p-8 rounded-2xl flex flex-col max-h-[85vh] shadow-2xl"
                        style="background-color: #FCFBF7 !important; border: 2px solid #12150F !important; box-shadow: 6px 8px 0px #12150F !important; color: #12150F !important; border-radius: 20px !important;"
                    >
                        <div class="flex items-center justify-between border-b pb-4 mb-4" style="border-color: #E4E0D4 !important;">
                            <div class="flex items-center gap-2.5">
                                <span class="text-xs font-bold px-2.5 py-1 rounded" style="background-color: #12150F !important; color: #E5A823 !important; letter-spacing: 0.5px;">Privacy</span>
                                <h3 class="gz-font-display font-bold text-xl" style="color: #12150F !important; margin: 0;">Privacy Policy</h3>
                            </div>
                            <button
                                type="button"
                                @click="modal = null"
                                aria-label="Close modal"
                                class="w-8 h-8 flex items-center justify-center rounded-lg text-lg font-bold hover:bg-stone-200 transition-colors"
                                style="color: #12150F !important;"
                            >✕</button>
                        </div>

                        <div class="overflow-y-auto space-y-4 text-sm leading-relaxed pr-2 flex-1" style="color: #374151 !important; max-height: 52vh;">
                            <div>
                                <h4 class="font-bold text-sm mb-1" style="color: #12150F !important;">1. Information We Collect</h4>
                                <p style="color: #4B5563 !important; margin: 0;">We collect your name, email address, password hash, and reservation history to facilitate court bookings and send automated OTP and receipt notifications.</p>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm mb-1" style="color: #12150F !important;">2. Use of Information</h4>
                                <p style="color: #4B5563 !important; margin: 0;">Your data is used strictly for scheduling match times, managing court capacity, verifying identity, and sending match confirmations.</p>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm mb-1" style="color: #12150F !important;">3. Data Security & Storage</h4>
                                <p style="color: #4B5563 !important; margin: 0;">We employ encrypted storage for passwords and session security cookies in accordance with the Philippine Data Privacy Act of 2012.</p>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm mb-1" style="color: #12150F !important;">4. No Third-Party Sales</h4>
                                <p style="color: #4B5563 !important; margin: 0;">We do not sell, distribute, or rent your personal contact information to any external advertisers or third parties.</p>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm mb-1" style="color: #12150F !important;">5. Your Data Rights</h4>
                                <p style="color: #4B5563 !important; margin: 0;">You may view, edit, or delete your registered profile and reservation data at any time via your Profile settings.</p>
                            </div>
                        </div>

                        <div class="mt-5 pt-4 border-t flex items-center justify-between gap-3" style="border-color: #E4E0D4 !important;">
                            <a href="{{ route('privacy') }}" target="_blank" class="text-xs font-semibold hover:underline" style="color: #A67512 !important;">Open full page ↗</a>
                            <div class="flex gap-2">
                                <button type="button" @click="modal = null" class="gz-btn-outline gz-btn-sm text-xs">Close</button>
                                <button type="button" @click="agreed = true; modal = null" class="gz-btn-primary gz-btn-sm text-xs">I Agree & Accept</button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div class="flex items-center justify-between mt-7">
            <a href="{{ route('login') }}" class="gz-link">Already registered?</a>
            <button type="submit" class="gz-btn-primary">Register</button>
        </div>
    </form>
</x-guest-layout>