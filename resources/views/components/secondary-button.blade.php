<button {{ $attributes->merge(['type' => 'button', 'class' => 'gz-btn-outline gz-btn-sm']) }}>
    {{ $slot }}
</button>