{{-- Section "couple" of the engine theme (ADR 012: order and visibility are configurable). --}}
{{-- Couple --}}
<section class="space-y-14 px-8 pb-20 text-center">
    <p class="tc-eyebrow" {!! $reveal !!}>Mempelai</p>
    @include('themes._engine.partials.person', ['side' => 'groom', 'photo' => $groomPhotoUrl])
    {{-- The ampersand sits between two fine rules. --}}
    <div class="flex items-center justify-center gap-5 text-t-accent" aria-hidden="true">
        <span class="h-px w-16 bg-current opacity-50"></span>
        <span class="font-script text-5xl">&amp;</span>
        <span class="h-px w-16 bg-current opacity-50"></span>
    </div>
    @include('themes._engine.partials.person', ['side' => 'bride', 'photo' => $bridePhotoUrl])
</section>

@if ($engine['illustration'])
    <div class="relative h-44 overflow-hidden" aria-hidden="true">
        @include('themes._engine.illustrations.place', ['class' => 'w-full max-w-md max-h-44'])
    </div>
@endif
