{{-- Section "countdown" of the engine theme (ADR 012: order and visibility are configurable). --}}
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
        @if ($pattern)<div class="{{ $pattern }} absolute inset-0 opacity-40 mix-blend-overlay"></div>@endif
        <p class="tc-eyebrow relative text-t-on/75!">Save the Date</p>
        <h2 class="tc-title relative mt-3 font-script text-5xl">Menuju Hari Bahagia</h2>
        {{-- Fixed-width digits so the numbers do not jump every second. --}}
        <div class="relative mx-auto mt-10 grid max-w-xs grid-cols-4 divide-x divide-t-on/20" {!! $reveal !!}>
            @foreach (['d' => 'Hari', 'h' => 'Jam', 'm' => 'Menit', 's' => 'Detik'] as $unit => $label)
                <div class="px-1">
                    <p class="tc-num font-heading text-4xl leading-none font-semibold" x-text="String({{ $unit }}).padStart(2, '0')">00</p>
                    <p class="mt-2 text-[10px] tracking-[0.2em] uppercase opacity-75">{{ $label }}</p>
                </div>
            @endforeach
        </div>
        @if ($mainDate)
            <p class="tc-num relative mt-10 text-xs tracking-[0.25em] uppercase opacity-80">{{ $mainDate->translatedFormat('d F Y') }}</p>
        @endif
    </section>
@endif
