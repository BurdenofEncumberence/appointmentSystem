<nav x-data="{ mobileOpen: false, profileOpen: false }" class="border-b relative" style="border-color: var(--gz-border);">
    <div class="max-w-6xl mx-auto px-6">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center gap-8">
                @php
                    $homeUrl = match(true) {
                        Auth::check() && Auth::user()->isAdmin() => route('admin.dashboard'),
                        Auth::check() && Auth::user()->hasRole('staff') => route('staff.today'),
                        Auth::check() => route('booking'),
                        default => url('/'),
                    };
                @endphp
                <a href="{{ $homeUrl }}" class="flex items-center gap-2">
                    <div class="pixel-mark" aria-hidden="true" style="width:32px; height:32px; background: var(--gz-ink); display:grid; grid-template-columns:repeat(8,1fr); grid-template-rows:repeat(8,1fr); padding:6px;" id="nav-seal"></div>
                    <span class="gz-font-display font-bold text-base">KYMNET</span>
                </a>

                <div class="hidden sm:flex gap-6 text-sm font-semibold" style="color: var(--gz-muted);">
                    @auth
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="nav-link" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>
                                Overview
                            </a>
                            <a href="{{ route('admin.courts.index') }}" class="nav-link" @if(request()->routeIs('admin.courts.*')) aria-current="page" @endif>
                                Courts
                            </a>
                            <a href="{{ route('admin.finance') }}" class="nav-link" @if(request()->routeIs('admin.finance')) aria-current="page" @endif>
                                Financials
                            </a>
                        @elseif(Auth::user()->hasRole('staff'))
                            <a href="{{ route('staff.today') }}" class="nav-link" @if(request()->routeIs('staff.*')) aria-current="page" @endif>
                                Today's Schedule
                            </a>
                        @else
                            <a href="{{ url('/') }}" class="nav-link" @if(request()->is('/')) aria-current="page" @endif>
                                Home
                            </a>
                            <a href="{{ route('booking') }}" class="nav-link" @if(request()->routeIs('booking')) aria-current="page" @endif>
                                Book Courts
                            </a>
                            <a href="{{ route('bookings.index') }}" class="nav-link" @if(request()->routeIs('bookings.index')) aria-current="page" @endif>
                                Courts Booked
                            </a>
                        @endif
                    @else
                        <a href="{{ url('/') }}" class="nav-link" @if(request()->is('/')) aria-current="page" @endif>
                            Home
                        </a>
                        <a href="{{ route('booking') }}" class="nav-link" @if(request()->routeIs('booking')) aria-current="page" @endif>
                            Book Courts
                        </a>
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex items-center gap-4 relative">
                @auth
                    <button @click="profileOpen = !profileOpen" @click.outside="profileOpen = false"
                            :aria-expanded="profileOpen.toString()"
                            class="gz-btn-outline gz-btn-sm">
                        {{ Auth::user()->name ?? 'Account' }}
                    </button>
                    <div x-show="profileOpen" x-cloak
                         class="absolute right-0 top-full mt-2 w-52 gz-panel z-40">
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 text-sm hover:bg-[color:var(--gz-bg)]">Admin Overview</a>
                            <a href="{{ route('admin.courts.index') }}" class="block px-4 py-3 text-sm hover:bg-[color:var(--gz-bg)]">Courts Inventory</a>
                            <a href="{{ route('admin.finance') }}" class="block px-4 py-3 text-sm hover:bg-[color:var(--gz-bg)]">Financial Reports</a>
                        @elseif(Auth::user()->hasRole('staff'))
                            <a href="{{ route('staff.today') }}" class="block px-4 py-3 text-sm hover:bg-[color:var(--gz-bg)]">Today's Schedule</a>
                        @else
                            <a href="{{ route('booking') }}" class="block px-4 py-3 text-sm hover:bg-[color:var(--gz-bg)]">Book Courts</a>
                            <a href="{{ route('bookings.index') }}" class="block px-4 py-3 text-sm hover:bg-[color:var(--gz-bg)]">Courts Booked</a>
                        @endif
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-3 text-sm hover:bg-[color:var(--gz-bg)]" style="border-top: 1px solid var(--gz-border);">
                            Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-3 text-sm hover:bg-[color:var(--gz-bg)]" style="color: var(--gz-danger); border-top: 1px solid var(--gz-border);">
                                Log Out
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="gz-btn-outline gz-btn-sm" @if(request()->routeIs('login')) aria-current="page" @endif>
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="gz-btn-primary gz-btn-sm" @if(request()->routeIs('register')) aria-current="page" @endif>
                        Register
                    </a>
                @endauth
            </div>

            <div class="sm:hidden flex items-center gap-2">
                <button @click="mobileOpen = !mobileOpen" :aria-expanded="mobileOpen.toString()" aria-controls="mobile-nav-genz" class="gz-btn-outline gz-btn-sm">
                    <span x-show="!mobileOpen">Menu</span>
                    <span x-show="mobileOpen" x-cloak>Close</span>
                </button>
            </div>
        </div>
    </div>

    <div id="mobile-nav-genz" x-show="mobileOpen" x-cloak class="sm:hidden gz-panel mx-6 mb-4">
        @auth
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 text-sm" style="border-bottom: 1px solid var(--gz-border);">Admin Overview</a>
                <a href="{{ route('admin.courts.index') }}" class="block px-4 py-3 text-sm" style="border-bottom: 1px solid var(--gz-border);">Courts</a>
                <a href="{{ route('admin.finance') }}" class="block px-4 py-3 text-sm" style="border-bottom: 1px solid var(--gz-border);">Financials</a>
            @elseif(Auth::user()->hasRole('staff'))
                <a href="{{ route('staff.today') }}" class="block px-4 py-3 text-sm" style="border-bottom: 1px solid var(--gz-border);">Today's Schedule</a>
            @else
                <a href="{{ url('/') }}" class="block px-4 py-3 text-sm" style="border-bottom: 1px solid var(--gz-border);">Home</a>
                <a href="{{ route('booking') }}" class="block px-4 py-3 text-sm" style="border-bottom: 1px solid var(--gz-border);">Book Courts</a>
                <a href="{{ route('bookings.index') }}" class="block px-4 py-3 text-sm" style="border-bottom: 1px solid var(--gz-border);">Courts Booked</a>
            @endif
            <a href="{{ route('profile.edit') }}" class="block px-4 py-3 text-sm" style="border-bottom: 1px solid var(--gz-border);">Profile</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-3 text-sm" style="color: var(--gz-danger);">Log Out</button>
            </form>
        @else
            <a href="{{ url('/') }}" class="block px-4 py-3 text-sm" style="border-bottom: 1px solid var(--gz-border);">Home</a>
            <a href="{{ route('booking') }}" class="block px-4 py-3 text-sm" style="border-bottom: 1px solid var(--gz-border);">Book Courts</a>
            <a href="{{ route('login') }}" class="block px-4 py-3 text-sm" style="border-bottom: 1px solid var(--gz-border);">Login</a>
            <a href="{{ route('register') }}" class="block px-4 py-3 text-sm" style="border-bottom: 1px solid var(--gz-border);">Register</a>
        @endauth
    </div>
</nav>

<script>
    if (typeof window.renderPixelGrid === 'undefined') {
        window.renderPixelGrid = function(id, rows, colorMap) {
            const el = document.getElementById(id);
            if (!el) return;
            el.innerHTML = '';
            rows.forEach(row => {
                [...row].forEach(ch => {
                    const cell = document.createElement('div');
                    cell.style.background = colorMap[ch] || 'transparent';
                    cell.style.width = '100%';
                    cell.style.height = '100%';
                    el.appendChild(cell);
                });
            });
        };
    }
    window.renderPixelGrid('nav-seal', [
        "........", ".W....W.", "..WWWW..", ".W.WW.W.",
        ".W.WW.W.", "..WWWW..", ".W....W.", "........"
    ], { '.': 'transparent', 'W': '#FCFBF7' });
</script>