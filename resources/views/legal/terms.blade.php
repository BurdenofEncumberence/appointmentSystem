<x-guest-layout title="Terms and Conditions — KYMNET" label="Terms & Conditions" card-class="max-w-[760px]">
    <div class="mb-6">
        @auth
            <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 hover:text-emerald-700 mb-3">
                ← Back to Home
            </a>
        @else
            <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 hover:text-emerald-700 mb-3">
                ← Back to Registration
            </a>
        @endauth
        <h1 class="gz-font-display font-bold text-2xl md:text-3xl text-stone-900 mb-1">Terms and Conditions</h1>
        <p class="gz-hint text-sm">Last updated: {{ date('F Y') }} · KYMNET Court Booking System</p>
    </div>

    <div class="space-y-6 text-sm text-stone-700 leading-relaxed max-h-[500px] overflow-y-auto pr-2 border-y border-stone-200 py-4">
        <section>
            <h2 class="font-bold text-base text-stone-900 mb-2">1. Acceptance of Agreement</h2>
            <p>
                By creating an account, reserving court slots, or utilizing any services provided by KYMNET ("the Platform", "we", "us"), you agree to be legally bound by these Terms and Conditions. If you do not agree to these terms, you may not register or utilize our court facilities.
            </p>
        </section>

        <section>
            <h2 class="font-bold text-base text-stone-900 mb-2">2. User Account & Security</h2>
            <p>
                You must provide accurate, current, and complete information during registration. You are solely responsible for maintaining the confidentiality of your login credentials and OTP verification codes. You agree to notify management immediately of any unauthorized use of your account.
            </p>
        </section>

        <section>
            <h2 class="font-bold text-base text-stone-900 mb-2">3. Court Bookings & Payments</h2>
            <p>
                All court reservations made through the platform are confirmed upon successful payment verification. Rates are displayed in Philippine Pesos (PHP) and reflect the specified time slots. Promotional discounts and coupon codes are applied at checkout and are non-transferable.
            </p>
        </section>

        <section>
            <h2 class="font-bold text-base text-stone-900 mb-2">4. Check-in & Attendance Policy</h2>
            <p>
                Players are required to arrive at the facility at least 10–15 minutes prior to their scheduled match time and present their booking reference code. Failure to arrive within 15 minutes of the start time may result in the slot being marked as a no-show.
            </p>
        </section>

        <section>
            <h2 class="font-bold text-base text-stone-900 mb-2">5. Facility Rules & Sportsmanship</h2>
            <p>
                All players and guests must wear appropriate non-marking athletic or court shoes. Proper athletic attire is required at all times. Abusive behavior, damage to nets or facility equipment, and unsportsmanlike conduct may result in immediate suspension or permanent banning from the courts.
            </p>
        </section>

        <section>
            <h2 class="font-bold text-base text-stone-900 mb-2">6. Limitation of Liability</h2>
            <p>
                Participation in racquet sports carries inherent physical risk. KYMNET and its affiliates are not liable for any personal injury, illness, loss, or property damage sustained while using our court premises, except where required by applicable Philippine law.
            </p>
        </section>

        <section>
            <h2 class="font-bold text-base text-stone-900 mb-2">7. Changes to Terms</h2>
            <p>
                KYMNET reserves the right to amend or update these Terms and Conditions at any time. Continued use of the platform following any modifications constitutes your formal acceptance of the revised terms.
            </p>
        </section>
    </div>

    <div class="mt-6 flex items-center justify-between">
        <a href="{{ route('privacy') }}" class="gz-link text-sm">Read Privacy Policy →</a>
        @auth
            <a href="{{ url('/') }}" class="gz-btn-primary">Return to Home</a>
        @else
            <a href="{{ route('register') }}" class="gz-btn-primary">Return to Register</a>
        @endauth
    </div>
</x-guest-layout>
