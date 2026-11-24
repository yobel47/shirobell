{{--
    Theme engine (ADR 009). One view for every engine theme; the recipe in
    config/themes.php ($engine) picks the hero, photo shape, heading style,
    section style, ornament and pattern. Colors and fonts arrive as CSS
    custom properties ($themeStyle). Receives the InvitationRenderer contract.
--}}
@php
    $dateLabel = $mainDate?->translatedFormat('l, d F Y');
    $groom = $invitation->groom_nickname ?: 'Mempelai Pria';
    $bride = $invitation->bride_nickname ?: 'Mempelai Wanita';

    $shape = 't-shape-'.$engine['shape'];
    $pattern = $engine['pattern'] !== 'none' ? 't-pattern t-pattern-'.$engine['pattern'] : null;
    $h2 = match ($engine['heading']) {
        'serif' => 'font-heading text-3xl font-semibold text-t-primary',
        'caps' => 'font-heading text-2xl font-semibold tracking-[0.2em] uppercase text-t-primary',
        default => 'font-script text-5xl text-t-primary',
    };
    $names = match ($engine['heading']) {
        'caps' => 'font-heading text-4xl font-semibold tracking-[0.15em] uppercase',
        'serif' => 'font-heading text-5xl font-semibold',
        default => 'font-script text-6xl',
    };
    $card = match ($engine['sections']) {
        'plain' => 'px-2 py-6',
        'bordered' => 't-frame bg-t-surface px-6 py-8',
        default => 'rounded-3xl bg-t-surface px-6 py-8 shadow-sm',
    };
    $initials = mb_strtoupper(mb_substr($groom, 0, 1)).' & '.mb_strtoupper(mb_substr($bride, 0, 1));
    // Scroll reveal (Alpine intersect, bundled with Livewire). Visible without JS,
    // and off in previews (?buka=1) so the editor frame shows everything at once.
    $reveal = request()->boolean('buka') ? '' : 'x-data="{ v: false }" x-intersect.once="v = true" x-bind:class="v ? \'t-in\' : \'t-reveal\'"';
    $cardOrnaments = $engine['sections'] !== 'plain';
    $hero = $engine['hero'] === 'photo' && ! $coverUrl ? 'frame' : $engine['hero'];
    $illustrationTop = in_array($engine['illustration'], ['lampion', 'melati', 'taman'], true);
    // Room on the cover so the illustration never sits under the text or button.
    $coverRoom = match (true) {
        ! $engine['illustration'] => '',
        $illustrationTop => 'pt-[20vh]',
        default => 'pb-[24vh]',
    };
    // ?buka=1 skips the cover (editor preview, catalog thumbnails).
    $skipCover = request()->boolean('buka');
    // Shared controls from craft.css (colors via --tc-* tokens).
    $btn = 'tc-btn';
    $btnOutline = 'tc-btn-outline';
@endphp
<x-invitation.layout
    :invitation="$invitation" :preview="$preview" :demo="$demo ?? false" :music-url="$musicUrl" :cover-url="$coverUrl" :theme-style="$themeStyle"
    :css="$themeCss"
    class="bg-t-bg font-sans text-t-ink"
    x-data="{
        opened: {{ $skipCover ? 'true' : 'false' }},
        playing: false,
        open() {
            this.opened = true;
            document.documentElement.style.overflow = '';
            if (this.$refs.music) { this.$refs.music.play().then(() => this.playing = true).catch(() => {}); }
            this.$nextTick(() => document.getElementById('mulai')?.scrollIntoView());
        },
        toggleMusic() {
            const audio = this.$refs.music;
            if (!audio) return;
            if (audio.paused) { audio.play(); this.playing = true; } else { audio.pause(); this.playing = false; }
        },
    }"
    x-init="if (!opened) document.documentElement.style.overflow = 'hidden'"
