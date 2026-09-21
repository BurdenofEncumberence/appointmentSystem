@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-pixel text-[10px] mb-2']) }}>
    {{ $value ?? $slot }}
</label>