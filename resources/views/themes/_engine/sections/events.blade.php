{{-- Section "events" of the engine theme (ADR 012: order and visibility are configurable). --}}
{{-- Events: an editorial date (weekday | day | month year) leads each card. --}}
@if ($events->isNotEmpty())
    <section class="space-y-8 px-6 py-20">
        @include('themes._engine.partials.heading', ['eyebrow' => 'Save the Date', 'title' => 'Waktu & Tempat'])
        @foreach ($events as $event)
            <article class="{{ $card }} tc-stagger relative overflow-hidden text-center" {!! $reveal !!}>
                @if ($cardOrnaments)
                    @include('themes._engine.ornaments.corners', ['size' => 'h-14 w-14', 'gap' => '1'])
                @endif
                <h3 class="tc-title font-heading text-2xl font-semibold text-t-primary">{{ $event->name }}</h3>
                <x-invitation.event-date :date="$event->date" class="mx-auto mt-6 max-w-[17rem]" />
                <p class="tc-num mt-5 inline-flex items-center gap-2 text-sm">
                    <svg class="h-4 w-4 text-t-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 7v5l3 2"/></svg>
                    Pukul {{ $event->timeLabel() }}
                </p>
                <div class="mx-auto my-6 h-px w-12 bg-t-accent/60"></div>
                <div>
                    <p class="tc-title inline-flex items-start gap-2 font-semibold">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-t-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-7-5.5-7-11a7 7 0 1 1 14 0c0 5.5-7 11-7 11Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                        {{ $event->venue_name }}
                    </p>
                    @if ($event->address)
                        <p class="tc-pretty mx-auto mt-2 max-w-xs text-sm leading-relaxed whitespace-pre-line opacity-80">{{ $event->address }}</p>
                    @endif
                </div>
                <div class="mt-7 flex flex-wrap justify-center gap-2">
                    @if ($event->maps_url)
                        <a href="{{ $event->maps_url }}" target="_blank" rel="noopener noreferrer" class="{{ $btn }}">Lihat Lokasi</a>
                    @endif
                    <a href="{{ $event->googleCalendarUrl($calendarTitle) }}" target="_blank" rel="noopener noreferrer" class="{{ $btnOutline }}">Simpan ke Kalender</a>
                </div>
            </article>
        @endforeach
    </section>
@endif
