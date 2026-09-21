<nav class="sticky top-0 z-50 border-b border-slate-100 bg-white">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
        <a href="/" class="flex items-center gap-3" aria-label="KYMNET home">
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-900 text-sm font-extrabold text-lime-400">K</span>
            <span class="text-xl font-extrabold tracking-tight text-slate-900">KYMNET</span>
            <span class="hidden rounded-full bg-lime-100 px-2 py-1 text-[10px] font-semibold text-lime-700 sm:inline-block">
                #1 PICKLEBALL COURT ARENA
            </span>
        </a>

        <div class="flex items-center gap-2 sm:gap-3">
            <a href="{{ route('login') }}"
               class="rounded-lg px-3 py-2 text-sm font-semibold transition
                      {{ request()->routeIs('login') ? 'text-slate-900' : 'text-slate-500 hover:text-slate-900' }}">
                Log in
            </a>
            <a href="{{ route('register') }}"
               class="rounded-lg px-4 py-2.5 text-sm font-bold transition
                      {{ request()->routeIs('register')
                          ? 'bg-lime-400 text-slate-950'
                          : 'bg-slate-900 text-white hover:bg-lime-400 hover:text-slate-950' }}">
                Join KYMNET
            </a>
        </div>
    </div>
</nav>