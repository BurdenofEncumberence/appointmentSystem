@props(['title' => null, 'heading' => "Today's Schedule"])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? "Staff Desk · Today's Schedule · KYMNET" }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased gz-app-shell">
    <div class="grain"></div>

    <div class="min-h-screen">
        <div style="background: var(--gz-bg); position: sticky; top: 0; z-index: 50;">
            @include('layouts.navigation')
        </div>

        <div class="gz-container relative" style="z-index: 1;">
            <main>
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>