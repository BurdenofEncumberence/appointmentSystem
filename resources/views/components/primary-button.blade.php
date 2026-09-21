<button {{ $attributes->merge(['type' => 'submit', 'class' => 'pixel-btn text-[color:var(--cream)] bg-[color:var(--red)]']) }}>
    {{ $slot }}
</button>