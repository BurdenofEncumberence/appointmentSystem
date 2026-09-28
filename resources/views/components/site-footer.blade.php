{{--
    resources/views/components/site-footer.blade.php

    Shared footer used by every page. Purely static — no per-page
    variation, so no props needed.
--}}
<footer class="relative z-10" style="background: var(--gz-ink); color: rgba(255,255,255,0.7);">
    <div class="max-w-6xl mx-auto px-6 py-14 grid md:grid-cols-4 gap-10">
        <div>
            <span class="gz-font-display font-bold text-lg" style="color: var(--gz-surface);">KYMNET</span>
            <p class="mt-4 text-sm max-w-xs leading-relaxed">
                Court booking for the Davao pickleball community. Built by players, for players.
            </p>
        </div>
        <div>
            <h4 class="text-xs font-semibold uppercase tracking-wide mb-4" style="color: var(--gz-surface);">Explore</h4>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('booking') }}" class="footer-link">Find courts</a></li>
                <li><a href="#" class="footer-link">Host a tournament</a></li>
                <li><a href="#" class="footer-link">Find partners</a></li>
            </ul>
        </div>
        <div>
            <h4 class="text-xs font-semibold uppercase tracking-wide mb-4" style="color: var(--gz-surface);">For users</h4>
            <ul class="space-y-2 text-sm">
                <li><a href="#" class="footer-link">Support</a></li>
                <li><a href="#" class="footer-link">Pricing</a></li>
            </ul>
        </div>
        <div>
            <h4 class="text-xs font-semibold uppercase tracking-wide mb-4" style="color: var(--gz-surface);">Company</h4>
            <ul class="space-y-2 text-sm">
                <li><a href="#" class="footer-link">About us</a></li>
                <li><a href="#" class="footer-link">Contact</a></li>
            </ul>
        </div>
    </div>
    <div class="max-w-6xl mx-auto px-6 pb-8 text-sm" style="color: rgba(255,255,255,0.4);">
        © {{ date('Y') }} KYMNET. All rights reserved.
    </div>
</footer>