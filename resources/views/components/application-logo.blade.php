@php
    $siteSettings = \App\Models\SiteSettings::first();
    $logoUrl = null;
    if ($siteSettings && $siteSettings->logo && file_exists(public_path('storage/' . $siteSettings->logo))) {
        $logoUrl = asset('storage/' . $siteSettings->logo);
    } elseif (file_exists(public_path('images/kymnet-logo.png'))) {
        $logoUrl = asset('images/kymnet-logo.png');
    }
@endphp

@if($logoUrl)
    <img src="{{ $logoUrl }}" alt="{{ $siteSettings->system_name ?? 'KYMNET' }}" {{ $attributes->merge(['class' => 'h-14 w-auto max-h-14 max-w-[180px] object-contain rounded-xl shadow-sm']) }}>
@else
    <div {{ $attributes->merge(['class' => 'h-12 w-12 rounded-2xl bg-[#12150F] flex items-center justify-center font-display font-extrabold text-emerald-400 text-xl border border-emerald-500/30 shadow-md']) }}>
        K
    </div>
@endif
