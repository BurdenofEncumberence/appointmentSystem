<nav x-data="{ mobileOpen: false, profileOpen: false }" class="border-b relative" style="border-color: var(--gz-border);">
    <div class="max-w-6xl mx-auto px-6">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center gap-8">
                @php
                    $homeUrl = match(true) {
                        Auth::check() && (Auth::user()->isAdmin() || Auth::user()->hasRole('manager')) => route('admin.dashboard'),
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
                        @if(Auth::user()->isAdmin() || Auth::user()->hasRole('manager'))
                            {{-- Admin/Manager only sees Admin Panel navigation --}}
                            <a href="{{ route('admin.dashboard') }}"
                               @if(request()->routeIs('admin.dashboard')) style="color: var(--red); border-bottom: 2px solid var(--red);" @endif>
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
                            {{-- <a href="{{ route('dashboard') }}"
                               @if(request()->routeIs('dashboard')) style="color: var(--red); border-bottom: 2px solid var(--red);" @endif>
                                Dashboard
                            </a> --}}
                            <a href="{{ route('booking') }}"
                               @if(request()->routeIs('booking')) style="color: var(--red); border-bottom: 2px solid var(--red);" @endif>
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
                    @php
                        $userShortName = Auth::user()->first_name 
                            ?: (\Illuminate\Support\Str::of(Auth::user()->name ?? 'Account')->before(' ')->value() ?: 'Account');
                    @endphp
                    <button @click="profileOpen = !profileOpen" @click.outside="profileOpen = false"
                            :aria-expanded="profileOpen.toString()"
                            class="gz-btn-outline gz-btn-sm flex items-center gap-2"
                            title="{{ Auth::user()->name }}">
                        <span class="max-w-[110px] truncate">{{ $userShortName }}</span>
                        <svg class="w-4 h-4 transition-transform duration-150 shrink-0" :class="{'rotate-180': profileOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="profileOpen" x-cloak
                         class="absolute right-0 top-full mt-2 w-56 gz-dropdown z-50 overflow-hidden"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1">
                        
                        <div class="px-4 py-3 border-b border-[color:var(--gz-border)] bg-[color:var(--gz-bg)]/40">
                            <p class="text-[10px] uppercase font-bold tracking-wider text-[color:var(--gz-muted)]">Signed in as</p>
                            <p class="text-sm font-bold truncate text-[color:var(--gz-ink)]" title="{{ Auth::user()->name }}">{{ $userShortName }}</p>
                            <p class="text-xs text-[color:var(--gz-muted)] truncate" title="{{ Auth::user()->email }}">{{ Auth::user()->email }}</p>
                            <div class="mt-1.5">
                                <span class="gz-badge-outline text-[10px] px-2 py-0.5 uppercase tracking-wider font-mono font-bold">
                                    {{ Auth::user()->isAdmin() ? 'ADMIN' : (Auth::user()->hasRole('manager') ? 'MANAGER' : (Auth::user()->hasRole('staff') ? 'STAFF' : 'PLAYER')) }}
                                </span>
                            </div>
                        </div>

                        <div class="py-1">
                            @if(Auth::user()->isAdmin() || Auth::user()->hasRole('manager'))
                                <a href="{{ route('admin.dashboard') }}" class="gz-dropdown-item">Admin Overview</a>
                                <a href="{{ route('admin.courts.index') }}" class="gz-dropdown-item">Courts Inventory</a>
                                <a href="{{ route('admin.finance') }}" class="gz-dropdown-item">Financial Reports</a>
                            @elseif(Auth::user()->hasRole('staff'))
                                <a href="{{ route('staff.today') }}" class="gz-dropdown-item">Today's Schedule</a>
                            @else
                                <a href="{{ route('booking') }}" class="gz-dropdown-item">Book Courts</a>
                                <a href="{{ route('bookings.index') }}" class="gz-dropdown-item">Courts Booked</a>
                            @endif

                            <div class="my-1 border-t border-[color:var(--gz-border)]"></div>

                            <a href="{{ route('profile.edit') }}" class="gz-dropdown-item">
                                Profile Settings
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left gz-dropdown-item font-semibold" style="color: var(--gz-danger);">
                                    Log Out
                                </button>
                            </form>
                        </div>
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

    <div id="mobile-nav-genz" x-show="mobileOpen" x-cloak class="sm:hidden gz-panel mx-6 mb-4 overflow-hidden">
        @auth
            <div class="px-4 py-3 border-b border-[color:var(--gz-border)] bg-[color:var(--gz-bg)]/40">
                <p class="text-[10px] uppercase font-bold tracking-wider text-[color:var(--gz-muted)]">Signed in as</p>
                <p class="text-sm font-bold truncate text-[color:var(--gz-ink)]" title="{{ Auth::user()->name }}">
                    {{ Auth::user()->first_name ?: (\Illuminate\Support\Str::of(Auth::user()->name ?? 'Account')->before(' ')->value() ?: 'Account') }}
                </p>
                <span class="gz-badge-outline text-[10px] px-2 py-0.5 uppercase tracking-wider font-mono font-bold mt-1 inline-block">
                    {{ Auth::user()->isAdmin() ? 'ADMIN' : (Auth::user()->hasRole('manager') ? 'MANAGER' : (Auth::user()->hasRole('staff') ? 'STAFF' : 'PLAYER')) }}
                </span>
            </div>
            @if(Auth::user()->isAdmin() || Auth::user()->hasRole('manager'))
                <a href="{{ route('admin.dashboard') }}" class="gz-dropdown-item">Admin Overview</a>
                <a href="{{ route('admin.courts.index') }}" class="gz-dropdown-item">Courts</a>
                <a href="{{ route('admin.finance') }}" class="gz-dropdown-item">Financials</a>
            @elseif(Auth::user()->hasRole('staff'))
                <a href="{{ route('staff.today') }}" class="gz-dropdown-item">Today's Schedule</a>
            @else
                <a href="{{ url('/') }}" class="gz-dropdown-item">Home</a>
                <a href="{{ route('booking') }}" class="gz-dropdown-item">Book Courts</a>
                <a href="{{ route('bookings.index') }}" class="gz-dropdown-item">Courts Booked</a>
            @endif
            <div class="my-1 border-t border-[color:var(--gz-border)]"></div>
            <a href="{{ route('profile.edit') }}" class="gz-dropdown-item">Profile Settings</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left gz-dropdown-item font-semibold" style="color: var(--gz-danger);">Log Out</button>
            </form>
        @else
            <a href="{{ url('/') }}" class="gz-dropdown-item">Home</a>
            <a href="{{ route('booking') }}" class="gz-dropdown-item">Book Courts</a>
            <div class="my-1 border-t border-[color:var(--gz-border)]"></div>
            <a href="{{ route('login') }}" class="gz-dropdown-item">Login</a>
            <a href="{{ route('register') }}" class="gz-dropdown-item">Register</a>
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