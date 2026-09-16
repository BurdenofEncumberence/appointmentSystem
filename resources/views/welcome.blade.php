<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KYMNET - Book Your Court, Rally with Ease</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-slate-900 antialiased">

    {{-- Navbar --}}
    <header class="border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-slate-900 flex items-center justify-center">
                    <span class="text-lime-400 font-bold text-sm">K</span>
                </div>
                <div>
                    <span class="text-xl font-extrabold tracking-tight">KYMNET</span>
                    <span class="ml-2 inline-block bg-lime-100 text-lime-700 text-[10px] font-semibold px-2 py-0.5 rounded-full align-middle">
                        #1 PICKLEBALL COURT ARENA
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}"
                   class="bg-slate-900 text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:bg-slate-800 transition">
                    Login
                </a>
                <a href="{{ route('register') }}"
                   class="bg-slate-900 text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:bg-slate-800 transition">
                    Register
                </a>
            </div>
        </div>
    </header>

    {{-- Hero --}}
    <section class="max-w-7xl mx-auto px-6 py-20 grid md:grid-cols-2 gap-12 items-center">
        <div>
            <h1 class="text-5xl font-extrabold leading-tight">
                Book Your Court<br>
                <span class="text-lime-500">Rally with Ease</span>
            </h1>
            <p class="mt-6 text-slate-500 max-w-md leading-relaxed">
                KYMNET brings everything pickleball under one roof. Reserve court time,
                join skill-matched games, and gear up—all from one seamless platform.
            </p>
        </div>

        <div class="rounded-3xl overflow-hidden shadow-xl">
            <img src="https://images.unsplash.com/photo-1595435742656-5272d0b3fa82?auto=format&fit=crop&w=1200&q=80"
                 alt="Players on a pickleball court at sunset"
                 class="w-full h-full object-cover">
        </div>
    </section>

    {{-- Features --}}
    <section class="bg-slate-50 py-20">
        <div class="max-w-4xl mx-auto text-center px-6">
            <h2 class="text-4xl font-extrabold">Built for the modern player</h2>
            <p class="mt-4 text-slate-500">
                Say goodbye to endless phone calls and waiting lists. Our platform streamlines
                every aspect of your game prep.
            </p>
        </div>

        <div class="max-w-6xl mx-auto px-6 mt-14 grid md:grid-cols-3 gap-8">

            {{-- Real-Time Availability --}}
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-100">
                <div class="w-12 h-12 rounded-xl bg-lime-100 flex items-center justify-center mb-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-lime-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0V11.25A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6l2.25 2.25L16.5 12" />
                    </svg>
                </div>
                <h3 class="font-bold text-lg mb-2">Real-Time Availability</h3>
                <p class="text-slate-500 text-sm leading-relaxed">
                    See live schedule updates directly from premier regional clubs and outdoor centers.
                </p>
            </div>

            {{-- Instant Booking --}}
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-100">
                <div class="w-12 h-12 rounded-xl bg-lime-100 flex items-center justify-center mb-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-lime-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12.75 10.5h7.5l-10.5 11.25L11.25 13.5h-7.5z" />
                    </svg>
                </div>
                <h3 class="font-bold text-lg mb-2">Instant Booking</h3>
                <p class="text-slate-500 text-sm leading-relaxed">
                    Secure your time slot in less than 30 seconds with clean, secure checkouts.
                </p>
            </div>

            {{-- Court Preferences --}}
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-100">
                <div class="w-12 h-12 rounded-xl bg-lime-100 flex items-center justify-center mb-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-lime-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m9 12h3.75M16.5 18a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0H13.5M10.5 12h9.75M10.5 12a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 12H7.5" />
                    </svg>
                </div>
                <h3 class="font-bold text-lg mb-2">Court Preferences</h3>
                <p class="text-slate-500 text-sm leading-relaxed">
                    Filter by indoor/outdoor, professional cushion surfaces, covered courts, or night lighting.
                </p>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-slate-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-4 gap-10">
            <div>
                <span class="text-2xl font-extrabold">KYMNET</span>
                <p class="mt-4 text-slate-400 text-sm max-w-xs leading-relaxed">
                    Premium court booking software designed for modern players. Real-time availability,
                    hassle-free reservations, and instant match setups.
                </p>
            </div>

            <div>
                <h4 class="text-xs font-semibold tracking-wider text-slate-400 mb-4">EXPLORE</h4>
                <ul class="space-y-2 text-sm text-slate-300">
                    <li><a href="#" class="hover:text-white">Find Courts</a></li>
                    <li><a href="#" class="hover:text-white">Host Tournament</a></li>
                    <li><a href="#" class="hover:text-white">Find Partners</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-xs font-semibold tracking-wider text-slate-400 mb-4">FOR USERS</h4>
                <ul class="space-y-2 text-sm text-slate-300">
                    <li><a href="#" class="hover:text-white">Support</a></li>
                    <li><a href="#" class="hover:text-white">Software Features</a></li>
                    <li><a href="#" class="hover:text-white">Pricing</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-xs font-semibold tracking-wider text-slate-400 mb-4">COMPANY</h4>
                <ul class="space-y-2 text-sm text-slate-300">
                    <li><a href="#" class="hover:text-white">About Us</a></li>
                    <li><a href="#" class="hover:text-white">Contact Support</a></li>
                    <li><a href="#" class="hover:text-white">Careers</a></li>
                </ul>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 mt-12 pt-8 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-slate-400">
            <span>© {{ date('Y') }} KYMNET Inc. All rights reserved.</span>
            <div class="flex gap-6">
                <a href="#" class="hover:text-white">Privacy Policy</a>
                <a href="#" class="hover:text-white">Terms of Service</a>
            </div>
        </div>
    </footer>

</body>
</html>