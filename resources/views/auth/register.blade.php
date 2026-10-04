<x-guest-layout title="Register — KYMNET" label="Register">
    <h1 class="gz-font-display font-bold text-2xl mb-1">Create your account.</h1>
    <p class="gz-hint mb-6">Takes about a minute. No cap.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <fieldset class="border-0 p-0 m-0">
            <legend class="sr-only">Full name</legend>

            <div>
                <label for="first_name" class="gz-label">First name</label>
                <input id="first_name" class="gz-input" type="text" name="first_name" value="{{ old('first_name') }}" required autofocus autocomplete="given-name">
                @error('first_name')
                    <p class="gz-error" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-5">
                <label for="middle_name" class="gz-label">Middle name (optional)</label>
                <input id="middle_name" class="gz-input" type="text" name="middle_name" value="{{ old('middle_name') }}" autocomplete="additional-name">
                @error('middle_name')
                    <p class="gz-error" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-5">
                <label for="last_name" class="gz-label">Last name</label>
                <input id="last_name" class="gz-input" type="text" name="last_name" value="{{ old('last_name') }}" required autocomplete="family-name">
                @error('last_name')
                    <p class="gz-error" role="alert">{{ $message }}</p>
                @enderror
            </div>
        </fieldset>

        <div class="mt-5">
            <label for="email" class="gz-label">Email</label>
            <input id="email" class="gz-input" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
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
            <p id="password-hint" class="gz-hint">At least 8 characters.</p>
            @error('password')
                <p class="gz-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-5">
            <label for="password_confirmation" class="gz-label">Confirm password</label>
            <input id="password_confirmation" class="gz-input" type="password" name="password_confirmation" required autocomplete="new-password">
            @error('password_confirmation')
                <p class="gz-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between mt-7">
            <a href="{{ route('login') }}" class="gz-link">Already registered?</a>
            <button type="submit" class="gz-btn-primary">Register</button>
        </div>
    </form>
</x-guest-layout>