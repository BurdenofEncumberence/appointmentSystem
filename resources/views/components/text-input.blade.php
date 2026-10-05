@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'gz-input']) }}>