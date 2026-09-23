<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'KYMNET') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div class="min-h-screen flex flex-col">
        @include('layouts.navigation')

        <div class="flex-1 flex flex-col items-center justify-center px-6 py-10">
            <div class="w-full sm:max-w-md pixel-border bg-[color:var(--cream)] px-8 py-8">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>