{{-- Pricing: the one dark section. One package, so the price itself is the hero. --}}
<section id="harga" class="scroll-mt-20 bg-wine py-20 text-cream sm:py-28">
    <div class="mx-auto grid max-w-6xl gap-14 px-4 sm:px-6 lg:grid-cols-12 lg:gap-8">
        <div class="lg:col-span-6" data-reveal>
            <p class="eyebrow text-rose-brand-300">Harga</p>
            <h2 class="display mt-6 text-[clamp(2.5rem,5vw,4.5rem)]">Satu paket. <em class="text-rose-brand-300">Semua terbuka.</em></h2>

            <p class="price-figure mt-12 flex items-start gap-3 whitespace-nowrap" aria-hidden="true">
                <span class="mt-[0.12em] text-[0.28em] tracking-normal text-rose-brand-300">Rp</span>{{ number_format($price, 0, ',', '.') }}
            </p>
            <p class="sr-only">Rp {{ number_format($price, 0, ',', '.') }}</p>
            <p class="mt-6 text-cream/70">aktif {{ $durationDays }} hari, bisa diperpanjang</p>

            <a href="{{ route('register') }}" class="btn btn-light btn-lg btn-arrow mt-10">
                Mulai Buat Undangan
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
            </a>
            <p class="mt-4 text-sm text-cream/55">Punya kode promo? Masukkan saat pembayaran.</p>
        </div>

        <ul class="self-end lg:col-span-5 lg:col-start-8">
            @foreach ([
                'Semua tema, bebas ganti kapan saja',
                'Subdomain pribadi',
                'Tamu tanpa batas + link personal',
                'RSVP & buku ucapan',
                'Amplop digital (QRIS & rekening)',
                'Galeri hingga '.$galleryMax.' foto, musik, love story',
            ] as $item)
                <li class="flex items-center gap-4 border-t border-cream/10 py-4 last:border-b" data-reveal style="--d: {{ $loop->index * 60 }}ms">
                    <svg class="h-4 w-4 shrink-0 text-rose-brand-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                    <span class="text-cream/90">{{ $item }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</section>
