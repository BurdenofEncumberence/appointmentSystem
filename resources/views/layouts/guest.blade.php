<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'KYMNET') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-slate-900 antialiased bg-slate-50">
    {{-- Top nav --}}
{{-- Top nav --}}
<x-public-navbar />

    <main class="min-h-[calc(100vh-4rem)] px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto flex min-h-[calc(100vh-5.5rem)] max-w-6xl overflow-hidden rounded-2xl bg-white shadow-xl shadow-slate-200/60 lg:grid lg:grid-cols-[0.95fr_1.05fr]">
            {{-- Left visual panel --}}
            <section class="relative hidden overflow-hidden bg-slate-900 lg:block">
                <img src="https://images.unsplash.com/photo-1595435742656-5272d0b3fa82?auto=format&fit=crop&w=1200&q=80"
                     alt="Players on a pickleball court at sunset"
                     class="absolute inset-0 h-full w-full object-cover opacity-50">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/60 to-transparent"></div>

                <div class="relative flex h-full flex-col justify-between p-10 xl:p-12">
                    <a href="/" class="flex items-center gap-2.5">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-lime-400 text-sm font-extrabold text-slate-950">K</span>
                        <span class="text-xl font-extrabold tracking-tight text-white">KYMNET</span>
                    </a>

                    <div>
                        <p class="mb-3 text-[11px] font-bold uppercase tracking-[0.2em] text-lime-300">
                            Your court. Your game.
                        </p>
                        <h1 class="max-w-xs text-4xl font-extrabold leading-[1.15] text-white xl:text-[2.75rem]">
                            Book the next rally.
                        </h1>
                        <p class="mt-4 max-w-xs text-sm leading-relaxed text-slate-300">
                            Reserve court time, find your people, and keep every match moving.
                        </p>
                    </div>
                </div>
            </section>

            {{-- Right form panel --}}
            <section class="flex flex-1 flex-col px-6 py-8 sm:px-10 sm:py-10 lg:px-14 lg:py-12">
                <div class="my-auto w-full max-w-md py-8 lg:py-12">
                    {{ $slot }}
                </div>
                <p class="mt-auto text-center text-xs text-slate-400">
                    © {{ date('Y') }} KYMNET Inc. · Premium court booking for modern players.
                </p>
            </section>
        </div>
    </main>
</body>
</html>