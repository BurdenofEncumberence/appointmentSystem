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
        :root {
            --ink: #1A1611;
            --red: #B3261E;
            --gold: #E3A857;
            --parchment: #F3E5C8;
            --jade: #2F6F4E;
            --cream: #FDF6E3;
        }

        * { border-radius: 0 !important; }

        body {
            background-color: var(--parchment);
            color: var(--ink);
            font-family: 'VT323', monospace;
            font-size: 20px;
            background-image:
                repeating-linear-gradient(0deg, rgba(26,22,17,0.03) 0px, rgba(26,22,17,0.03) 1px, transparent 1px, transparent 4px);
        }

        .font-pixel {
            font-family: 'Press Start 2P', monospace;
            line-height: 1.6;
        }

        .double-rule {
            border-bottom: 6px solid var(--red);
            box-shadow: 0 8px 0 -2px var(--gold);
            margin-bottom: 8px;
        }

        .pixel-border {
            border: 3px solid var(--ink);
            box-shadow: 6px 6px 0 var(--ink);
        }

        .pixel-btn {
            font-family: 'Press Start 2P', monospace;
            font-size: 12px;
            border: 3px solid var(--ink);
            box-shadow: 5px 5px 0 var(--ink);
            transition: transform 0.08s ease, box-shadow 0.08s ease;
            display: inline-block;
            padding: 14px 20px;
        }
        .pixel-btn:active {
            transform: translate(5px, 5px);
            box-shadow: 0 0 0 var(--ink);
        }

        .seal {
            width: 44px;
            height: 44px;
            background: var(--red);
            border: 2px solid var(--ink);
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            grid-template-rows: repeat(8, 1fr);
            flex-shrink: 0;
        }
        .seal div { width: 100%; height: 100%; }

        .event-tab {
            position: absolute;
            top: -1px;
            right: 40px;
            padding: 10px 18px;
            cursor: pointer;
            transition: transform 0.7s cubic-bezier(0.22, 0.68, 0.32, 1);
            z-index: 30;
        }
        .event-tab.open {
            transform: translate(-78vw, 46px);
        }
        @media (max-width: 640px) {
            .event-tab.open {
                transform: translate(-55vw, 46px);
            }
        }

        .event-ticker-wrap {
            position: absolute;
            top: 45px;
            right: 0;
            height: 40px;
            width: 0;
            overflow: hidden;
            background: var(--ink);
            transition: width 0.7s cubic-bezier(0.22, 0.68, 0.32, 1);
            z-index: 25;
        }
        .event-ticker-wrap.open {
            width: 100%;
        }
        .event-ticker-track {
            display: inline-flex;
            white-space: nowrap;
            animation: ticker-scroll 14s linear infinite;
            animation-play-state: paused;
            padding-top: 9px;
        }
        .event-ticker-wrap.open .event-ticker-track {
            animation-play-state: running;
        }
        .event-ticker-track span {
            color: var(--gold);
            font-family: 'Press Start 2P', monospace;
            font-size: 11px;
            padding-right: 60px;
        }
        @keyframes ticker-scroll {
            from { transform: translateX(0); }
            to { transform: translateX(-50%); }
        }
        @media (prefers-reduced-motion: reduce) {
            .event-tab.open { transition: none; }
            .event-ticker-wrap.open { transition: none; }
            .event-ticker-track { animation-duration: 40s; }
        }

        .court-frame {
            aspect-ratio: 4 / 3;
            background: var(--jade);
            position: relative;
            overflow: hidden;
        }
        .court-line {
            position: absolute;
            background: var(--cream);
        }
        .court-net {
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 6px;
            background: repeating-linear-gradient(0deg, var(--ink), var(--ink) 4px, transparent 4px, transparent 8px);
            transform: translateX(-50%);
        }
        .court-dot {
            position: absolute;
            width: 10px;
            height: 10px;
        }

        .ledger-row {
            border-bottom: 3px dashed var(--ink);
        }
        .ledger-row:last-child { border-bottom: none; }
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
        <div class="event-tab pixel-border bg-[color:var(--cream)]" id="dragon-toggle" role="button" tabindex="0" aria-label="Show upcoming events">
            <span class="font-pixel text-[10px]">EVENTS</span>
        </div>
        <div class="event-ticker-wrap" id="event-ticker-wrap">
            <div class="event-ticker-track">
                <span>GRAND OPENING TOURNAMENT — REGISTRATION OPENS SOON</span>
                <span>GRAND OPENING TOURNAMENT — REGISTRATION OPENS SOON</span>
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

        {{-- court frame is the only thing left on the right column now --}}
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

        const dragonToggle = document.getElementById('dragon-toggle');
        const tickerWrap = document.getElementById('event-ticker-wrap');
        function toggleBanner() {
            dragonToggle.classList.toggle('open');
            tickerWrap.classList.toggle('open');
        }
        dragonToggle.addEventListener('click', toggleBanner);
        dragonToggle.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleBanner();
            }
        });
    </script>

</body>
</html>