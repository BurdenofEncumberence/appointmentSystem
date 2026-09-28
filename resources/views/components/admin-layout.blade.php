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
    <div class="min-h-screen">
        {{-- Consistent top navbar --}}
        @include('layouts.navigation')

        <div class="gz-container">
            {{-- Admin sub-navigation banner --}}
            <div class="gz-panel gz-panel-body mb-6 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="gz-badge gz-badge-danger">HQ OPERATIONS</span>
                    <span class="gz-font-display font-bold text-base">{{ $heading }}</span>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.dashboard') }}"
                       class="{{ request()->routeIs('admin.dashboard') ? 'gz-btn-primary' : 'gz-btn-outline' }} gz-btn-sm">
                        Overview
                    </a>
                    <a href="{{ route('admin.courts.index') }}"
                       class="{{ request()->routeIs('admin.courts.*') ? 'gz-btn-primary' : 'gz-btn-outline' }} gz-btn-sm">
                        Courts
                    </a>
                    <a href="{{ route('admin.finance') }}"
                       class="{{ request()->routeIs('admin.finance') ? 'gz-btn-primary' : 'gz-btn-outline' }} gz-btn-sm">
                        Financials
                    </a>
                </div>
            </div>

            <main>
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>