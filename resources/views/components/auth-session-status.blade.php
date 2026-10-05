@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'gz-status mb-4']) }}>
        {{ $status }}
    </div>
@endif