>
    {{-- Cover: the customer's canvas design when enabled (ADR 012), otherwise the recipe cover. --}}
    @if ($canvasCover ?? null)
        @include('themes._engine.canvas.cover', ['cover' => $canvasCover])
    @else
    <section x-show="!opened" @if ($skipCover) x-cloak @endif x-transition:leave="transition duration-[1100ms] ease-[cubic-bezier(0.76,0,0.24,1)]" x-transition:leave-end="-translate-y-full"
             class="fixed inset-0 z-40 flex items-center justify-center overflow-hidden bg-t-bg text-t-ink">
        @if ($coverUrl)
            <img src="{{ $coverUrl }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-30">
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-t-bg/70 via-t-bg/40 to-t-bg/95"></div>
        @if ($pattern)
            <div class="{{ $pattern }} absolute inset-0"></div>
        @endif
        <div class="t-grain absolute inset-0"></div>
        @include('themes._engine.ornaments.ambient')
        @include('themes._engine.illustrations.place', ['class' => 'w-full max-w-lg max-h-[24vh]'])
        @include('themes._engine.ornaments.corners', ['size' => 'h-40 w-40 sm:h-52 sm:w-52', 'gap' => '2'])
        <div class="relative mx-auto flex max-w-md flex-col items-center px-6 text-center {{ $coverRoom }}">
            <div class="tc-rise" style="--i: 0"><div class="t-sway">@include('themes._engine.ornaments.crown', ['class' => 'h-16 w-56'])</div></div>
            <div style="--i: 1" class="tc-rise {{ $shape }} mt-2 h-40 w-32 overflow-hidden border-4 border-t-surface shadow-lg ring-1 ring-t-accent/60 [@media(max-height:700px)]:hidden">
                @if ($coverUrl)
                    <img src="{{ $coverUrl }}" alt="" class="h-full w-full object-cover">
                @else
                    @include('themes._engine.ornaments.monogram', ['withCorners' => false])
                @endif
            </div>
            <p class="tc-eyebrow tc-rise mt-5" style="--i: 2">The Wedding of</p>
            <h1 class="{{ $names }} tc-title tc-rise mt-4 leading-tight text-t-primary" style="--i: 3">{{ $groom }} <span class="mx-1 text-t-accent">&amp;</span> {{ $bride }}</h1>
            @if ($dateLabel)
                <p class="tc-num tc-rise mt-3 text-sm tracking-[0.2em]" style="--i: 4">{{ $dateLabel }}</p>
            @endif
            <div class="tc-rise" style="--i: 5">@include('themes._engine.partials.ornament')</div>
            <div class="tc-rise px-6 py-2" style="--i: 6">
                <p class="text-[11px] tracking-[0.18em] uppercase opacity-70">Kepada Yth. Bapak/Ibu/Saudara/i</p>
                <p class="tc-title mt-2 font-heading text-2xl font-semibold">{{ $guestName ?: 'Tamu Undangan' }}</p>
            </div>
            <button type="button" x-on:click="open()" class="tc-btn tc-rise mt-8 px-8 py-3.5 text-sm" style="--i: 7">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 9v.906a2.25 2.25 0 0 1-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 0 0 1.183 1.981l6.478 3.488m8.839 2.51-4.66-2.51m0 0-1.023-.55a2.25 2.25 0 0 0-2.134 0l-1.022.55m0 0-4.661 2.51m16.5 1.615a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V8.844a2.25 2.25 0 0 1 1.183-1.981l7.5-4.039a2.25 2.25 0 0 1 2.134 0l7.5 4.039a2.25 2.25 0 0 1 1.183 1.98V19.5Z"/></svg>
                Buka Undangan
            </button>
        </div>
    </section>

    @endif

    @include('themes._engine.partials.desktop-panel')

    {{-- On large screens the column docks right and the panel above fills the rest. --}}
    <main id="mulai" class="relative z-10 mx-auto max-w-md overflow-hidden bg-t-bg sm:border-x sm:border-t-soft lg:mr-0 lg:shadow-2xl">
        {{-- Sections in the customer's order; "page:{id}" entries are free canvas pages (ADR 012). --}}
        @foreach ($sectionOrder as $block)
            @if (str_starts_with($block, 'page:'))
                @if ($page = $canvasPages[substr($block, 5)] ?? null)
                    @include('themes._engine.canvas.page', ['page' => $page])
                @endif
            @else
                @include('themes._engine.sections.'.$block)
            @endif
        @endforeach

        <div class="t-grain pointer-events-none fixed inset-0 z-30"></div>

        <footer class="bg-t-primary px-6 py-7 text-center text-[11px] tracking-[0.18em] text-t-on/70 uppercase">
            Dibuat dengan <a href="{{ config('app.url') }}" class="font-semibold text-t-on underline-offset-4 hover:underline">{{ $siteName ?? config('app.name') }}</a>
        </footer>
    </main>
</x-invitation.layout>
