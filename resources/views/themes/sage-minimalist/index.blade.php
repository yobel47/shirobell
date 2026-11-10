{{-- Theme: Sage Minimalist. Receives the data contract from App\Services\InvitationRenderer. --}}
<x-invitation.layout
    :invitation="$invitation" :preview="$preview" :demo="$demo ?? false" :music-url="$musicUrl" :cover-url="$coverUrl"
    css="resources/css/themes/sage-minimalist.css"
    class="sm-pattern font-sans text-sm-ink"
    x-data="{
        opened: false,
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
    x-init="document.documentElement.style.overflow = 'hidden'"
>
    @php
        $dateLabel = $mainDate?->translatedFormat('l, d F Y');
        $groom = $invitation->groom_nickname ?: 'Mempelai Pria';
        $bride = $invitation->bride_nickname ?: 'Mempelai Wanita';
    @endphp

    {{-- Cover --}}
    <section x-show="!opened" x-transition:leave="transition duration-700" x-transition:leave-end="-translate-y-full opacity-0"
             class="fixed inset-0 z-40 flex items-center justify-center overflow-hidden bg-sm-cream text-sm-ink">
        @if ($coverUrl)
            <img src="{{ $coverUrl }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-25">
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-sm-cream/60 via-sm-cream/40 to-sm-cream/90"></div>
        <div class="relative mx-auto flex max-w-md flex-col items-center px-6 text-center">
            <p class="text-xs tracking-[0.35em] text-sm-rose uppercase">The Wedding of</p>
            <h1 class="mt-4 font-serif text-5xl font-normal leading-tight text-sm-deep sm:text-6xl">{{ $groom }} <span class="text-4xl">&amp;</span> {{ $bride }}</h1>
            @if ($dateLabel)
                <p class="mt-3 text-sm tracking-widest">{{ $dateLabel }}</p>
            @endif
            <div class="mt-10 border-y border-sm-rose/40 px-6 py-4">
                <p class="text-xs opacity-90">Kepada Yth. Bapak/Ibu/Saudara/i</p>
                <p class="mt-1 font-serif text-xl font-semibold">{{ $guestName ?: 'Tamu Undangan' }}</p>
            </div>
            <button type="button" x-on:click="open()" class="mt-8 inline-flex items-center gap-2 rounded-full bg-sm-deep px-7 py-3 text-sm font-medium text-white transition hover:bg-sm-rose">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 9v.906a2.25 2.25 0 0 1-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 0 0 1.183 1.981l6.478 3.488m8.839 2.51-4.66-2.51m0 0-1.023-.55a2.25 2.25 0 0 0-2.134 0l-1.022.55m0 0-4.661 2.51m16.5 1.615a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V8.844a2.25 2.25 0 0 1 1.183-1.981l7.5-4.039a2.25 2.25 0 0 1 2.134 0l7.5 4.039a2.25 2.25 0 0 1 1.183 1.98V19.5Z"/></svg>
                Buka Undangan
            </button>
        </div>
    </section>

    <main id="mulai" class="mx-auto max-w-md bg-sm-cream sm:my-0 sm:border-x sm:border-sm-blush">
        {{-- Hero --}}
        <section class="relative px-6 pt-16 pb-14 text-center">
            <p class="text-xs tracking-[0.35em] text-sm-rose uppercase">The Wedding of</p>
            <div class="sm-arch mx-auto mt-8 h-80 w-60 overflow-hidden border-4 border-white bg-sm-blush shadow-xl">
                @if ($coverUrl)
                    <img src="{{ $coverUrl }}" alt="Foto {{ $invitation->coupleName() }}" class="h-full w-full object-cover">
                @endif
            </div>
            <h2 class="mt-8 font-script text-6xl text-sm-deep">{{ $groom }} <span class="text-4xl text-sm-gold">&amp;</span> {{ $bride }}</h2>
            @if ($dateLabel)
                <p class="mt-3 text-sm tracking-widest text-sm-ink/80">{{ $dateLabel }}</p>
            @endif
        </section>

        @include('themes.sage-minimalist.partials.ornament')

        {{-- Opening & quote --}}
        <section class="px-8 py-12 text-center">
            @if ($invitation->quote_text)
                <blockquote class="text-sm leading-relaxed text-sm-ink/80 italic">"{{ $invitation->quote_text }}"</blockquote>
                @if ($invitation->quote_source)
                    <p class="mt-3 text-xs font-semibold tracking-widest text-sm-rose uppercase">{{ $invitation->quote_source }}</p>
                @endif
            @endif
            @if ($invitation->opening_text)
                <p @class(['text-sm leading-relaxed', 'mt-10' => $invitation->quote_text])>{{ $invitation->opening_text }}</p>
            @endif
        </section>

        {{-- Couple --}}
        <section class="space-y-12 px-8 pb-16 text-center">
            @include('themes.sage-minimalist.partials.person', ['side' => 'groom', 'photo' => $groomPhotoUrl])
            <p class="font-script text-5xl text-sm-gold">&amp;</p>
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
                <h2 class="font-script text-5xl">Menuju Hari Bahagia</h2>
                <div class="mt-8 grid grid-cols-4 gap-3">
                    @foreach (['d' => 'Hari', 'h' => 'Jam', 'm' => 'Menit', 's' => 'Detik'] as $unit => $label)
                        <div class="rounded-2xl bg-white/10 py-4">
                            <p class="font-serif text-3xl font-semibold" x-text="{{ $unit }}">0</p>
                            <p class="mt-1 text-[11px] tracking-widest uppercase opacity-80">{{ $label }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Events --}}
        @if ($events->isNotEmpty())
            <section class="space-y-6 px-6 py-16">
                <h2 class="text-center font-script text-5xl text-sm-deep">Waktu &amp; Tempat</h2>
                @foreach ($events as $event)
                    <article class="rounded-3xl bg-white px-6 py-8 text-center">
                        <h3 class="font-serif text-2xl font-semibold text-sm-deep">{{ $event->name }}</h3>
                        <p class="mt-4 font-medium">{{ $event->date->translatedFormat('l, d F Y') }}</p>
                        <p class="text-sm text-sm-ink/80">Pukul {{ $event->timeLabel() }}</p>
                        <div class="mx-auto my-5 h-px w-16 bg-sm-gold/50"></div>
                        <p class="font-semibold">{{ $event->venue_name }}</p>
                        @if ($event->address)
                            <p class="mt-1 text-sm whitespace-pre-line text-sm-ink/80">{{ $event->address }}</p>
                        @endif
                        <div class="mt-6 flex flex-wrap justify-center gap-2">
                            @if ($event->maps_url)
                                <a href="{{ $event->maps_url }}" target="_blank" rel="noopener noreferrer" class="rounded-full bg-sm-deep px-5 py-2 text-xs font-semibold text-white hover:bg-sm-rose">Lihat Lokasi</a>
                            @endif
                            <a href="{{ $event->googleCalendarUrl($calendarTitle) }}" target="_blank" rel="noopener noreferrer" class="rounded-full border border-sm-deep/40 px-5 py-2 text-xs font-semibold text-sm-deep hover:bg-sm-blush">Simpan ke Kalender</a>
                        </div>
                    </article>
                @endforeach
            </section>
        @endif

        {{-- Love story --}}
        @if ($stories->isNotEmpty())
            <section class="bg-sm-blush/50 px-6 py-16">
                <h2 class="text-center font-script text-5xl text-sm-deep">Cerita Kami</h2>
                <ol class="relative mt-10 space-y-10 border-l border-sm-rose/40 pl-6">
                    @foreach ($stories as $story)
                        <li class="relative">
                            <span class="absolute top-1.5 -left-[31px] h-3 w-3 rounded-full bg-sm-rose ring-4 ring-sm-blush"></span>
                            @if ($story->story_date)
                                <p class="text-xs font-semibold tracking-widest text-sm-rose uppercase">{{ $story->story_date }}</p>
                            @endif
                            <h3 class="mt-1 font-serif text-xl font-semibold text-sm-deep">{{ $story->title }}</h3>
                            @if ($story->photoUrl())
                                <img src="{{ $story->photoUrl() }}" alt="" class="mt-3 w-full rounded-2xl object-cover" loading="lazy">
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
                <h2 class="text-center font-script text-5xl text-sm-deep">Galeri</h2>
                <div class="mt-8 grid grid-cols-2 gap-2">
                    @foreach ($photos as $photo)
                        <button type="button" x-on:click="index = {{ $loop->index }}; show = true"
                                @class(['overflow-hidden rounded-2xl', 'col-span-2' => $loop->index % 5 === 0])>
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
            @include('themes.sage-minimalist.partials.gift')
        @endif

        {{-- RSVP & wishes --}}
        @if ($showInteraction)
            <section id="rsvp" class="px-6 py-16 text-center">
                <h2 class="font-script text-5xl text-sm-deep">
                    {{ $invitation->sectionEnabled('rsvp') && $invitation->sectionEnabled('wishes') ? 'Konfirmasi & Ucapan' : ($invitation->sectionEnabled('rsvp') ? 'Konfirmasi Kehadiran' : 'Ucapan & Doa') }}
                </h2>
                <p class="mx-auto mt-3 mb-8 max-w-xs text-sm text-sm-ink/80">Kehadiran dan doa restu Anda sangat berarti bagi kami.</p>
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
        <section class="px-8 pt-10 pb-16 text-center">
            @if ($invitation->closing_text)
                <p class="text-sm leading-relaxed">{{ $invitation->closing_text }}</p>
            @endif
            <p class="mt-8 text-sm">Kami yang berbahagia,</p>
            <p class="mt-2 font-script text-5xl text-sm-deep">{{ $groom }} &amp; {{ $bride }}</p>
        </section>

        <footer class="bg-sm-deep px-6 py-6 text-center text-xs text-white/80">
            Dibuat dengan <a href="{{ config('app.url') }}" class="font-semibold text-white">{{ $siteName ?? config('app.name') }}</a>
        </footer>
    </main>
</x-invitation.layout>
