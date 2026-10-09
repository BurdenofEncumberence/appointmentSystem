<x-app-layout title="My Hosted Open Play Sessions — KYMNET">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6">
        {{-- Header & Breadcrumb --}}
        <div class="mb-6">
            <div class="flex items-center gap-2 text-xs mb-2" style="color: var(--gz-muted);">
                <a href="{{ route('open-play.index') }}" class="hover:underline">Open Play</a>
                <span>/</span>
                <span class="font-semibold" style="color: var(--gz-ink);">My Hosted Sessions</span>
            </div>
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h1 class="gz-font-display font-extrabold text-2xl sm:text-3xl tracking-tight" style="color: var(--gz-ink);">
                        My Hosted Open Play Sessions
                    </h1>
                    <p class="text-sm mt-1" style="color: var(--gz-muted);">
                        Track the approval status and court payments of your hosted communal Open Play sessions.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('open-play.host.create') }}" class="gz-btn-primary gz-btn-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Host Another Session</span>
                    </a>
                    <a href="{{ route('open-play.index') }}" class="gz-btn-outline gz-btn-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Browse Open Play</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Flash Status Message --}}
        @if (session('status'))
            <div class="mb-6 p-4 rounded-xl text-sm flex items-start gap-3" style="background: rgba(16, 185, 129, 0.1); color: #047857; border: 1px solid rgba(16, 185, 129, 0.2);" role="alert">
                <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">{{ session('status') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 rounded-xl text-sm flex items-start gap-3" style="background: var(--gz-danger-bg); color: var(--gz-danger);" role="alert">
                <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">{{ session('error') }}</div>
            </div>
        @endif

        {{-- Sessions Listing --}}
        @if ($sessions->isEmpty())
            <div class="gz-panel p-12 text-center">
                <div class="w-12 h-12 mx-auto rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <h3 class="gz-font-display font-bold text-lg mb-1" style="color: var(--gz-ink);">No hosted sessions yet</h3>
                <p class="text-xs max-w-md mx-auto mb-6" style="color: var(--gz-muted);">
                    You haven't submitted any Open Play sessions. Pick a date and time to host a communal session for the community.
                </p>
                <a href="{{ route('open-play.host.create') }}" class="gz-btn-primary gz-btn-sm inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Host Your First Open Play</span>
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($sessions as $session)
                    <div class="gz-panel p-5 border transition-all" style="background: var(--gz-surface); border-color: var(--gz-border);">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            {{-- Info --}}
                            <div class="space-y-2 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    {{-- Status Badge --}}
                                    @if ($session->session_status === 'pending_approval')
                                        <span class="gz-badge text-xs font-bold uppercase tracking-wider" style="background: rgba(245, 158, 11, 0.15); color: #b45309; border: 1px solid rgba(245, 158, 11, 0.3);">
                                            Pending Manager Approval
                                        </span>
                                    @elseif ($session->session_status === 'approved_pending_payment')
                                        <span class="gz-badge gz-badge-success text-xs font-bold uppercase tracking-wider animate-pulse">
                                            Approved - Awaiting Court Payment
                                        </span>
                                    @elseif ($session->session_status === 'scheduled')
                                        <span class="gz-badge text-xs font-bold uppercase tracking-wider" style="background: rgba(16, 185, 129, 0.15); color: #047857; border: 1px solid rgba(16, 185, 129, 0.3);">
                                            Courts Secured & Scheduled
                                        </span>
                                    @elseif ($session->session_status === 'rejected')
                                        <span class="gz-badge text-xs font-bold uppercase tracking-wider" style="background: var(--gz-danger-bg); color: var(--gz-danger); border: 1px solid rgba(239, 68, 68, 0.3);">
                                            Request Rejected
                                        </span>
                                    @elseif ($session->session_status === 'ongoing')
                                        <span class="gz-badge gz-badge-primary text-xs font-bold uppercase tracking-wider">
                                            Live Ongoing
                                        </span>
                                    @else
                                        <span class="gz-badge gz-badge-outline text-xs font-semibold">
                                            {{ ucfirst($session->session_status) }}
                                        </span>
                                    @endif

                                    <span class="text-xs" style="color: var(--gz-muted);">•</span>
                                    <span class="text-xs font-semibold" style="color: var(--gz-muted);">{{ $session->skill_level }}</span>
                                </div>

                                <h2 class="gz-font-display font-bold text-lg" style="color: var(--gz-ink);">
                                    {{ $session->title }}
                                </h2>

                                <div class="flex flex-wrap items-center gap-y-1 gap-x-4 text-xs" style="color: var(--gz-muted);">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>{{ $session->date->format('M d, Y (D)') }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>{{ $session->time_window }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <span>{{ $session->courts->pluck('court_name')->implode(', ') ?: 'No courts assigned' }}</span>
                                    </div>
                                </div>

                                {{-- Manager Note if present --}}
                                @if ($session->manager_note)
                                    <div class="text-xs p-3 rounded-lg border" style="background: rgba(0, 0, 0, 0.02); border-color: var(--gz-border);">
                                        <span class="font-bold text-[11px] uppercase tracking-wider block mb-0.5" style="color: var(--gz-muted);">Manager Note:</span>
                                        <p style="color: var(--gz-ink);">{{ $session->manager_note }}</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Financial & Action Column --}}
                            <div class="flex flex-col sm:items-end justify-between gap-3 pt-3 sm:pt-0 border-t sm:border-t-0" style="border-color: var(--gz-border);">
                                <div class="sm:text-right">
                                    <div class="text-[11px] uppercase tracking-wider" style="color: var(--gz-muted);">Court Appointment Fee</div>
                                    <div class="gz-font-display text-xl font-bold" style="color: var(--gz-ink);">
                                        ₱{{ number_format($session->court_fee, 2) }}
                                    </div>
                                    <div class="text-[11px]" style="color: var(--gz-muted);">
                                        Status: <span class="font-semibold capitalize">{{ $session->host_payment_status }}</span>
                                        @if($session->host_payment_method)
                                            ({{ ucfirst($session->host_payment_method) }})
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 flex-wrap">
                                    {{-- If Approved Pending Payment -> Show Pay Button --}}
                                    @if ($session->session_status === 'approved_pending_payment')
                                        <a href="{{ route('open-play.host.pay.show', $session) }}" class="gz-btn-primary gz-btn-sm flex items-center gap-1.5 shadow-md">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            <span>Pay ₱{{ number_format($session->court_fee, 2) }} to Secure</span>
                                        </a>
                                    @endif

                                    {{-- If Scheduled -> View Public Page --}}
                                    @if (in_array($session->session_status, ['scheduled', 'ongoing']))
                                        <a href="{{ route('open-play.show', $session) }}" class="gz-btn-outline gz-btn-sm flex items-center gap-1.5">
                                            <span>View Session & Roster</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    @endif

                                    {{-- If Pending or Rejected -> Cancel Button --}}
                                    @if (in_array($session->session_status, ['pending_approval', 'rejected']))
                                        <form method="POST" action="{{ route('open-play.host.destroy', $session) }}" onsubmit="return confirm('Cancel this hosting request?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="gz-btn-outline gz-btn-sm text-xs hover:border-red-500 hover:text-red-600">
                                                Cancel Request
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="mt-4">
                    {{ $sessions->links() }}
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
