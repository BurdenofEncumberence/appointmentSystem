<x-guest-layout title="Forgot Password — KYMNET" label="Forgot password">
    <h1 class="gz-font-display font-bold text-2xl mb-1">Forgot your password?</h1>
    <p class="gz-hint mb-6">No worries — we'll email you a link to reset it.</p>

    <div role="status" aria-live="polite">
        @if (session('status'))
            <div class="gz-status mb-5">{{ session('status') }}</div>
        @endif
    </div>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <label for="email" class="gz-label">Email</label>
            <input id="email" class="gz-input" type="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <p class="gz-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end mt-7">
            <button type="submit" class="gz-btn-primary">Email reset link</button>
        </div>
    </form>
</x-guest-layout>