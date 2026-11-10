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
    // ?buka=1 skips the cover (editor preview, catalog thumbnails).
    $skipCover = request()->boolean('buka');
    $btn = 'rounded-full bg-t-primary px-5 py-2 text-xs font-semibold text-t-on transition hover:opacity-90';
    $btnOutline = 'rounded-full border border-t-primary/40 px-5 py-2 text-xs font-semibold text-t-primary transition hover:bg-t-soft';
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
    {{-- Cover --}}
    <section x-show="!opened" @if ($skipCover) x-cloak @endif x-transition:leave="transition duration-700" x-transition:leave-end="-translate-y-full opacity-0"
             class="fixed inset-0 z-40 flex items-center justify-center overflow-hidden bg-t-bg text-t-ink">
        @if ($coverUrl)
            <img src="{{ $coverUrl }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-30">
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-t-bg/70 via-t-bg/40 to-t-bg/95"></div>
        @if ($pattern)
            <div class="{{ $pattern }} absolute inset-0"></div>
        @endif
        <div class="relative mx-auto flex max-w-md flex-col items-center px-6 text-center">
            <p class="text-xs tracking-[0.35em] text-t-accent uppercase">The Wedding of</p>
            <h1 class="{{ $names }} mt-4 leading-tight text-t-primary">{{ $groom }} <span class="mx-1 text-t-accent">&amp;</span> {{ $bride }}</h1>
            @if ($dateLabel)
                <p class="mt-3 text-sm tracking-widest">{{ $dateLabel }}</p>
            @endif
            @include('themes._engine.partials.ornament')
            <div class="px-6 py-2">
                <p class="text-xs opacity-80">Kepada Yth. Bapak/Ibu/Saudara/i</p>
                <p class="mt-1 font-heading text-xl font-semibold">{{ $guestName ?: 'Tamu Undangan' }}</p>
            </div>
            <button type="button" x-on:click="open()" class="mt-8 inline-flex items-center gap-2 rounded-full bg-t-primary px-7 py-3 text-sm font-medium text-t-on transition hover:opacity-90">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 9v.906a2.25 2.25 0 0 1-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 0 0 1.183 1.981l6.478 3.488m8.839 2.51-4.66-2.51m0 0-1.023-.55a2.25 2.25 0 0 0-2.134 0l-1.022.55m0 0-4.661 2.51m16.5 1.615a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V8.844a2.25 2.25 0 0 1 1.183-1.981l7.5-4.039a2.25 2.25 0 0 1 2.134 0l7.5 4.039a2.25 2.25 0 0 1 1.183 1.98V19.5Z"/></svg>
                Buka Undangan
            </button>
        </div>
    </section>

    <main id="mulai" class="relative mx-auto max-w-md overflow-hidden bg-t-bg sm:border-x sm:border-t-soft">
        {{-- Hero --}}
        @switch($engine['hero'])
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
                            @endif
                        </div>
                        <h2 class="{{ $names }} mt-8 leading-tight text-t-primary">{{ $groom }} <span class="mx-1 text-t-accent">&amp;</span> {{ $bride }}</h2>
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
                        @endif
                    </div>
                    <h2 class="{{ $names }} relative mt-8 leading-tight text-t-primary">{{ $groom }} <span class="mx-1 text-t-accent">&amp;</span> {{ $bride }}</h2>
                    @if ($dateLabel)
                        <p class="mt-3 text-sm tracking-widest">{{ $dateLabel }}</p>
                    @endif
                </section>
        @endswitch

        @include('themes._engine.partials.ornament')

        {{-- Opening & quote --}}
        <section class="px-8 py-10 text-center">
            @if ($invitation->quote_text)
                <blockquote class="font-heading text-base leading-relaxed italic opacity-90">"{{ $invitation->quote_text }}"</blockquote>
                @if ($invitation->quote_source)
                    <p class="mt-3 text-xs font-semibold tracking-widest text-t-accent uppercase">{{ $invitation->quote_source }}</p>
                @endif
            @endif
            @if ($invitation->opening_text)
                <p @class(['text-sm leading-relaxed', 'mt-10' => $invitation->quote_text])>{{ $invitation->opening_text }}</p>
            @endif
        </section>

        {{-- Couple --}}
        <section class="space-y-12 px-8 pb-16 text-center">
            @include('themes._engine.partials.person', ['side' => 'groom', 'photo' => $groomPhotoUrl])
            <p class="font-script text-5xl text-t-accent">&amp;</p>
            @include('themes._engine.partials.person', ['side' => 'bride', 'photo' => $bridePhotoUrl])
        </section>

        {{-- Countdown --}}
        @if ($countdownTarget)
            <section class="relative overflow-hidden bg-t-primary px-6 py-14 text-center text-t-on"
                     x-data="{
                        target: new Date('{{ $countdownTarget }}').getTime(), d: 0, h: 0, m: 0, s: 0,
                        tick() {
                            const diff = Math.max(0, this.target - Date.now());
                            this.d = Math.floor(diff / 864e5); this.h = Math.floor(diff / 36e5) % 24;
                            this.m = Math.floor(diff / 6e4) % 60; this.s = Math.floor(diff / 1e3) % 60;
                        },
                        init() { this.tick(); setInterval(() => this.tick(), 1000); },
                     }">
                <h2 class="relative font-script text-5xl">Menuju Hari Bahagia</h2>
                <div class="relative mt-8 grid grid-cols-4 gap-3">
                    @foreach (['d' => 'Hari', 'h' => 'Jam', 'm' => 'Menit', 's' => 'Detik'] as $unit => $label)
                        <div class="rounded-2xl border border-t-on/20 py-4">
                            <p class="font-heading text-3xl font-semibold" x-text="{{ $unit }}">0</p>
                            <p class="mt-1 text-[11px] tracking-widest uppercase opacity-80">{{ $label }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Events --}}
        @if ($events->isNotEmpty())
            <section class="space-y-8 px-6 py-16">
                <h2 class="{{ $h2 }} text-center">Waktu &amp; Tempat</h2>
                @foreach ($events as $event)
                    <article class="{{ $card }} text-center">
                        <h3 class="font-heading text-2xl font-semibold text-t-primary">{{ $event->name }}</h3>
                        <p class="mt-4 font-medium">{{ $event->date->translatedFormat('l, d F Y') }}</p>
                        <p class="text-sm opacity-80">Pukul {{ $event->timeLabel() }}</p>
                        <div class="mx-auto my-5 h-px w-16 bg-t-accent/60"></div>
                        <p class="font-semibold">{{ $event->venue_name }}</p>
                        @if ($event->address)
                            <p class="mt-1 text-sm whitespace-pre-line opacity-80">{{ $event->address }}</p>
                        @endif
                        <div class="mt-6 flex flex-wrap justify-center gap-2">
                            @if ($event->maps_url)
                                <a href="{{ $event->maps_url }}" target="_blank" rel="noopener noreferrer" class="{{ $btn }}">Lihat Lokasi</a>
                            @endif
                            <a href="{{ $event->googleCalendarUrl($calendarTitle) }}" target="_blank" rel="noopener noreferrer" class="{{ $btnOutline }}">Simpan ke Kalender</a>
                        </div>
                    </article>
                @endforeach
            </section>
        @endif

        {{-- Love story --}}
        @if ($stories->isNotEmpty())
            <section class="relative bg-t-soft/60 px-6 py-16">
                <h2 class="{{ $h2 }} text-center">Cerita Kami</h2>
                <ol class="relative mt-10 space-y-10 border-l border-t-accent/50 pl-6">
                    @foreach ($stories as $story)
                        <li class="relative">
                            <span class="absolute top-1.5 -left-[31px] h-3 w-3 rounded-full bg-t-accent ring-4 ring-t-soft"></span>
                            @if ($story->story_date)
                                <p class="text-xs font-semibold tracking-widest text-t-accent uppercase">{{ $story->story_date }}</p>
                            @endif
                            <h3 class="mt-1 font-heading text-xl font-semibold text-t-primary">{{ $story->title }}</h3>
                            @if ($story->photoUrl())
                                <img src="{{ $story->photoUrl() }}" alt="" class="{{ $shape === 't-shape-circle' ? 't-shape-rounded' : $shape }} mt-3 w-full object-cover" loading="lazy">
                            @endif
                            <p class="mt-3 text-sm leading-relaxed whitespace-pre-line">{{ $story->body }}</p>
                        </li>
                    @endforeach
                </ol>
            </section>
        @endif

        {{-- Gallery --}}
        @if ($photos->isNotEmpty())
            <section class="px-4 py-16"
                     x-data="{ show: false, index: 0, photos: @js($photos->map->url()->values()) }"
                     x-on:keydown.escape.window="show = false">
                <h2 class="{{ $h2 }} text-center">Galeri</h2>
                <div class="mt-8 grid grid-cols-2 gap-2">
                    @foreach ($photos as $photo)
                        <button type="button" x-on:click="index = {{ $loop->index }}; show = true"
                                @class([$engine['shape'] === 'square' ? 'overflow-hidden' : 'overflow-hidden rounded-2xl', 'col-span-2' => $loop->index % 5 === 0])>
                            <img src="{{ $photo->url() }}" alt="{{ $photo->caption ?: 'Foto '.$loop->iteration }}" loading="lazy"
                                 @class(['w-full object-cover transition duration-500 hover:scale-105', 'aspect-[4/3]' => $loop->index % 5 === 0, 'aspect-square' => $loop->index % 5 !== 0])>
                        </button>
                    @endforeach
                </div>
                <div x-show="show" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4" x-on:click.self="show = false">
                    <img x-bind:src="photos[index]" alt="" class="max-h-[85vh] max-w-full rounded-lg object-contain">
                    <button type="button" x-on:click="show = false" class="absolute top-4 right-4 rounded-full bg-white/10 px-3 py-1 text-sm text-white" aria-label="Tutup">Tutup</button>
                    <button type="button" x-on:click="index = (index - 1 + photos.length) % photos.length" class="absolute left-2 rounded-full bg-white/10 px-3 py-2 text-white" aria-label="Sebelumnya">&lsaquo;</button>
                    <button type="button" x-on:click="index = (index + 1) % photos.length" class="absolute right-2 rounded-full bg-white/10 px-3 py-2 text-white" aria-label="Berikutnya">&rsaquo;</button>
                </div>
            </section>
        @endif

        {{-- Digital gift --}}
        @if ($giftAccounts->isNotEmpty())
            @include('themes._engine.partials.gift')
        @endif

        @if ($ticketQr ?? null)
            <x-invitation.ticket :qr="$ticketQr" :guest="$guest" />
        @endif

        {{-- RSVP & wishes --}}
        @if ($showInteraction)
            <section id="rsvp" class="px-6 py-16 text-center">
                <h2 class="{{ $h2 }}">
                    {{ $invitation->sectionEnabled('rsvp') && $invitation->sectionEnabled('wishes') ? 'Konfirmasi & Ucapan' : ($invitation->sectionEnabled('rsvp') ? 'Konfirmasi Kehadiran' : 'Ucapan & Doa') }}
                </h2>
                <p class="mx-auto mt-3 mb-8 max-w-xs text-sm opacity-80">Kehadiran dan doa restu Anda sangat berarti bagi kami.</p>
                @if ($demo ?? false)
                    @include('themes._engine.livewire.wishes', [
                        'submitted' => false, 'rsvpEnabled' => true, 'wishesEnabled' => true, 'attendance' => 'yes',
                        'attendanceOptions' => \App\Enums\Attendance::cases(), 'maxPax' => 2, 'preview' => true,
                        'wishes' => $demoWishes, 'total' => $demoWishes->count(),
                    ])
                @else
                    <livewire:invitation.public-wishes :invitation="$invitation" :guest-id="$guest?->id" :guest-name="$guestName" :preview="$preview" />
                @endif
            </section>
        @endif

        @include('themes._engine.partials.ornament')

        {{-- Closing --}}
        <section class="relative px-8 pt-10 pb-16 text-center">
            @if ($invitation->closing_text)
                <p class="text-sm leading-relaxed">{{ $invitation->closing_text }}</p>
            @endif
            <p class="mt-8 text-sm">Kami yang berbahagia,</p>
            <p class="{{ $names }} mt-2 text-t-primary">{{ $groom }} &amp; {{ $bride }}</p>
        </section>

        <footer class="bg-t-primary px-6 py-6 text-center text-xs text-t-on/80">
            Dibuat dengan <a href="{{ config('app.url') }}" class="font-semibold text-t-on">{{ $siteName ?? config('app.name') }}</a>
        </footer>
    </main>
</x-invitation.layout>
