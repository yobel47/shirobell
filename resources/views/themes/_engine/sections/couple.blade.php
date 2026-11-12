{{-- Section "couple" of the engine theme (ADR 012: order and visibility are configurable). --}}
{{-- Couple --}}
<section class="space-y-12 px-8 pb-16 text-center">
    @include('themes._engine.partials.person', ['side' => 'groom', 'photo' => $groomPhotoUrl])
    <p class="font-script text-5xl text-t-accent">&amp;</p>
    @include('themes._engine.partials.person', ['side' => 'bride', 'photo' => $bridePhotoUrl])
</section>

@if ($engine['illustration'])
    <div class="relative h-44 overflow-hidden" aria-hidden="true">
        @include('themes._engine.illustrations.place', ['class' => 'w-full max-w-md max-h-44'])
    </div>
@endif
