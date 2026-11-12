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
