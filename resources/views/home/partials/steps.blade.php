{{-- How it works: sticky heading on the left, numbered editorial list on the right. --}}
<section class="bg-white py-20 sm:py-28">
    <div class="mx-auto grid max-w-6xl gap-12 px-4 sm:px-6 lg:grid-cols-12 lg:gap-8">
        <div class="lg:col-span-5">
            <div class="lg:sticky lg:top-28" data-reveal>
                <p class="eyebrow">Cara kerja</p>
                <h2 class="display mt-6 text-[clamp(2.5rem,5vw,4.5rem)] text-ink">Semudah <em>tiga</em> langkah.</h2>
                <p class="mt-6 max-w-sm text-gray-600">Tanpa admin, tanpa antre. Semua Anda kerjakan sendiri, dari HP sekalipun.</p>
            </div>
        </div>

        <ol class="lg:col-span-6 lg:col-start-7">
            @foreach ([
                ['Pilih tema & isi data', 'Pilih tema favorit, isi nama mempelai, acara, foto, dan cerita Anda. Lihat hasilnya langsung.'],
                ['Bayar online', 'Bayar dengan QRIS, transfer bank (virtual account), atau e-wallet. Undangan langsung aktif otomatis.'],
                ['Bagikan ke tamu', 'Kirim link personal untuk setiap tamu lewat WhatsApp. Pantau RSVP dan ucapan secara real-time.'],
            ] as [$title, $text])
                <li class="grid grid-cols-[4.5rem_1fr] gap-4 border-t border-rose-brand-900/10 py-10 first:border-t-0 first:pt-0 sm:grid-cols-[6rem_1fr] sm:gap-6" data-reveal style="--d: {{ $loop->index * 80 }}ms">
                    <span class="font-display text-6xl leading-none text-rose-brand-300 italic sm:text-7xl" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <div class="pt-2">
                        <h3 class="text-xl font-semibold tracking-[-0.01em] text-ink">{{ $title }}</h3>
                        <p class="mt-3 leading-relaxed text-gray-600">{{ $text }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</section>
