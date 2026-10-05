<button {{ $attributes->merge(['type' => 'submit', 'class' => 'gz-btn-danger gz-btn-sm']) }}>
    {{ $slot }}
</button>
