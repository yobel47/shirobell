{{-- Section "closing" of the engine theme (ADR 012: order and visibility are configurable). --}}
@include('themes._engine.partials.ornament')

{{-- Closing --}}
<section class="relative overflow-hidden px-8 pt-16 pb-20 text-center">
    @include('themes._engine.ornaments.corners', ['size' => 'h-24 w-24', 'gap' => '2'])
    @include('themes._engine.ornaments.crown', ['class' => 'h-12 w-44'])
    @if ($invitation->closing_text)
        <p class="text-sm leading-relaxed">{{ $invitation->closing_text }}</p>
    @endif
    <p class="mt-8 text-sm">Kami yang berbahagia,</p>
    <p class="{{ $names }} mt-2 text-t-primary">{{ $groom }} &amp; {{ $bride }}</p>
</section>

@if ($engine['illustration'])
    <div class="relative h-48 overflow-hidden" aria-hidden="true">
        @include('themes._engine.illustrations.place', ['class' => 'w-full max-w-md max-h-48'])
    </div>
@endif
