@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col sm:flex-row items-center justify-between gap-4 w-full">
        <div class="flex items-center justify-between w-full sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg border border-[color:var(--gz-border)] text-[color:var(--gz-muted)] opacity-40 cursor-not-allowed">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg border border-[color:var(--gz-border)] bg-[color:var(--gz-surface)] text-[color:var(--gz-ink)] hover:bg-[color:var(--gz-bg)]">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            <span class="text-xs font-mono text-[color:var(--gz-muted)]">
                Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg border border-[color:var(--gz-border)] bg-[color:var(--gz-surface)] text-[color:var(--gz-ink)] hover:bg-[color:var(--gz-bg)]">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg border border-[color:var(--gz-border)] text-[color:var(--gz-muted)] opacity-40 cursor-not-allowed">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <div class="hidden sm:flex items-center justify-between w-full">
            <div>
                <p class="text-xs font-mono text-[color:var(--gz-muted)]">
                    {!! __('Showing') !!}
                    <span class="font-bold text-[color:var(--gz-ink)]">{{ $paginator->firstItem() ?? 0 }}</span>
                    {!! __('to') !!}
                    <span class="font-bold text-[color:var(--gz-ink)]">{{ $paginator->lastItem() ?? 0 }}</span>
                    {!! __('of') !!}
                    <span class="font-bold text-[color:var(--gz-ink)]">{{ $paginator->total() }}</span>
                    {!! __('results') !!}
                </p>
            </div>

            <div class="inline-flex items-center gap-1.5">
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-[color:var(--gz-border)] bg-[color:var(--gz-bg)] text-[color:var(--gz-muted)] opacity-40 cursor-not-allowed" aria-hidden="true">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                        </span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-[color:var(--gz-border)] bg-[color:var(--gz-surface)] text-[color:var(--gz-ink)] hover:bg-[color:var(--gz-bg)] transition-colors" aria-label="{{ __('pagination.previous') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-mono text-[color:var(--gz-muted)] cursor-default">
                            {{ $element }}
                        </span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page">
                                    <span class="inline-flex items-center justify-center min-w-[32px] h-8 px-2 text-xs font-bold rounded-lg border border-[color:var(--gz-ink)] bg-[color:var(--gz-ink)] text-[color:var(--gz-surface)] cursor-default">
                                        {{ $page }}
                                    </span>
                                </span>
                            @else
                                <a href="{{ $url }}" class="inline-flex items-center justify-center min-w-[32px] h-8 px-2 text-xs font-semibold rounded-lg border border-[color:var(--gz-border)] bg-[color:var(--gz-surface)] text-[color:var(--gz-ink)] hover:bg-[color:var(--gz-bg)] hover:border-[color:var(--gz-ink)] transition-colors" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-[color:var(--gz-border)] bg-[color:var(--gz-surface)] text-[color:var(--gz-ink)] hover:bg-[color:var(--gz-bg)] transition-colors" aria-label="{{ __('pagination.next') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-[color:var(--gz-border)] bg-[color:var(--gz-bg)] text-[color:var(--gz-muted)] opacity-40 cursor-not-allowed" aria-hidden="true">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
