<x-guest-layout title="Log In — KYMNET" label="Log in">
    <h1 class="gz-font-display font-bold text-2xl mb-1">Welcome back.</h1>
    <p class="gz-hint mb-6">Log in to lock in your next court.</p>

    @if (session('status'))
        <div class="gz-status mb-5" role="status" aria-live="polite">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <label for="email" class="gz-label">Email</label>
            <input id="email" class="gz-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email')
                <p class="gz-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-5" x-data="{ showPassword: false }">
            <label for="password" class="gz-label">Password</label>
            <div class="relative">
                <input
                    id="password"
                    class="gz-input pr-11"
                    :type="showPassword ? 'text' : 'password'"
                    name="password"
                    required
                    autocomplete="current-password"
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
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"></path>
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
            @error('password')
                <p class="gz-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between mt-5">
            <label class="flex items-center gap-2 text-sm" style="color: var(--gz-muted);">
                <input type="checkbox" name="remember" class="gz-checkbox">
                Remember me
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="gz-link">Forgot password?</a>
            @endif
        </div>

        <div class="flex items-center justify-between mt-7">
            <a href="{{ route('register') }}" class="gz-link">Need an account?</a>
            <button type="submit" class="gz-btn-primary">Log in</button>
        </div>
    </form>
</x-guest-layout>