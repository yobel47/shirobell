{{-- Section "events" of the engine theme (ADR 012: order and visibility are configurable). --}}
{{-- Events --}}
@if ($events->isNotEmpty())
    <section class="space-y-8 px-6 py-16">
        @include('themes._engine.ornaments.crown', ['class' => 'h-9 w-36'])
        <h2 class="{{ $h2 }} text-center">Waktu &amp; Tempat</h2>
        @foreach ($events as $event)
            <article class="{{ $card }} relative overflow-hidden text-center" {!! $reveal !!}>
                @if ($cardOrnaments)
                    @include('themes._engine.ornaments.corners', ['size' => 'h-14 w-14', 'gap' => '1'])
                @endif
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
