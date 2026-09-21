<button {{ $attributes->merge(['type' => 'button', 'class' => 'pixel-btn bg-[color:var(--parchment)]']) }}>
    {{ $slot }}
</button>