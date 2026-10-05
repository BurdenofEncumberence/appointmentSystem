@props(['value'])

<label {{ $attributes->merge(['class' => 'gz-label block mb-1.5']) }}>
    {{ $value ?? $slot }}
</label>