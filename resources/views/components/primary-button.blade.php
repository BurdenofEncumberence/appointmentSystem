<button {{ $attributes->merge(['type' => 'submit', 'class' => 'gz-btn-primary gz-btn-sm']) }}>
    {{ $slot }}
</button>