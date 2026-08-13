<span {{ $attributes->merge(['class' => 'app-datetime inline-block leading-snug']) }}>
    <span class="app-datetime__primary">{{ $primary }}</span>
    @if ($secondary)
        <span class="app-datetime__secondary block text-xs opacity-70 mt-0.5">{{ $secondary }}</span>
    @endif
</span>
