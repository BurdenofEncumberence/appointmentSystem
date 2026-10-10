{{--
    resources/views/components/site-footer.blade.php

    Shared footer used by every page. Purely static — no per-page
    variation, so no props needed.
--}}
@php
    $siteSettings = \App\Models\SiteSettings::first();
@endphp
<footer class="relative z-10 border-t" style="background: #12150F; border-color: rgba(255,255,255,0.08); color: rgba(255,255,255,0.7);">
    <div class="max-w-6xl mx-auto px-6 py-14 grid md:grid-cols-4 gap-10">
        <div>
            <span class="gz-font-display font-bold text-lg" style="color: #FCFBF7;">{{ $siteSettings->business_name ?? 'Gaoshou Pickleball' }}</span>
            <p class="mt-4 text-sm max-w-xs leading-relaxed">
                {{ $siteSettings->tagline ?? 'Court booking for the Davao pickleball community. Built by players, for players.' }}
            </p>
            @if($siteSettings && $siteSettings->email_address)
                <p class="mt-2 text-sm">
                    <a href="mailto:{{ $siteSettings->email_address }}" class="footer-link">{{ $siteSettings->email_address }}</a>
                </p>
            @endif
            @if($siteSettings && $siteSettings->contact_number)
                <p class="mt-1 text-sm">{{ $siteSettings->contact_number }}</p>
            @endif
        </div>
        <div>
            <h4 class="text-xs font-semibold uppercase tracking-wide mb-4" style="color: #FCFBF7;">Explore</h4>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('booking') }}" class="footer-link">Find courts</a></li>
                <li><a href="#" class="footer-link">Host a tournament</a></li>
                <li><a href="#" class="footer-link">Find partners</a></li>
            </ul>
        </div>
        <div>
            <h4 class="text-xs font-semibold uppercase tracking-wide mb-4" style="color: #FCFBF7;">Legal & Support</h4>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('terms') }}" class="footer-link">Terms & Conditions</a></li>
                <li><a href="{{ route('privacy') }}" class="footer-link">Privacy Policy</a></li>
            </ul>
        </div>
        <div>
            <h4 class="text-xs font-semibold uppercase tracking-wide mb-4" style="color: #FCFBF7;">Connect</h4>
            <ul class="space-y-2 text-sm">
                @if($siteSettings && $siteSettings->facebook_link)
                    <li><a href="{{ $siteSettings->facebook_link }}" target="_blank" class="footer-link">Facebook</a></li>
                @endif
                @if($siteSettings && $siteSettings->twitter_link)
                    <li><a href="{{ $siteSettings->twitter_link }}" target="_blank" class="footer-link">Twitter</a></li>
                @endif
                @if($siteSettings && $siteSettings->instagram_link)
                    <li><a href="{{ $siteSettings->instagram_link }}" target="_blank" class="footer-link">Instagram</a></li>
                @endif
                @if($siteSettings && $siteSettings->linkedin_link)
                    <li><a href="{{ $siteSettings->linkedin_link }}" target="_blank" class="footer-link">LinkedIn</a></li>
                @endif
            </ul>
        </div>
    </div>
    <div class="max-w-6xl mx-auto px-6 pb-8 text-sm" style="color: rgba(255,255,255,0.4);">
        © {{ date('Y') }} {{ $siteSettings->business_name ?? 'Gaoshou Pickleball' }}. All rights reserved.
    </div>
</footer>