@props(['title' => null, 'heading' => 'Overview'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin Operations · KYMNET' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased gz-app-shell">
    <div class="grain"></div>

    <div class="min-h-screen">
        {{-- Consistent top navbar --}}
        @include('layouts.navigation')

        {{-- Admin sub-navigation banner --}}
        <div class="pixel-border mx-6 mt-6 max-w-6xl md:mx-auto p-4 flex flex-wrap items-center justify-between gap-4" style="background: var(--cream);">
            <div class="flex items-center gap-3">
                <span class="font-pixel text-[10px] px-2.5 py-1 pixel-border" style="background: var(--red); color: var(--cream);">
                    HQ OPERATIONS
                </span>
                <span class="font-pixel text-sm tracking-wide" style="color: var(--ink);">
                    {{ $heading }}
                </span>
            </div>

            <div class="flex items-center gap-3">
                <span class="font-pixel text-[9px] px-2.5 py-1 pixel-border" style="background: var(--parchment); color: var(--ink);">
                    ● SYSTEM ACTIVE
                </span>
            </div>
        </div>

        <div class="gz-container relative" style="z-index: 1;">
            <main>
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>