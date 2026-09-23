<nav x-data="{ mobileOpen: false, profileOpen: false }" class="double-rule relative">
    <div class="max-w-6xl mx-auto px-6">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center gap-8">
                @php
                    $homeUrl = match(true) {
                        Auth::check() && Auth::user()->isAdmin() => route('admin.dashboard'),
                        Auth::check() && Auth::user()->hasRole('staff') => route('staff.today'),
                        Auth::check() => route('dashboard'),
                        default => url('/'),
                    };
                @endphp
                <a href="{{ $homeUrl }}" class="flex items-center gap-2">
                    <div class="seal" aria-hidden="true" id="nav-seal"></div>
                    <span class="font-pixel text-sm">KYMNET</span>
                </a>

                <div class="hidden sm:flex gap-6 font-pixel text-[10px]">
                    @auth
                        @if(Auth::user()->isAdmin())
                            {{-- Admin only sees Admin Panel navigation --}}
                            <a href="{{ route('admin.dashboard') }}"
                               @if(request()->routeIs('admin.dashboard')) style="color: var(--red); border-bottom: 2px solid var(--red);" @endif>
                                Overview
                            </a>
                            <a href="{{ route('admin.courts.index') }}"
                               @if(request()->routeIs('admin.courts.*')) style="color: var(--red); border-bottom: 2px solid var(--red);" @endif>
                                Courts
                            </a>
                            <a href="{{ route('admin.finance') }}"
                               @if(request()->routeIs('admin.finance')) style="color: var(--red); border-bottom: 2px solid var(--red);" @endif>
                                Financials
                            </a>
                        @elseif(Auth::user()->hasRole('staff'))
                            {{-- Staff only sees Today's Schedule --}}
                            <a href="{{ route('staff.today') }}"
                               @if(request()->routeIs('staff.*')) style="color: var(--red); border-bottom: 2px solid var(--red);" @endif>
                                Today's Schedule
                            </a>
                        @else
                            {{-- Regular Player navigation --}}
                            <a href="{{ url('/') }}"
                               @if(request()->is('/')) style="color: var(--red); border-bottom: 2px solid var(--red);" @endif>
                                Home
                            </a>
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
                        @endif
                    @else
                        {{-- Guest navigation --}}
                        <a href="{{ url('/') }}"
                           @if(request()->is('/')) style="color: var(--red); border-bottom: 2px solid var(--red);" @endif>
                            Home
                        </a>
                        <a href="{{ route('booking') }}"
                           @if(request()->routeIs('booking')) style="color: var(--red); border-bottom: 2px solid var(--red);" @endif>
                            Book Courts
                        </a>
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex items-center gap-4 relative">
                @auth
                    <button @click="profileOpen = !profileOpen" @click.outside="profileOpen = false"
                            class="pixel-btn text-[10px] py-2 px-3" style="background: var(--parchment);">
                        {{ Auth::user()->name ?? 'Account' }}
                    </button>
                    <div x-show="profileOpen" x-cloak
                         class="absolute right-0 top-full mt-2 w-48 pixel-border z-40" style="background: var(--cream);">
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 text-lg hover:opacity-70">
                                Admin Overview
                            </a>
                            <a href="{{ route('admin.courts.index') }}" class="block px-4 py-3 text-lg hover:opacity-70">
                                Courts Inventory
                            </a>
                            <a href="{{ route('admin.finance') }}" class="block px-4 py-3 text-lg hover:opacity-70">
                                Financial Reports
                            </a>
                        @elseif(Auth::user()->hasRole('staff'))
                            <a href="{{ route('staff.today') }}" class="block px-4 py-3 text-lg hover:opacity-70">
                                Today's Schedule
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="block px-4 py-3 text-lg hover:opacity-70">
                                Dashboard
                            </a>
                            <a href="{{ route('bookings.index') }}" class="block px-4 py-3 text-lg hover:opacity-70">
                                Courts Booked
                            </a>
                        @endif
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-3 text-lg hover:opacity-70" style="border-top: 1px solid var(--ink);">
                            Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-3 text-lg hover:opacity-70" style="color: var(--red); border-top: 1px solid var(--ink);">
                                Log Out
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="pixel-btn text-[10px] py-2 px-3 bg-[color:var(--parchment)]"
                       @if(request()->routeIs('login')) style="border-color: var(--red); box-shadow: 0 0 0 2px var(--gold);" @endif>
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="pixel-btn text-[10px] py-2 px-3 text-[color:var(--cream)] bg-[color:var(--red)]"
                       @if(request()->routeIs('register')) style="box-shadow: 0 0 0 2px var(--gold);" @endif>
                        Register
                    </a>
                @endauth
            </div>

            <div class="sm:hidden flex items-center gap-2">
                <button @click="mobileOpen = !mobileOpen" class="pixel-btn px-3 py-2" style="background: var(--parchment);">
                    <span class="font-pixel text-[10px]">MENU</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Dropdown Menu --}}
    <div x-show="mobileOpen" x-cloak class="sm:hidden pixel-border mx-6 mb-4" style="background: var(--cream);">
        @auth
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 text-lg" style="border-bottom: 1px solid var(--ink);">Admin Overview</a>
                <a href="{{ route('admin.courts.index') }}" class="block px-4 py-3 text-lg" style="border-bottom: 1px solid var(--ink);">Courts</a>
                <a href="{{ route('admin.finance') }}" class="block px-4 py-3 text-lg" style="border-bottom: 1px solid var(--ink);">Financials</a>
            @elseif(Auth::user()->hasRole('staff'))
                <a href="{{ route('staff.today') }}" class="block px-4 py-3 text-lg" style="border-bottom: 1px solid var(--ink);">Today's Schedule</a>
            @else
                <a href="{{ url('/') }}" class="block px-4 py-3 text-lg" style="border-bottom: 1px solid var(--ink);">Home</a>
                <a href="{{ route('dashboard') }}" class="block px-4 py-3 text-lg" style="border-bottom: 1px solid var(--ink);">Dashboard</a>
                <a href="{{ route('booking') }}" class="block px-4 py-3 text-lg" style="border-bottom: 1px solid var(--ink);">Book Courts</a>
                <a href="{{ route('bookings.index') }}" class="block px-4 py-3 text-lg" style="border-bottom: 1px solid var(--ink);">Courts Booked</a>
            @endif
            <a href="{{ route('profile.edit') }}" class="block px-4 py-3 text-lg" style="border-bottom: 1px solid var(--ink);">Profile</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-3 text-lg" style="color: var(--red);">Log Out</button>
            </form>
        @else
            <a href="{{ url('/') }}" class="block px-4 py-3 text-lg" style="border-bottom: 1px solid var(--ink);">Home</a>
            <a href="{{ route('booking') }}" class="block px-4 py-3 text-lg" style="border-bottom: 1px solid var(--ink);">Book Courts</a>
            <a href="{{ route('login') }}" class="block px-4 py-3 text-lg" style="border-bottom: 1px solid var(--ink);">Login</a>
            <a href="{{ route('register') }}" class="block px-4 py-3 text-lg" style="border-bottom: 1px solid var(--ink);">Register</a>
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
                    el.appendChild(cell);
                });
            });
        };
    }
    window.renderPixelGrid('nav-seal', [
        "........", ".G....G.", "..GGGG..", ".G.GG.G.",
        ".G.GG.G.", "..GGGG..", ".G....G.", "........"
    ], { '.': 'transparent', 'G': '#E3A857' });
</script>