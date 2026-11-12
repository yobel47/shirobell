{{--
    Desktop backdrop (lg and up): a fixed panel beside the phone-width
    invitation column. Mobile is unchanged. Uses the cover photo when there
    is one, otherwise the theme pattern, ornaments and illustration.
--}}
<aside class="fixed inset-y-0 left-0 right-[28rem] z-0 hidden overflow-hidden lg:flex" aria-hidden="true">
    @if ($coverUrl)
        <img src="{{ $coverUrl }}" alt="" class="absolute inset-0 h-full w-full object-cover">
        <div class="absolute inset-0 bg-linear-to-t from-black/70 via-black/30 to-black/10"></div>
    @else
        <div class="absolute inset-0 bg-t-soft"></div>
        @if ($pattern)
            <div class="{{ $pattern }} absolute inset-0"></div>
        @endif
        <div class="t-grain absolute inset-0"></div>
        @include('themes._engine.ornaments.corners', ['size' => 'h-48 w-48 xl:h-60 xl:w-60', 'gap' => '3'])
        @include('themes._engine.illustrations.place', ['class' => 'w-full max-w-3xl max-h-[34vh]'])
    @endif

    <div @class([
        'relative m-auto flex max-w-xl flex-col items-center px-10 text-center',
        'text-white' => $coverUrl,
        'text-t-ink' => ! $coverUrl,
        'pb-[20vh]' => ! $coverUrl && $engine['illustration'] && ! $illustrationTop,
        'pt-[18vh]' => ! $coverUrl && $illustrationTop,
    ])>
        @unless ($coverUrl)
            @include('themes._engine.ornaments.crown', ['class' => 'h-16 w-60'])
        @endunless
        <p @class(['mt-2 text-sm tracking-[0.4em] uppercase', 'text-white/80' => $coverUrl, 'text-t-accent' => ! $coverUrl])>The Wedding of</p>
        <p @class([$names, 'mt-4 text-6xl leading-tight xl:text-7xl', 'text-t-primary' => ! $coverUrl])>{{ $groom }} <span @class(['mx-2', 'text-t-accent' => ! $coverUrl, 'text-white/80' => $coverUrl])>&amp;</span> {{ $bride }}</p>
        @if ($dateLabel)
            <p class="mt-5 text-base tracking-[0.25em]">{{ $dateLabel }}</p>
        @endif

        @if ($countdownTarget)
            <div class="mt-10 grid grid-cols-4 gap-3"
                 x-data="{
                    target: new Date('{{ $countdownTarget }}').getTime(), d: 0, h: 0, m: 0, s: 0,
                    tick() {
                        const diff = Math.max(0, this.target - Date.now());
                        this.d = Math.floor(diff / 864e5); this.h = Math.floor(diff / 36e5) % 24;
                        this.m = Math.floor(diff / 6e4) % 60; this.s = Math.floor(diff / 1e3) % 60;
                    },
                    init() { this.tick(); setInterval(() => this.tick(), 1000); },
                 }">
                @foreach (['d' => 'Hari', 'h' => 'Jam', 'm' => 'Menit', 's' => 'Detik'] as $unit => $label)
                    <div @class(['w-20 rounded-2xl py-3 backdrop-blur-sm', 'bg-white/15 ring-1 ring-white/25' => $coverUrl, 'bg-t-surface/70 ring-1 ring-t-accent/40' => ! $coverUrl])>
                        <p class="font-heading text-3xl font-semibold" x-text="{{ $unit }}">0</p>
                        <p class="mt-1 text-[11px] tracking-widest uppercase opacity-80">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        <p @class(['mt-12 text-xs tracking-[0.3em] uppercase', 'text-white/70' => $coverUrl, 'opacity-60' => ! $coverUrl])>Gulir undangan di sebelah kanan</p>
    </div>
</aside>
