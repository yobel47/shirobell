<div wire:poll.3s class="fixed inset-0 flex text-white" style="background-color: {{ $colors['primary'] }}"
     x-data="{ full: false, toggle() { document.fullscreenElement ? document.exitFullscreen() : document.documentElement.requestFullscreen(); } }"
     x-on:fullscreenchange.document="full = !!document.fullscreenElement">
    @if ($coverUrl)
        <img src="{{ $coverUrl }}" alt="" class="absolute inset-0 h-full w-full object-cover">
    @endif
    <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/55 to-black/30"></div>

    <div class="relative flex flex-1 flex-col justify-end p-[5vw]">
        <p class="text-[1.2vw] tracking-[0.4em] uppercase" style="color: {{ $colors['accent'] }}">Selamat datang di pernikahan</p>
        <p class="mt-[1vw] font-serif text-[5vw] leading-none font-semibold">{{ $invitation->groom_nickname }} <span style="color: {{ $colors['accent'] }}">&amp;</span> {{ $invitation->bride_nickname }}</p>
        @if ($mainDate)
            <p class="mt-[1vw] text-[1.5vw] tracking-[0.3em] opacity-90">{{ \Illuminate\Support\Carbon::parse($mainDate)->format('d · m · Y') }}</p>
        @endif

        <div class="mt-[3vw] min-h-[12vw]" aria-live="polite">
            @if ($greeting)
                <div wire:key="greet-{{ $greeting->id }}" x-data x-init="$el.animate([{ opacity: 0, transform: 'translateY(2vw)' }, { opacity: 1, transform: 'none' }], { duration: 700, easing: 'ease-out' })"
                     class="inline-block rounded-[2vw] border border-white/25 bg-white/10 px-[3vw] py-[2vw] backdrop-blur-md">
                    <p class="text-[1.3vw] opacity-90">Terima kasih telah hadir,</p>
                    <p class="font-serif text-[4.2vw] leading-tight font-semibold">{{ $greeting->name }}</p>
                    @if ($greeting->group_name)
                        <p class="text-[1.3vw] opacity-80">{{ $greeting->group_name }}</p>
                    @endif
                </div>
            @else
                <p class="font-serif text-[2.4vw] italic opacity-90">Silakan check-in di meja penerima tamu.</p>
            @endif
        </div>
    </div>

    @if ($arrivals->isNotEmpty())
        <aside class="relative hidden w-[22vw] flex-col gap-[0.8vw] overflow-hidden p-[2vw] md:flex">
            <p class="text-[0.9vw] tracking-[0.3em] uppercase opacity-70">Baru tiba</p>
            @foreach ($arrivals as $arrival)
                <div wire:key="a-{{ $arrival->id }}" class="rounded-[1vw] border border-white/15 bg-white/10 px-[1.2vw] py-[0.9vw] backdrop-blur">
                    <p class="truncate text-[1.1vw] font-semibold">{{ $arrival->name }}</p>
                    <p class="text-[0.85vw] opacity-70">{{ $arrival->checked_in_at->format('H.i') }} &middot; {{ $arrival->checked_in_pax }} orang</p>
                </div>
            @endforeach
        </aside>
    @endif

    <button type="button" x-on:click="toggle()" class="absolute top-4 right-4 rounded-full bg-black/40 px-4 py-2 text-xs text-white/80 hover:bg-black/60"
            x-text="full ? 'Keluar layar penuh' : 'Layar penuh'">Layar penuh</button>
</div>
