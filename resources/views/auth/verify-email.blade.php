<x-guest-layout title="Verify Email — Gaoshou Pickleball" label="Verify email">
    <h1 class="gz-font-display font-bold text-2xl mb-1">Check your inbox.</h1>
    <p class="gz-hint mb-6">
        Thanks for signing up! Before getting started, click the verification link we just emailed you. Didn't get it? We can send another.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="gz-status mb-5" role="status" aria-live="polite">
            A new verification link has been sent to the email address you provided during registration.
        </div>
    @endif

    <div class="flex items-center justify-between mt-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="gz-btn-primary">
                Resend verification email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="gz-link">
                Log out
            </button>
        </form>
    </div>
</x-guest-layout>