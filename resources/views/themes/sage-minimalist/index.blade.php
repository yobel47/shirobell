{{-- Theme: Sage Minimalist. Receives the data contract from App\Services\InvitationRenderer. --}}
<x-invitation.layout
    :invitation="$invitation" :preview="$preview" :demo="$demo ?? false" :music-url="$musicUrl" :cover-url="$coverUrl" :theme-style="$themeStyle ?? null"
    css="resources/css/themes/sage-minimalist.css"
    class="sm-pattern font-sans text-sm-ink"
    x-data="{
        opened: {{ request()->boolean('buka') ? 'true' : 'false' }},
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
    @php
        $dateLabel = $mainDate?->translatedFormat('l, d F Y');
        $groom = $invitation->groom_nickname ?: 'Mempelai Pria';
        $bride = $invitation->bride_nickname ?: 'Mempelai Wanita';
        // Scroll reveal (craft.css). Off in previews (?buka=1) so the editor frame shows everything.
        $reveal = request()->boolean('buka') ? '' : 'x-data="{ v: false }" x-intersect.once="v = true" x-bind:class="v ? \'t-in\' : \'t-reveal\'"';
    @endphp

    {{-- Cover --}}
    <section x-show="!opened" @if (request()->boolean('buka')) x-cloak @endif x-transition:leave="transition duration-[1100ms] ease-[cubic-bezier(0.76,0,0.24,1)]" x-transition:leave-end="-translate-y-full"
             class="fixed inset-0 z-40 flex items-center justify-center overflow-hidden bg-sm-cream text-sm-ink">
        @if ($coverUrl)
            <img src="{{ $coverUrl }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-25">
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-sm-cream/60 via-sm-cream/40 to-sm-cream/90"></div>
        <div class="relative mx-auto flex max-w-md flex-col items-center px-6 text-center">
            <p class="tc-eyebrow">The Wedding of</p>
            <h1 class="mt-4 font-serif text-5xl font-normal leading-tight text-sm-deep sm:text-6xl">{{ $groom }} <span class="text-4xl">&amp;</span> {{ $bride }}</h1>
            @if ($dateLabel)
                <p class="tc-num tc-rise mt-4 text-sm tracking-[0.2em]" style="--i: 2">{{ $dateLabel }}</p>
            @endif
            <div class="mt-10 border-y border-sm-rose/40 px-6 py-4">
                <p class="text-xs opacity-90">Kepada Yth. Bapak/Ibu/Saudara/i</p>
                <p class="mt-1 font-serif text-xl font-semibold">{{ $guestName ?: 'Tamu Undangan' }}</p>
            </div>
            <button type="button" x-on:click="open()" class="tc-rise mt-8 inline-flex items-center gap-2 rounded-full px-8! py-3.5! transition active:scale-[0.97] bg-sm-deep px-7 py-3 text-sm font-medium text-white transition hover:bg-sm-rose" style="--i: 4">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 9v.906a2.25 2.25 0 0 1-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 0 0 1.183 1.981l6.478 3.488m8.839 2.51-4.66-2.51m0 0-1.023-.55a2.25 2.25 0 0 0-2.134 0l-1.022.55m0 0-4.661 2.51m16.5 1.615a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V8.844a2.25 2.25 0 0 1 1.183-1.981l7.5-4.039a2.25 2.25 0 0 1 2.134 0l7.5 4.039a2.25 2.25 0 0 1 1.183 1.98V19.5Z"/></svg>
                Buka Undangan
            </button>
        </div>
    </section>

    <main id="mulai" class="mx-auto max-w-md bg-sm-cream sm:my-0 sm:border-x sm:border-sm-blush">
        {{-- Hero --}}
        <section class="relative px-6 pt-16 pb-14 text-center">
            <p class="tc-eyebrow">The Wedding of</p>
            <div class="sm-arch mx-auto mt-8 h-80 w-60 overflow-hidden border-4 border-white bg-sm-blush shadow-xl">
                @if ($coverUrl)
                    <img src="{{ $coverUrl }}" alt="Foto {{ $invitation->coupleName() }}" class="h-full w-full object-cover">
                @endif
            </div>
            <h2 class="tc-title mt-8 font-script text-6xl text-sm-deep">{{ $groom }} <span class="text-4xl text-sm-gold">&amp;</span> {{ $bride }}</h2>
            @if ($dateLabel)
                <p class="tc-num mt-4 text-sm tracking-[0.2em] text-sm-ink/80">{{ $dateLabel }}</p>
            @endif
        </section>

        @include('themes.sage-minimalist.partials.ornament')

        {{-- Opening & quote --}}
        <section class="px-8 py-14 text-center" {!! $reveal !!}>
            @if ($invitation->quote_text)
                <span class="block font-serif text-7xl leading-[0.5] text-sm-gold/60" aria-hidden="true">&ldquo;</span>
                <blockquote class="tc-pretty mt-4 font-serif text-lg leading-relaxed text-sm-ink/85 italic">{{ $invitation->quote_text }}</blockquote>
                @if ($invitation->quote_source)
                    <p class="tc-eyebrow mt-5">{{ $invitation->quote_source }}</p>
                @endif
            @endif
            @if ($invitation->opening_text)
                <p @class(['tc-pretty mx-auto max-w-sm text-[0.95rem] leading-[1.8]', 'mt-12' => $invitation->quote_text])>{{ $invitation->opening_text }}</p>
            @endif
        </section>

        {{-- Couple --}}
        <section class="space-y-14 px-8 pb-20 text-center">
            <p class="tc-eyebrow" {!! $reveal !!}>Mempelai</p>
            @include('themes.sage-minimalist.partials.person', ['side' => 'groom', 'photo' => $groomPhotoUrl])
            <div class="flex items-center justify-center gap-5 text-sm-gold" aria-hidden="true">
                <span class="h-px w-16 bg-current opacity-50"></span>
                <span class="font-script text-5xl">&amp;</span>
                <span class="h-px w-16 bg-current opacity-50"></span>
            </div>
            @include('themes.sage-minimalist.partials.person', ['side' => 'bride', 'photo' => $bridePhotoUrl])
        </section>

        {{-- Countdown --}}
        @if ($countdownTarget)
            <section class="bg-sm-deep px-6 py-14 text-center text-white"
                     x-data="{
                        target: new Date('{{ $countdownTarget }}').getTime(), d: 0, h: 0, m: 0, s: 0,
                        tick() {
                            const diff = Math.max(0, this.target - Date.now());
                            this.d = Math.floor(diff / 864e5); this.h = Math.floor(diff / 36e5) % 24;
                            this.m = Math.floor(diff / 6e4) % 60; this.s = Math.floor(diff / 1e3) % 60;
                        },
                        init() { this.tick(); setInterval(() => this.tick(), 1000); },
                     }">
                <p class="tc-eyebrow text-white/75!">Save the Date</p>
                <h2 class="tc-title mt-3 font-script text-5xl">Menuju Hari Bahagia</h2>
                {{-- Fixed-width digits so the numbers do not jump every second. --}}
                <div class="mx-auto mt-10 grid max-w-xs grid-cols-4 divide-x divide-white/20">
                    @foreach (['d' => 'Hari', 'h' => 'Jam', 'm' => 'Menit', 's' => 'Detik'] as $unit => $label)
                        <div class="px-1">
                            <p class="tc-num font-serif text-4xl leading-none font-semibold" x-text="String({{ $unit }}).padStart(2, '0')">00</p>
                            <p class="mt-2 text-[10px] tracking-[0.2em] uppercase opacity-75">{{ $label }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Events --}}
        @if ($events->isNotEmpty())
            <section class="space-y-8 px-6 py-20">
                <p class="tc-eyebrow" {!! $reveal !!}>Save the Date</p>
                <h2 class="tc-title mt-3 text-center font-script text-5xl text-sm-deep">Waktu &amp; Tempat</h2>
                @foreach ($events as $event)
                    <article class="rounded-3xl bg-white px-6 py-8 text-center">
                        <h3 class="tc-title font-serif text-2xl font-semibold text-sm-deep">{{ $event->name }}</h3>
                        <x-invitation.event-date :date="$event->date" class="mx-auto mt-6 max-w-[17rem]" />
                        <p class="tc-num mt-5 inline-flex items-center gap-2 text-sm text-sm-ink/85"><svg class="h-4 w-4 text-sm-rose" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 7v5l3 2"/></svg>Pukul {{ $event->timeLabel() }}</p>
                        <div class="mx-auto my-6 h-px w-12 bg-sm-gold/60"></div>
                        <div>
                            <p class="tc-title inline-flex items-start gap-2 font-semibold"><svg class="mt-0.5 h-4 w-4 shrink-0 text-sm-rose" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-7-5.5-7-11a7 7 0 1 1 14 0c0 5.5-7 11-7 11Z"/><circle cx="12" cy="10" r="2.5"/></svg>{{ $event->venue_name }}</p>
                            @if ($event->address)
                                <p class="tc-pretty mx-auto mt-2 max-w-xs text-sm leading-relaxed whitespace-pre-line text-sm-ink/80">{{ $event->address }}</p>
                            @endif
                        </div>
                        <div class="mt-7 flex flex-wrap justify-center gap-2">
                            @if ($event->maps_url)
                                <a href="{{ $event->maps_url }}" target="_blank" rel="noopener noreferrer" class="tc-btn">Lihat Lokasi</a>
                            @endif
                            <a href="{{ $event->googleCalendarUrl($calendarTitle) }}" target="_blank" rel="noopener noreferrer" class="tc-btn-outline">Simpan ke Kalender</a>
                        </div>
                    </article>
                @endforeach
            </section>
        @endif

        {{-- Love story --}}
        @if ($stories->isNotEmpty())
            <section class="bg-sm-blush/50 px-6 py-16">
                <p class="tc-eyebrow" {!! $reveal !!}>Perjalanan Kami</p>
                <h2 class="tc-title mt-3 text-center font-script text-5xl text-sm-deep">Cerita Kami</h2>
                <ol class="relative mt-10 space-y-10 border-l border-sm-rose/40 pl-6">
                    @foreach ($stories as $story)
                        <li class="relative" {!! $reveal !!}>
                            <span class="absolute top-1.5 -left-[31px] h-3 w-3 rounded-full bg-sm-rose ring-4 ring-sm-blush"></span>
                            @if ($story->story_date)
                                <p class="tc-eyebrow tc-eyebrow-start tc-num">{{ $story->story_date }}</p>
                            @endif
                            <h3 class="tc-title mt-2 font-serif text-2xl font-semibold text-sm-deep">{{ $story->title }}</h3>
                            @if ($story->photoUrl())
                                <img src="{{ $story->photoUrl() }}" alt="" class="mt-3 w-full rounded-2xl object-cover" loading="lazy">
                            @endif
                            <p class="tc-pretty mt-4 text-sm leading-[1.8] whitespace-pre-line">{{ $story->body }}</p>
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
                <p class="tc-eyebrow" {!! $reveal !!}>Momen Kami</p>
                <h2 class="tc-title mt-3 text-center font-script text-5xl text-sm-deep">Galeri</h2>
                <div class="mt-10 grid grid-cols-2 gap-2" {!! $reveal !!}>
                    @foreach ($photos as $photo)
                        <button type="button" x-on:click="index = {{ $loop->index }}; show = true" aria-label="Buka foto {{ $loop->iteration }}"
                                @class(['tc-zoom overflow-hidden rounded-2xl', 'col-span-2' => $loop->index % 5 === 0])>
                            <img src="{{ $photo->url() }}" alt="{{ $photo->caption ?: 'Foto '.$loop->iteration }}" loading="lazy"
                                 @class(['w-full object-cover', 'aspect-[4/3]' => $loop->index % 5 === 0, 'aspect-square' => $loop->index % 5 !== 0])>
                        </button>
                    @endforeach
                </div>
                <x-invitation.lightbox />
            </section>
        @endif

        {{-- Digital gift --}}
        @if ($giftAccounts->isNotEmpty())
            @include('themes.sage-minimalist.partials.gift')
        @endif

        @if ($ticketQr ?? null)
            <x-invitation.ticket :qr="$ticketQr" :guest="$guest" />
        @endif

        {{-- RSVP & wishes --}}
        @if ($showInteraction)
            <section id="rsvp" class="px-6 py-20 text-center">
                <p class="tc-eyebrow" {!! $reveal !!}>Kehadiran</p>
                <h2 class="tc-title mt-3 font-script text-5xl text-sm-deep">
                    {{ $invitation->sectionEnabled('rsvp') && $invitation->sectionEnabled('wishes') ? 'Konfirmasi & Ucapan' : ($invitation->sectionEnabled('rsvp') ? 'Konfirmasi Kehadiran' : 'Ucapan & Doa') }}
                </h2>
                <p class="tc-pretty mx-auto mt-4 mb-10 max-w-xs text-sm leading-relaxed text-sm-ink/80">Kehadiran dan doa restu Anda sangat berarti bagi kami.</p>
                @if ($demo ?? false)
                    @include('themes.sage-minimalist.livewire.wishes', [
                        'submitted' => false, 'rsvpEnabled' => true, 'wishesEnabled' => true, 'attendance' => 'yes',
                        'attendanceOptions' => \App\Enums\Attendance::cases(), 'maxPax' => 2, 'preview' => true,
                        'wishes' => $demoWishes, 'total' => $demoWishes->count(),
                    ])
                @else
                    <livewire:invitation.public-wishes :invitation="$invitation" :guest-id="$guest?->id" :guest-name="$guestName" :preview="$preview" />
                @endif
            </section>
        @endif

        @include('themes.sage-minimalist.partials.ornament')

        {{-- Closing --}}
        <section class="tc-stagger px-8 pt-12 pb-20 text-center" {!! $reveal !!}>
            <p class="tc-eyebrow">Terima Kasih</p>
            @if ($invitation->closing_text)
                <p class="tc-pretty mx-auto mt-6 max-w-sm text-[0.95rem] leading-[1.8]">{{ $invitation->closing_text }}</p>
            @endif
            <p class="mt-10 text-xs tracking-[0.2em] uppercase opacity-70">Kami yang berbahagia,</p>
            <p class="tc-title mt-3 font-script text-5xl text-sm-deep">{{ $groom }} &amp; {{ $bride }}</p>
        </section>

        <footer class="bg-sm-deep px-6 py-7 text-center text-[11px] tracking-[0.18em] uppercase text-white/80">
            Dibuat dengan <a href="{{ config('app.url') }}" class="font-semibold text-white">{{ $siteName ?? config('app.name') }}</a>
        </footer>
    </main>
</x-invitation.layout>
