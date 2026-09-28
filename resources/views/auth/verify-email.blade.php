<x-guest-layout>
    <div class="gz-page">
        <div class="gz-card">
            <h1 class="gz-font-display font-bold text-2xl mb-1">Confirm it's you.</h1>
            <p class="gz-hint mb-6">This is a secure area — please confirm your password before continuing.</p>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div>
                    <label for="password" class="gz-label">Password</label>
                    <input id="password" class="gz-input" type="password" name="password" required autocomplete="current-password" autofocus>
                    @error('password')
                        <p class="gz-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end mt-7">
                    <button type="submit" class="gz-btn-primary">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>