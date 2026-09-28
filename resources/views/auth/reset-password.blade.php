<x-guest-layout>
    <div class="gz-page">
        <div class="gz-card">
            <h1 class="gz-font-display font-bold text-2xl mb-1">Reset your password.</h1>
            <p class="gz-hint mb-6">Pick a new one and you're back in.</p>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div>
                    <label for="email" class="gz-label">Email</label>
                    <input id="email" class="gz-input" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
                    @error('email')
                        <p class="gz-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-5">
                    <label for="password" class="gz-label">New password</label>
                    <input id="password" class="gz-input" type="password" name="password" required autocomplete="new-password" aria-describedby="password-hint">
                    <p id="password-hint" class="gz-hint">At least 8 characters.</p>
                    @error('password')
                        <p class="gz-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-5">
                    <label for="password_confirmation" class="gz-label">Confirm new password</label>
                    <input id="password_confirmation" class="gz-input" type="password" name="password_confirmation" required autocomplete="new-password">
                    @error('password_confirmation')
                        <p class="gz-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end mt-7">
                    <button type="submit" class="gz-btn-primary">Reset password</button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>