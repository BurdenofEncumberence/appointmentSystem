@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'mt-2 text-base']) }} style="color: var(--red);">
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif