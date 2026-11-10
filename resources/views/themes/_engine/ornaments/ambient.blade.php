{{-- Ambient motion on the cover: falling petals (floral families) or twinkling sparkles (luxury families). --}}
@php
    $petals = in_array($engine['ornament'], ['floral', 'leaf', 'lotus', 'wave'], true);
    $sparkles = in_array($engine['ornament'], ['deco', 'star', 'songket', 'kawung', 'parang', 'tenun', 'cloud', 'line'], true);
    // Fixed positions so the markup is deterministic (no random per request).
    $spots = [[8, 0, 13], [22, 4, 17], [37, 9, 15], [51, 2, 19], [66, 7, 14], [79, 11, 18], [91, 5, 16], [14, 12, 20], [58, 14, 21]];
@endphp
<div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
    @if ($petals)
        @foreach ($spots as [$left, $delay, $duration])
            <svg viewBox="0 0 20 20" class="t-petal h-4 w-4 fill-t-accent/50" style="left: {{ $left }}%; animation-duration: {{ $duration }}s; animation-delay: -{{ $delay }}s">
                @if ($engine['ornament'] === 'leaf')
                    <ellipse cx="10" cy="10" rx="9" ry="5" transform="rotate(30 10 10)"/>
                @else
                    <path d="M10 1 C16 5 17 12 10 19 C3 12 4 5 10 1Z"/>
                @endif
            </svg>
        @endforeach
    @elseif ($sparkles)
        @foreach ($spots as $i => [$left, $delay, $duration])
            <svg viewBox="0 0 20 20" class="t-sparkle h-3 w-3 fill-t-accent" style="left: {{ $left }}%; top: {{ 8 + ($i * 37) % 80 }}%; animation-duration: {{ 2 + $duration % 4 }}s; animation-delay: -{{ $delay % 4 }}s">
                <path d="M10 0 L12 8 L20 10 L12 12 L10 20 L8 12 L0 10 L8 8Z"/>
            </svg>
        @endforeach
    @endif
</div>
