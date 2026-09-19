<x-guest-layout>
    <div class="mb-8">
        <p class="mb-3 text-xs font-bold uppercase tracking-[0.2em] text-lime-600">Welcome back</p>
        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Sign in to KYMNET</h2>
        <p class="mt-3 text-sm leading-6 text-slate-500">Pick up where you left off and get back on court.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="mt-5">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-lime-500 shadow-sm focus:ring-lime-400" name="remember">
                <span class="ms-2 text-sm text-slate-500">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="mt-7 flex flex-col-reverse items-center justify-between gap-4 sm:flex-row">
            @if (Route::has('password.request'))
            <a class="text-sm font-semibold text-slate-500 transition hover:text-slate-900" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="w-full justify-center sm:w-auto">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <p class="mt-8 text-center text-sm text-slate-500">New to KYMNET?
        <a href="{{ route('register') }}" class="font-bold text-slate-900 underline decoration-lime-400 decoration-2 underline-offset-4 hover:text-lime-700">Create an account</a>
    </p>
</x-guest-layout>
