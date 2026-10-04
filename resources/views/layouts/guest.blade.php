@props([
    'title' => 'KYMNET',
    'label' => 'Account',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
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
        <section class="max-w-6xl mx-auto px-6 py-16 flex justify-center" aria-label="{{ $label }}">
            <div class="gz-card w-full">
                {{ $slot }}
            </div>
        </section>
    </main>

    <x-site-footer />
</body>
</html>