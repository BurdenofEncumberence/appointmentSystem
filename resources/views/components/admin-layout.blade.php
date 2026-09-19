@props(['title' => null, 'heading' => 'Overview'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin · KYMNET' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">
    <div class="min-h-screen lg:flex">
        <aside class="hidden w-64 shrink-0 border-r border-slate-200 bg-slate-950 text-white lg:flex lg:flex-col">
            <div class="flex h-20 items-center gap-3 border-b border-white/10 px-6"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-lime-400 text-sm font-extrabold text-slate-950">K</span><div><span class="block text-lg font-extrabold tracking-tight">KYMNET</span><span class="block text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Operations</span></div></div>
            <nav class="flex-1 space-y-1 px-3 py-6"><p class="px-3 pb-3 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Workspace</p><a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-lime-400 text-slate-950' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"><span class="text-base">⌂</span> Overview</a>@if(Auth::user()->hasPermission('manage_courts'))<a href="{{ route('admin.courts.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.courts.*') ? 'bg-lime-400 text-slate-950' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"><span class="text-base">▦</span> Courts</a>@endif @if(Auth::user()->hasPermission('view_finances'))<a href="{{ route('admin.finance') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.finance') ? 'bg-lime-400 text-slate-950' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"><span class="text-base">↗</span> Financials</a>@endif</nav>
            <div class="border-t border-white/10 p-4"><a href="{{ route('dashboard') }}" class="mb-3 flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold text-slate-400 transition hover:bg-white/10 hover:text-white">← Back to app</a><div class="flex items-center gap-3 rounded-xl bg-white/5 p-3"><span class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-700 text-xs font-bold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span><div class="min-w-0"><p class="truncate text-xs font-bold text-white">{{ Auth::user()->name }}</p><p class="truncate text-[11px] text-slate-500">Administrator</p></div></div></div>
        </aside>
        <div class="min-w-0 flex-1"><header class="sticky top-0 z-40 flex h-20 items-center justify-between border-b border-slate-200 bg-white/95 px-5 backdrop-blur sm:px-8 lg:px-10"><div><p class="text-xs font-bold uppercase tracking-[0.2em] text-lime-600">KYMNET operations</p><h1 class="mt-1 text-xl font-extrabold tracking-tight text-slate-900">{{ $heading }}</h1></div><div class="flex items-center gap-3"><span class="hidden text-right sm:block"><span class="block text-xs font-bold text-slate-700">{{ Auth::user()->name }}</span><span class="block text-[11px] text-slate-400">{{ now()->format('D, M j, Y') }}</span></span><span class="flex h-9 w-9 items-center justify-center rounded-full bg-lime-100 text-xs font-extrabold text-lime-700">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span></div></header><div class="border-b border-slate-200 bg-white px-5 py-3 lg:hidden"><div class="flex gap-2 overflow-x-auto"><a href="{{ route('admin.dashboard') }}" class="whitespace-nowrap rounded-lg px-3 py-2 text-xs font-bold {{ request()->routeIs('admin.dashboard') ? 'bg-slate-900 text-white' : 'text-slate-500' }}">Overview</a>@if(Auth::user()->hasPermission('manage_courts'))<a href="{{ route('admin.courts.index') }}" class="whitespace-nowrap rounded-lg px-3 py-2 text-xs font-bold {{ request()->routeIs('admin.courts.*') ? 'bg-slate-900 text-white' : 'text-slate-500' }}">Courts</a>@endif @if(Auth::user()->hasPermission('view_finances'))<a href="{{ route('admin.finance') }}" class="whitespace-nowrap rounded-lg px-3 py-2 text-xs font-bold {{ request()->routeIs('admin.finance') ? 'bg-slate-900 text-white' : 'text-slate-500' }}">Financials</a>@endif</div></div><main class="p-5 sm:p-8 lg:p-10">{{ $slot }}</main></div>
    </div>
</body>
</html>
