{{-- Four mirrored corner ornaments. Params: $size (Tailwind w/h classes), $inset (position offset classes per side). --}}
@php
    $size ??= 'h-28 w-28';
    $gap ??= '2';
    $family = $engine['ornament'];
@endphp
<div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
    @foreach ([
        "top-{$gap} left-{$gap}" => '',
        "top-{$gap} right-{$gap}" => '-scale-x-100',
        "bottom-{$gap} left-{$gap}" => '-scale-y-100',
        "bottom-{$gap} right-{$gap}" => '-scale-100',
    ] as $pos => $flip)
        <svg viewBox="0 0 120 120" class="absolute {{ $pos }} {{ $size }} {{ $flip }}">
            @include('themes._engine.ornaments.corner', ['family' => $family])
        </svg>
    @endforeach
</div>
{{-- Classes generated above, listed for Tailwind: top-1 left-1 right-1 bottom-1 top-2 left-2 right-2 bottom-2 top-3 left-3 right-3 bottom-3 top-0 left-0 right-0 bottom-0 -scale-x-100 -scale-y-100 -scale-100 --}}
