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
    <div class="min-h-screen flex flex-col items-center justify-center px-6 py-12">
        <a href="/" class="flex items-center gap-3 mb-8">
            <div class="seal" aria-hidden="true" id="guest-seal"></div>
            <span class="font-pixel text-lg">KYMNET</span>
        </a>

        <div class="w-full sm:max-w-md pixel-border bg-[color:var(--cream)] px-8 py-8">
            {{ $slot }}
        </div>
    </div>

    <script>
        function renderPixelGrid(id, rows, colorMap) {
            const el = document.getElementById(id);
            if (!el) return;
            el.innerHTML = '';
            rows.forEach(row => {
                [...row].forEach(ch => {
                    const cell = document.createElement('div');
                    cell.style.background = colorMap[ch] || 'transparent';
                    el.appendChild(cell);
                });
            });
        }
        renderPixelGrid('guest-seal', [
            "........", ".G....G.", "..GGGG..", ".G.GG.G.",
            ".G.GG.G.", "..GGGG..", ".G....G.", "........"
        ], { '.': 'transparent', 'G': '#E3A857' });
    </script>
</body>
</html>