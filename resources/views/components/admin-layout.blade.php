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
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
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

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.dashboard') }}"
                   class="pixel-btn text-[10px] py-1.5 px-3 {{ request()->routeIs('admin.dashboard') ? 'bg-[color:var(--ink)] text-[color:var(--cream)]' : 'bg-[color:var(--parchment)]' }}">
                    Overview
                </a>
                <a href="{{ route('admin.courts.index') }}"
                   class="pixel-btn text-[10px] py-1.5 px-3 {{ request()->routeIs('admin.courts.*') ? 'bg-[color:var(--ink)] text-[color:var(--cream)]' : 'bg-[color:var(--parchment)]' }}">
                    Courts
                </a>
                <a href="{{ route('admin.finance') }}"
                   class="pixel-btn text-[10px] py-1.5 px-3 {{ request()->routeIs('admin.finance') ? 'bg-[color:var(--ink)] text-[color:var(--cream)]' : 'bg-[color:var(--parchment)]' }}">
                    Financials
                </a>
            </div>
        </div>

        <main class="max-w-6xl mx-auto px-6 py-6">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
