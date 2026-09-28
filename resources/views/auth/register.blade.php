<x-guest-layout>
    <div class="gz-page">
        <div class="gz-card">
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

                <div class="mt-5">
                    <label for="password" class="gz-label">Password</label>
                    <input id="password" class="gz-input" type="password" name="password" required autocomplete="new-password" aria-describedby="password-hint">
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
        </div>
    </div>
</x-guest-layout>