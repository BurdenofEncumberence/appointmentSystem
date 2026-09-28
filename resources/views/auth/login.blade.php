<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log In — KYMNET</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased relative gz-app-shell">
    <div class="grain"></div>

    <a href="#main-content" class="skip-link">Skip to main content</a>

    <x-site-header />

    <main id="main-content" class="relative z-10">
        <section class="max-w-6xl mx-auto px-6 py-16 flex justify-center" aria-label="Log in">
            <div class="gz-card w-full">
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

                    <div class="mt-5">
                        <label for="password" class="gz-label">Password</label>
                        <input id="password" class="gz-input" type="password" name="password" required autocomplete="current-password">
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
            </div>
        </section>
    </main>

    <x-site-footer />
</body>
</html>