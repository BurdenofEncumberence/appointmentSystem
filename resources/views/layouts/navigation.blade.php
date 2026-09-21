<nav x-data="{ mobileOpen: false, profileOpen: false }" class="double-rule relative">
    <div class="max-w-6xl mx-auto px-6">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center gap-8">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <div class="seal" aria-hidden="true" id="nav-seal"></div>
                    <span class="font-pixel text-sm">KYMNET</span>
                </a>

                <div class="hidden sm:flex gap-6 font-pixel text-[10px]">
                    <a href="{{ route('dashboard') }}"
                       @if(request()->routeIs('dashboard')) style="color: var(--red); border-bottom: 2px solid var(--red);" @endif>
                        Dashboard
                    </a>
                    <a href="{{ route('booking') }}"
                       @if(request()->routeIs('booking')) style="color: var(--red); border-bottom: 2px solid var(--red);" @endif>
                        Book Courts
                    </a>
                    <a href="{{ route('bookings.index') }}"
                       @if(request()->routeIs('bookings.index')) style="color: var(--red); border-bottom: 2px solid var(--red);" @endif>
                        Courts Booked
                    </a>
                </div>
            </div>

            <div class="hidden sm:flex items-center gap-4 relative">
                <button @click="profileOpen = !profileOpen" @click.outside="profileOpen = false"
                        class="pixel-btn text-[10px]" style="background: var(--parchment);">
                    {{ Auth::user()->name ?? 'Account' }}
                </button>
                <div x-show="profileOpen" x-cloak
                     class="absolute right-0 top-full mt-2 w-44 pixel-border z-40" style="background: var(--cream);">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-3 text-lg hover:opacity-70">
                        Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-3 text-lg hover:opacity-70" style="color: var(--red);">
                            Log Out
                        </button>
                    </form>
                </div>
            </div>

            <div class="sm:hidden">
                <button @click="mobileOpen = !mobileOpen" class="pixel-btn px-3 py-2" style="background: var(--parchment);">
                    <span class="font-pixel text-[10px]">MENU</span>
                </button>
            </div>
        </div>
    </div>

    <div x-show="mobileOpen" x-cloak class="sm:hidden pixel-border mx-6 mb-4" style="background: var(--cream);">
        <a href="{{ route('dashboard') }}" class="block px-4 py-3 text-lg" style="border-bottom: 1px solid var(--ink);">Dashboard</a>
        <a href="{{ route('booking') }}" class="block px-4 py-3 text-lg" style="border-bottom: 1px solid var(--ink);">Book Courts</a>
        <a href="{{ route('bookings.index') }}" class="block px-4 py-3 text-lg" style="border-bottom: 1px solid var(--ink);">Courts Booked</a>
        <a href="{{ route('profile.edit') }}" class="block px-4 py-3 text-lg" style="border-bottom: 1px solid var(--ink);">Profile</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-3 text-lg" style="color: var(--red);">Log Out</button>
        </form>
    </div>
</nav>

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
    renderPixelGrid('nav-seal', [
        "........", ".G....G.", "..GGGG..", ".G.GG.G.",
        ".G.GG.G.", "..GGGG..", ".G....G.", "........"
    ], { '.': 'transparent', 'G': '#E3A857' });
</script>