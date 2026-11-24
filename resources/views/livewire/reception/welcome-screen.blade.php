<div wire:poll.3s class="fixed inset-0 flex text-white" style="background-color: {{ $colors['primary'] }}"
     x-data="{ full: false, toggle() { document.fullscreenElement ? document.exitFullscreen() : document.documentElement.requestFullscreen(); } }"
     x-on:fullscreenchange.document="full = !!document.fullscreenElement">
    @if ($coverUrl)
        <img src="{{ $coverUrl }}" alt="" class="absolute inset-0 h-full w-full object-cover">
    @endif
    <div class="absolute inset-0 bg-linear-to-r from-black/80 via-black/55 to-black/30"></div>

    <div class="relative flex flex-1 flex-col justify-end p-[5vw]">
        <p class="flex items-center gap-[1vw] text-[1.1vw] font-semibold tracking-[0.4em] uppercase" style="color: {{ $colors['accent'] }}"><span class="h-px w-[3vw] bg-current opacity-70"></span>Selamat datang di pernikahan</p>
        <p class="mt-[1.2vw] font-display text-[6vw] leading-[0.95] tracking-[-0.02em] italic">{{ $invitation->groom_nickname }} <span style="color: {{ $colors['accent'] }}">&amp;</span> {{ $invitation->bride_nickname }}</p>
        @if ($mainDate)
            <p class="mt-[1.4vw] text-[1.4vw] tracking-[0.3em] tabular-nums opacity-85">{{ \Illuminate\Support\Carbon::parse($mainDate)->translatedFormat('d F Y') }}</p>
        @endif

        <div class="mt-[3vw] min-h-[12vw]" aria-live="polite">
            @if ($greeting)
                <div wire:key="greet-{{ $greeting->id }}" x-data x-init="if (! matchMedia('(prefers-reduced-motion: reduce)').matches) $el.animate([{ opacity: 0, transform: 'translateY(2.5vw) scale(0.98)' }, { opacity: 1, transform: 'none' }], { duration: 1100, easing: 'cubic-bezier(0.16, 1, 0.3, 1)' })"
                     class="inline-flex items-center gap-[2.4vw] rounded-[2vw] border border-white/20 bg-white/10 px-[3vw] py-[2.2vw] shadow-[0_2vw_4vw_-2vw_rgb(0_0_0/0.5)] backdrop-blur-md">
                    @if ($greeting->selfie_path)
                        <img src="{{ $greeting->selfieUrl() }}" alt="" class="h-[11vw] w-[11vw] rounded-full object-cover ring-4 ring-white/60">
                    @endif
                    <div>
                    <p class="text-[1.2vw] tracking-[0.2em] uppercase opacity-80">Terima kasih telah hadir</p>
                    <p class="mt-[0.4vw] font-display text-[4.6vw] leading-[1.05] font-medium tracking-[-0.01em]">{{ $greeting->name }}</p>
                    @if ($greeting->group_name)
                        <p class="text-[1.3vw] opacity-80">{{ $greeting->group_name }}</p>
                    @endif
                    @if ($greeting->table_number)
                        <p class="mt-[1vw] inline-block rounded-full bg-white px-[1.4vw] py-[0.4vw] text-[1.4vw] font-semibold tabular-nums text-gray-900">Meja {{ $greeting->table_number }}</p>
                    @endif
                    </div>
                </div>
            @else
                <p class="font-display text-[2.6vw] italic opacity-85">Silakan check-in di meja penerima tamu.</p>
            @endif
        </div>
    </div>

    @if ($arrivals->isNotEmpty())
        <aside class="relative hidden w-[22vw] flex-col gap-[0.8vw] overflow-hidden p-[2vw] md:flex">
            <p class="flex items-center gap-[0.6vw] text-[0.85vw] font-semibold tracking-[0.3em] uppercase opacity-70"><span class="h-[0.5vw] w-[0.5vw] animate-pulse rounded-full motion-reduce:animate-none" style="background-color: {{ $colors['accent'] }}"></span>Baru tiba</p>
            @foreach ($arrivals as $arrival)
                <div wire:key="a-{{ $arrival->id }}" class="rounded-[1vw] border border-white/15 bg-white/10 px-[1.2vw] py-[0.9vw] backdrop-blur">
                    <p class="truncate text-[1.1vw] font-semibold">{{ $arrival->name }}</p>
                    <p class="text-[0.85vw] tabular-nums opacity-70">{{ $arrival->checked_in_at->format('H.i') }} &middot; {{ $arrival->checked_in_pax }} orang</p>
                </div>
            @endforeach
        </aside>
    @endif

    <button type="button" x-on:click="toggle()" class="absolute top-4 right-4 rounded-full bg-black/40 px-4 py-2 text-xs text-white/80 backdrop-blur transition hover:bg-black/60"
            x-text="full ? 'Keluar layar penuh' : 'Layar penuh'">Layar penuh</button>
</div>
