<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-xl border border-transparent bg-slate-900 px-5 py-3 text-xs font-bold uppercase tracking-[0.16em] text-white transition hover:bg-lime-400 hover:text-slate-950 focus:outline-none focus:ring-2 focus:ring-lime-400 focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
