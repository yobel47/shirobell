{{-- Section "closing" of the engine theme (ADR 012: order and visibility are configurable). --}}
@include('themes._engine.partials.ornament')

{{-- Closing --}}
<section class="relative overflow-hidden px-8 pt-20 pb-24 text-center">
    @include('themes._engine.ornaments.corners', ['size' => 'h-24 w-24', 'gap' => '2'])
    <div class="tc-stagger relative" {!! $reveal !!}>
        @include('themes._engine.ornaments.crown', ['class' => 'h-12 w-44'])
        <p class="tc-eyebrow mt-2">Terima Kasih</p>
        @if ($invitation->closing_text)
            <p class="tc-pretty mx-auto mt-6 max-w-sm text-[0.95rem] leading-[1.8]">{{ $invitation->closing_text }}</p>
        @endif
        <p class="mt-10 text-xs tracking-[0.2em] uppercase opacity-70">Kami yang berbahagia,</p>
        <p class="{{ $names }} tc-title mt-3 text-t-primary">{{ $groom }} &amp; {{ $bride }}</p>
    </div>
</section>

@if ($engine['illustration'])
    <div class="relative h-48 overflow-hidden" aria-hidden="true">
        @include('themes._engine.illustrations.place', ['class' => 'w-full max-w-md max-h-48'])
    </div>
@endif
