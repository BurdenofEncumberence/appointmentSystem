<x-app-layout>
    <x-slot name="header">
        <div class="mx-auto" style="width: min(96vw, 1700px); position: relative; left: 50%; transform: translateX(-50%);">
            <h1 class="gz-font-display font-bold text-xl sm:text-2xl">Courts Booked</h1>
        </div>
    </x-slot>

    @php
        // Where the "Book a court" buttons go. Set this to your real booking route.
        $bookUrl = Route::has('bookings.create') ? route('bookings.create') : url('/');

        // Build real start/end datetimes once, so we can tell upcoming from past.
        $rows = $bookings->map(function ($b) {
            $date  = \Illuminate\Support\Carbon::parse($b->date);
            $start = \Illuminate\Support\Carbon::parse($b->start_time);
            $end   = \Illuminate\Support\Carbon::parse($b->end_time);

            return [
                'b'     => $b,
                'start' => $date->copy()->setTime($start->hour, $start->minute),
                'end'   => $date->copy()->setTime($end->hour, $end->minute),
            ];
        });

        $upcoming = $rows
            ->filter(fn ($r) => $r['b']->booking_status !== 'cancelled' && $r['end']->isFuture())
            ->sortBy('start')
            ->values();

        $past = $rows
            ->reject(fn ($r) => $upcoming->contains(fn ($u) => $u['b']->is($r['b'])))
            ->sortByDesc('start')
            ->values();

        $groups = ['upcoming' => $upcoming, 'past' => $past];
        $defaultTab = $upcoming->isNotEmpty() || $past->isEmpty() ? 'upcoming' : 'past';
    @endphp

    <div class="px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <div class="mx-auto" style="width: min(96vw, 1700px); position: relative; left: 50%; transform: translateX(-50%);" x-data="{ tab: '{{ $defaultTab }}' }">

            @if (session('status'))
                <div class="gz-status mb-4" role="status" aria-live="polite">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Tabs + primary action --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div class="flex items-center gap-2" role="tablist" aria-label="Booking filter">
                    @foreach ($groups as $key => $items)
                        <button type="button"
                                role="tab"
                                id="tab-{{ $key }}"
                                aria-controls="panel-{{ $key }}"
                                :aria-selected="tab === '{{ $key }}' ? 'true' : 'false'"
                                @click="tab = '{{ $key }}'"
                                class="text-sm font-semibold rounded-xl"
                                :style="tab === '{{ $key }}'
                                    ? 'padding: 7px 14px; background: var(--gz-pop); color: var(--gz-ink); border: 1px solid var(--gz-pop);'
                                    : 'padding: 7px 14px; background: var(--gz-surface); color: var(--gz-muted); border: 1px solid var(--gz-border);'">
                            {{ ucfirst($key) }} ({{ $items->count() }})
                        </button>
                    @endforeach
                </div>

                <a href="{{ $bookUrl }}" class="gz-btn-primary gz-btn-sm">Book a court</a>
            </div>

            @foreach ($groups as $key => $items)
                <section id="panel-{{ $key }}" role="tabpanel" aria-labelledby="tab-{{ $key }}"
                         x-show="tab === '{{ $key }}'" @if ($key !== $defaultTab) x-cloak @endif>

                    @if ($items->isEmpty())
                        <div class="gz-panel text-center" style="padding: 40px 24px;">
                            <p class="gz-font-display font-bold text-base mb-1">
                                {{ $key === 'upcoming' ? 'No upcoming bookings' : 'No past bookings' }}
                            </p>
                            <p class="text-sm mb-4" style="color: var(--gz-muted);">
                                {{ $key === 'upcoming'
                                    ? 'Book a court and it will show up here.'
                                    : 'Finished and cancelled bookings will show up here.' }}
                            </p>
                            @if ($key === 'upcoming')
                                <a href="{{ $bookUrl }}" class="gz-btn-primary gz-btn-sm">Book a court</a>
                            @endif
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($items as $row)
                                @php
                                    $b      = $row['b'];
                                    $start  = $row['start'];
                                    $end    = $row['end'];

                                    $status = $b->booking_status === 'confirmed' && $end->isPast()
                                        ? 'completed'
                                        : $b->booking_status;

                                    $badgeClass = match ($status) {
                                        'confirmed' => 'gz-badge-success',
                                        'pending'   => 'gz-badge-warning',
                                        'cancelled' => 'gz-badge-danger',
                                        default     => 'gz-badge-neutral',
                                    };

                                    $mins     = abs($start->diffInMinutes($end));
                                    $duration = $mins % 60 === 0
                                        ? ($mins / 60) . ' hour' . ($mins === 60 ? '' : 's')
                                        : $mins . ' min';

                                    $when = $start->isToday() ? 'Today' : ($start->isTomorrow() ? 'Tomorrow' : null);

                                    $canCancel = $key === 'upcoming'
                                        && in_array($b->booking_status, ['confirmed', 'pending'], true)
                                        && Route::has('bookings.cancel');
                                @endphp

                                <article class="gz-panel"
                                         style="padding: 14px 18px; {{ $key === 'upcoming' ? 'border-left: 4px solid var(--gz-pop);' : 'opacity: .85;' }}">
                                    <div class="grid gap-3 sm:grid-cols-[1fr_1.2fr_1.2fr_auto] sm:items-center">

                                        <div>
                                            <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--gz-muted);">Court</p>
                                            <h3 class="gz-font-display font-bold text-base">
                                                {{ $b->court->court_name ?? 'Deleted court' }}
                                            </h3>
                                        </div>

                                        <div>
                                            <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--gz-muted);">Date</p>
                                            <p class="text-sm font-semibold">
                                                {{ $start->format('D, M j, Y') }}
                                                @if ($when && $key === 'upcoming')
                                                    <span class="ml-1 text-[11px] font-bold" style="color: var(--gz-pop-dark);">{{ $when }}</span>
                                                @endif
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--gz-muted);">Time</p>
                                            <p class="text-sm font-semibold whitespace-nowrap">
                                                {{ $start->format('g:i A') }} – {{ $end->format('g:i A') }}
                                                <span class="text-xs font-normal" style="color: var(--gz-muted);">· {{ $duration }}</span>
                                            </p>
                                        </div>

                                        <div class="flex items-center gap-2 sm:justify-end">
                                            <span class="gz-badge {{ $badgeClass }}">{{ ucfirst($status) }}</span>

                                            @if ($canCancel)
                                                {{-- Needs a route named bookings.cancel (PATCH). Rename to match yours. --}}
                                                <form method="POST" action="{{ route('bookings.cancel', $b) }}"
                                                      @submit="if (!confirm('Cancel this booking? This cannot be undone.')) $event.preventDefault()">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="gz-btn-outline gz-btn-sm" style="color: var(--gz-danger);">
                                                        Cancel
                                                    </button>
                                                </form>
                                            @endif
                                        </div>

                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endforeach

        </div>
    </div>
</x-app-layout>