{{-- Section "hero" of the engine theme (ADR 012: order and visibility are configurable). --}}
{{-- Hero --}}
<div class="relative">
@if (in_array($hero, ['arch', 'frame'], true))
    @include('themes._engine.ornaments.corners', ['size' => 'h-28 w-28', 'gap' => '1'])
@endif
@switch($hero)
    @case('photo')
        <section class="relative flex min-h-[85vh] items-end">
            @if ($coverUrl)
                <img src="{{ $coverUrl }}" alt="Foto {{ $invitation->coupleName() }}" class="absolute inset-0 h-full w-full object-cover">
            @else
                <div class="absolute inset-0 bg-t-soft"></div>
                @if ($pattern)<div class="{{ $pattern }} absolute inset-0"></div>@endif
            @endif
            <div class="absolute inset-0 bg-gradient-to-b from-transparent via-t-bg/30 to-t-bg"></div>
            <div class="relative w-full px-6 pb-12 text-center">
                @include('themes._engine.ornaments.crown', ['class' => 'h-12 w-44'])
                <p class="text-xs tracking-[0.35em] text-t-accent uppercase">The Wedding of</p>
                <h2 class="{{ $names }} mt-3 leading-tight text-t-primary">{{ $groom }} <span class="mx-1 text-t-accent">&amp;</span> {{ $bride }}</h2>
                @if ($dateLabel)
                    <p class="mt-3 text-sm tracking-widest">{{ $dateLabel }}</p>
                @endif
            </div>
        </section>
        @break
    @case('editorial')
        <section class="relative px-6 pt-16 pb-12">
            <p class="text-xs font-semibold tracking-[0.35em] text-t-accent uppercase">Undangan Pernikahan</p>
            <h2 class="mt-6 font-heading text-6xl leading-[0.95] font-semibold text-t-primary">{{ $groom }}</h2>
            <p class="font-script text-5xl text-t-accent">&amp;</p>
            <h2 class="font-heading text-6xl leading-[0.95] font-semibold text-t-primary">{{ $bride }}</h2>
            @if ($dateLabel)
                <p class="mt-6 border-l-2 border-t-accent pl-3 text-sm tracking-widest uppercase">{{ $dateLabel }}</p>
            @endif
            <div class="relative mt-10">
                <div class="absolute -right-6 -bottom-6 h-3/4 w-3/4 bg-t-soft"></div>
                @if ($pattern)<div class="{{ $pattern }} absolute -right-6 -bottom-6 h-3/4 w-3/4"></div>@endif
                <div class="{{ $shape }} relative aspect-[4/5] w-full overflow-hidden bg-t-soft">
                    @if ($coverUrl)
                        <img src="{{ $coverUrl }}" alt="Foto {{ $invitation->coupleName() }}" class="h-full w-full object-cover">
                    @else
                        @include('themes._engine.ornaments.monogram')
                    @endif
                </div>
            </div>
        </section>
        @break
    @case('frame')
        <section class="relative px-5 pt-12 pb-10">
            @if ($pattern)<div class="{{ $pattern }} absolute inset-0"></div>@endif
            <div class="t-frame relative bg-t-surface px-6 py-10 text-center">
                <p class="text-xs tracking-[0.35em] text-t-accent uppercase">The Wedding of</p>
                @include('themes._engine.partials.ornament')
                <div class="{{ $shape }} mx-auto h-72 w-56 overflow-hidden border-4 border-t-soft bg-t-soft">
                    @if ($coverUrl)
                        <img src="{{ $coverUrl }}" alt="Foto {{ $invitation->coupleName() }}" class="h-full w-full object-cover">
                    @else
                        @include('themes._engine.ornaments.monogram')
                    @endif
                </div>
                <div class="mt-6">@include('themes._engine.ornaments.crown', ['class' => 'h-12 w-44'])</div>
                <h2 class="{{ $names }} mt-2 leading-tight text-t-primary">{{ $groom }} <span class="mx-1 text-t-accent">&amp;</span> {{ $bride }}</h2>
                @if ($dateLabel)
                    <p class="mt-3 text-sm tracking-widest">{{ $dateLabel }}</p>
                @endif
                @include('themes._engine.partials.ornament')
            </div>
        </section>
        @break
    @default
        <section class="relative px-6 pt-16 pb-14 text-center">
            @if ($pattern)<div class="{{ $pattern }} absolute inset-x-0 top-0 h-2/3"></div>@endif
            <p class="relative text-xs tracking-[0.35em] text-t-accent uppercase">The Wedding of</p>
            <div class="{{ $shape }} relative mx-auto mt-8 h-80 w-60 overflow-hidden border-4 border-t-surface bg-t-soft shadow-xl">
                @if ($coverUrl)
                    <img src="{{ $coverUrl }}" alt="Foto {{ $invitation->coupleName() }}" class="h-full w-full object-cover">
                @else
                        @include('themes._engine.ornaments.monogram')
                    @endif
            </div>
            <div class="relative mt-6">@include('themes._engine.ornaments.crown', ['class' => 'h-12 w-44'])</div>
            <h2 class="{{ $names }} relative mt-2 leading-tight text-t-primary">{{ $groom }} <span class="mx-1 text-t-accent">&amp;</span> {{ $bride }}</h2>
            @if ($dateLabel)
                <p class="mt-3 text-sm tracking-widest">{{ $dateLabel }}</p>
            @endif
        </section>
@endswitch
</div>

@include('themes._engine.partials.ornament')
