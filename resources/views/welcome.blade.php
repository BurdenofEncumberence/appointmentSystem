<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KYMNET - Book Your Court, Rally with Ease</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .dragon-icon {
            display: inline-block;
            width: 135px;
            height: auto;
            image-rendering: pixelated;
        }
        .event-travel-group.traveling .dragon-icon {
            animation: dragon-fly 5.0s ease-in-out;
        }
        @keyframes dragon-fly {
            0%   { transform: translateX(0) translateY(0); }
            50%  { transform: translateX(40px) translateY(-10px); }
            100% { transform: translateX(0) translateY(0); }
        }
    </style>
</head>
<body class="antialiased">

    {{-- Header --}}
    <div class="double-rule relative">
        <header class="max-w-6xl mx-auto px-6 py-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="seal" aria-hidden="true" id="brand-seal"></div>
                <span class="font-pixel text-lg">KYMNET</span>
            </div>
            <nav class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="pixel-btn bg-[color:var(--parchment)]">
                    Login
                </a>
                <a href="{{ route('register') }}" class="pixel-btn text-[color:var(--cream)] bg-[color:var(--red)]">
                    Register
                </a>
            </nav>
        </header>
        <div class="event-travel-group" id="dragon-toggle" role="button" tabindex="0" aria-label="Show upcoming events">
            <img
                src="{{ asset('images/dragon-static.png') }}"
                data-static="{{ asset('images/dragon-static.png') }}"
                data-animated="{{ asset('images/dragon.gif') }}"
                alt="Dragon"
                class="event-tab dragon-icon"
                id="dragon-img"
            >
            <div class="event-banner-strip pixel-border" style="background: var(--ink);">
                <span class="font-pixel text-[11px]" style="color: var(--gold);">GRAND OPENING TOURNAMENT — REGISTRATION OPENS SOON</span>
            </div>
        </div>
    </div>

    {{-- Hero --}}
    <section class="max-w-6xl mx-auto px-6 pt-20 pb-28 relative">
        <div class="grid md:grid-cols-2 gap-16 items-center relative">
            <div class="relative">
                <h1 class="font-pixel text-3xl md:text-4xl leading-relaxed">
                    Book Your Court.<br>
                    <span style="color: var(--red);">Rally With Ease.</span>
                </h1>
                <p class="mt-8 max-w-sm text-xl leading-relaxed">
                    KYMNET brings court reservations, tournaments, and match schedules
                    into one place. Pick a court, pick a time, and play.
                </p>
                <a href="{{ route('register') }}" class="pixel-btn text-[color:var(--cream)] bg-[color:var(--jade)] mt-10">
                    Reserve a Court
                </a>
            </div>

            <div class="pixel-border court-frame">
                <div class="court-line" style="top:8%; left:8%; right:8%; height:4px;"></div>
                <div class="court-line" style="bottom:8%; left:8%; right:8%; height:4px;"></div>
                <div class="court-line" style="top:8%; bottom:8%; left:8%; width:4px;"></div>
                <div class="court-line" style="top:8%; bottom:8%; right:8%; width:4px;"></div>
                <div class="court-net"></div>
                <div class="court-dot" style="background: var(--gold); left: 30%; top: 40%;"></div>
                <div class="court-dot" style="background: var(--red); left: 65%; top: 60%;"></div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="max-w-4xl mx-auto px-6 pb-24">
        <h2 class="font-pixel text-2xl mb-10">Built for the modern player</h2>

        <div class="pixel-border bg-[color:var(--cream)]">
            <div class="ledger-row flex items-start gap-5 p-6">
                <div class="seal" id="seal-availability"></div>
                <div>
                    <h3 class="font-pixel text-base mb-2">Real-Time Availability</h3>
                    <p class="text-lg leading-relaxed">
                        See live court schedules the moment a slot opens or closes, no phone calls needed.
                    </p>
                </div>
            </div>
            <div class="ledger-row flex items-start gap-5 p-6">
                <div class="seal" id="seal-booking"></div>
                <div>
                    <h3 class="font-pixel text-base mb-2">Instant Booking</h3>
                    <p class="text-lg leading-relaxed">
                        Reserve a court in under a minute, with a clear summary before you pay.
                    </p>
                </div>
            </div>
            <div class="flex items-start gap-5 p-6">
                <div class="seal" id="seal-preferences"></div>
                <div>
                    <h3 class="font-pixel text-base mb-2">Court Preferences</h3>
                    <p class="text-lg leading-relaxed">
                        Filter by indoor or outdoor, covered courts, or night lighting.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer style="background: var(--ink); color: var(--cream);">
        <div style="border-top: 6px solid var(--red); box-shadow: inset 0 4px 0 -1px var(--gold);">
            <div class="max-w-6xl mx-auto px-6 py-14 grid md:grid-cols-4 gap-10">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="seal" id="footer-seal"></div>
                        <span class="font-pixel text-base">KYMNET</span>
                    </div>
                    <p class="text-lg opacity-80 max-w-xs leading-relaxed">
                        Court booking for the Davao pickleball community. Built by players, for players.
                    </p>
                </div>

                <div>
                    <h4 class="font-pixel text-xs mb-4">Explore</h4>
                    <ul class="space-y-2 text-lg opacity-80">
                        <li><a href="#" class="hover:opacity-100" style="color: var(--gold);">Find courts</a></li>
                        <li><a href="#" class="hover:opacity-100" style="color: var(--gold);">Host a tournament</a></li>
                        <li><a href="#" class="hover:opacity-100" style="color: var(--gold);">Find partners</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-pixel text-xs mb-4">For users</h4>
                    <ul class="space-y-2 text-lg opacity-80">
                        <li><a href="#" class="hover:opacity-100" style="color: var(--gold);">Support</a></li>
                        <li><a href="#" class="hover:opacity-100" style="color: var(--gold);">Pricing</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-pixel text-xs mb-4">Company</h4>
                    <ul class="space-y-2 text-lg opacity-80">
                        <li><a href="#" class="hover:opacity-100" style="color: var(--gold);">About us</a></li>
                        <li><a href="#" class="hover:opacity-100" style="color: var(--gold);">Contact</a></li>
                    </ul>
                </div>
            </div>
            <div class="max-w-6xl mx-auto px-6 pb-8 text-base opacity-60">
                © {{ date('Y') }} KYMNET. All rights reserved.
            </div>
        </div>
    </footer>

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

        const sealRows = {
            'brand-seal': [
                "........",".G....G.","..GGGG..",".G.GG.G.",
                ".G.GG.G.","..GGGG..",".G....G.","........"
            ],
            'seal-availability': [
                "........",".G....G.","..GGGG..",".G.GG.G.",
                ".G.GG.G.","..GGGG..",".G....G.","........"
            ],
            'seal-booking': [
                "...G....","..GG....",".GGG....","GGGGGGG.",
                "....GGG.","....GG..","....G...","........"
            ],
            'seal-preferences': [
                "........",".GGGG...","...G....",".GGGGGG.",
                "...G....",".GG.....","...G....","........"
            ],
            'footer-seal': [
                "........",".G....G.","..GGGG..",".G.GG.G.",
                ".G.GG.G.","..GGGG..",".G....G.","........"
            ]
        };
        Object.keys(sealRows).forEach(id => {
            renderPixelGrid(id, sealRows[id], { '.': 'transparent', 'G': '#E3A857' });
        });

        // Dragon: sits on a static frame until clicked. The GIF is only
        // swapped in for the duration of the fly animation, then swapped
        // back out, so it never idles/loops on its own.
        const travelGroup = document.getElementById('dragon-toggle');
        const dragonImg = document.getElementById('dragon-img');

        function startTravel() {
            if (travelGroup.classList.contains('traveling')) return;
            dragonImg.src = dragonImg.dataset.animated; // reloading the src restarts the GIF at frame 0
            travelGroup.classList.add('traveling');
        }

        travelGroup.addEventListener('animationend', () => {
            travelGroup.classList.remove('traveling');
            dragonImg.src = dragonImg.dataset.static;
        });

        travelGroup.addEventListener('click', startTravel);
        travelGroup.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                startTravel();
            }
        });
    </script>

</body>
</html>