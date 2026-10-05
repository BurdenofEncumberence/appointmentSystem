<x-guest-layout title="Verify Email — KYMNET" label="Verify Email">
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-2">
            <span class="gz-badge-primary text-[10px] uppercase font-bold tracking-wider">Step 2 of 2</span>
            <span class="text-xs font-mono text-[color:var(--gz-muted)]">Security Verification</span>
        </div>
        <h1 class="gz-font-display font-bold text-2xl mb-1">Verify your email.</h1>
        <p class="gz-hint">
            We sent a 6-digit verification code to
            <span class="font-bold text-[color:var(--gz-ink)] font-mono">{{ $maskedEmail }}</span>.
        </p>
    </div>

    @if (session('status'))
        <div class="gz-status mb-5" role="status" aria-live="polite">
            {{ session('status') }}
        </div>
    @endif

    @if (!empty($devOtp))
        <div class="mb-5 p-3.5 rounded-xl border border-dashed border-[color:var(--gz-pop)] bg-[color:var(--gz-pop)]/10 text-xs font-mono flex items-center justify-between">
            <div>
                <span class="font-bold text-[color:var(--gz-ink)]">⚡ Local Test Code:</span>
                <span class="text-sm font-black tracking-widest text-[color:var(--gz-ink)] ml-2">{{ $devOtp }}</span>
            </div>
            <span class="text-[10px] text-[color:var(--gz-muted)] uppercase tracking-wider">dev-only</span>
        </div>
    @endif

    <form method="POST" action="{{ route('register.otp.verify') }}" class="mb-6" data-show-loader="true">
        @csrf

        <div>
            <label for="otp" class="gz-label flex items-center justify-between">
                <span>Enter 6-Digit Code</span>
                <span class="text-xs font-normal text-[color:var(--gz-muted)]">Expires in 10 mins</span>
            </label>
            <input
                id="otp"
                class="gz-input text-center text-2xl font-mono tracking-[0.4em] font-bold py-3 uppercase"
                type="text"
                name="otp"
                maxlength="6"
                pattern="[0-9]*"
                inputmode="numeric"
                autocomplete="one-time-code"
                placeholder="000000"
                required
                autofocus
            >
            @error('otp')
                <p class="gz-error mt-2" role="alert">{{ $message }}</p>
            @enderror
            @error('email')
                <p class="gz-error mt-2" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-6">
            <button type="submit" class="gz-btn-primary w-full text-center justify-center">
                Verify &amp; Create Account
            </button>
        </div>
    </form>

    <div
        class="border-t border-[color:var(--gz-border)] pt-5 text-sm"
        x-data="{
            resendTarget: {{ (int) $resendAvailableAt }},
            now: Math.floor(Date.now() / 1000),
            get remaining() {
                return Math.max(0, this.resendTarget - this.now);
            },
            init() {
                setInterval(() => {
                    this.now = Math.floor(Date.now() / 1000);
                }, 1000);
            }
        }"
    >
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <form method="POST" action="{{ route('register.otp.resend') }}">
                @csrf
                <button
                    type="submit"
                    class="text-xs font-bold underline underline-offset-4 disabled:opacity-40 disabled:no-underline disabled:cursor-not-allowed"
                    :disabled="remaining > 0"
                    style="color: var(--gz-ink);"
                >
                    <span x-show="remaining > 0" x-cloak>
                        Resend code in (<span x-text="remaining"></span>s)
                    </span>
                    <span x-show="remaining === 0">
                        Didn't get the code? Resend
                    </span>
                </button>
            </form>

            <a
                href="{{ route('register.otp.cancel') }}"
                class="text-xs font-medium text-[color:var(--gz-muted)] hover:text-[color:var(--gz-ink)] underline underline-offset-2"
            >
                Wrong email? Edit details
            </a>
        </div>
    </div>
</x-guest-layout>
