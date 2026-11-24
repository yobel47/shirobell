{{-- Features: bento grid. Each tile shows the feature instead of only describing it. --}}
@php
    $registry = app(\App\Services\ThemeRegistry::class);
    $guests = ['Pak Joko & Keluarga', 'Ibu Sari Wulandari', 'Dimas & Partner', 'Mbak Laras'];
    // Decorative QR: a fixed 9x9 pattern with the three finder squares.
    $qr = '111011111101101001101010101011100010000110111010111011001101011010001111110110111';
@endphp
<section id="fitur" class="scroll-mt-20 py-20 sm:py-28">
    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        <div class="grid gap-6 lg:grid-cols-12 lg:items-end" data-reveal>
            <div class="lg:col-span-7">
                <p class="eyebrow">Fitur</p>
                <h2 class="display mt-6 text-[clamp(2.5rem,5vw,4.5rem)] text-ink">Semua fitur, <em>satu harga.</em></h2>
            </div>
            <p class="max-w-md text-gray-600 lg:col-span-5 lg:justify-self-end">Tidak ada paket bertingkat. Sekali bayar, semua fitur dan semua tema terbuka untuk Anda.</p>
        </div>

        <div class="mt-14 grid gap-4 md:grid-cols-6">
            {{-- Personal guest name --}}
            <article class="tile flex flex-col justify-between gap-10 bg-rose-brand-50 md:col-span-4 md:row-span-2" data-reveal>
                <div class="max-w-sm">
                    <h3 class="text-xl font-semibold tracking-[-0.01em] text-ink">Nama tamu personal</h3>
                    <p class="mt-2 text-gray-600">Setiap tamu mendapat link dengan namanya sendiri di sampul undangan.</p>
                </div>
                <div class="mx-auto w-full max-w-sm rounded-2xl border border-rose-brand-200 bg-cream px-6 py-8 text-center shadow-[0_20px_40px_-24px_rgb(89_52_59/0.35)]" aria-hidden="true"
                     x-data="{ i: 0, n: {{ count($guests) }}, init() { if (matchMedia('(prefers-reduced-motion: reduce)').matches) return; setInterval(() => this.i = (this.i + 1) % this.n, 2200) } }">
                    <p class="text-[10px] tracking-[0.3em] text-rose-brand-600 uppercase">Kepada Yth.</p>
                    <p class="mt-3 font-display text-3xl text-ink italic sm:text-4xl">
                        <span class="cycle w-full justify-items-center">
                            @foreach ($guests as $guest)
                                <span x-bind:class="i === {{ $loop->index }} ? 'translate-y-0 opacity-100' : (i === ({{ $loop->index }} + 1) % n ? '-translate-y-full opacity-0' : 'translate-y-full opacity-0')"
                                      @class(['translate-y-full opacity-0' => ! $loop->first])>{{ $guest }}</span>
                            @endforeach
                        </span>
                    </p>
                    <p class="mt-3 text-xs text-gray-500">Mohon maaf bila ada kesalahan penulisan nama</p>
                </div>
            </article>

            {{-- Own address --}}
            <article class="tile md:col-span-2" data-reveal style="--d: 80ms">
                <h3 class="text-lg font-semibold text-ink">Alamat sendiri</h3>
                <p class="mt-2 text-sm text-gray-600">Undangan di subdomain pilihan Anda.</p>
                <p class="mt-8 truncate rounded-full bg-cream px-4 py-2.5 font-mono text-xs text-ink">
                    <span class="text-rose-brand-600">nama-anda</span>.{{ $domain }}
                </p>
            </article>

            {{-- Digital envelope --}}
            <article class="tile flex items-start justify-between gap-6 md:col-span-2" data-reveal style="--d: 160ms">
                <div>
                    <h3 class="text-lg font-semibold text-ink">Amplop digital</h3>
                    <p class="mt-2 text-sm text-gray-600">QRIS dan rekening Anda sendiri, langsung tanpa perantara.</p>
                </div>
                <div class="grid h-20 w-20 shrink-0 grid-cols-9 gap-px rounded-lg border border-rose-brand-100 p-1.5" aria-hidden="true">
                    @foreach (str_split($qr) as $bit)
                        <span @class(['qr-cell' => $bit === '1'])></span>
                    @endforeach
                </div>
            </article>

            {{-- RSVP & wishes --}}
            <article class="tile md:col-span-3" data-reveal>
                <h3 class="text-lg font-semibold text-ink">RSVP &amp; buku ucapan</h3>
                <p class="mt-2 text-sm text-gray-600">Tamu konfirmasi kehadiran dan kirim doa. Rekap otomatis untuk Anda.</p>
                <div class="mt-8 space-y-2.5" aria-hidden="true">
                    <div class="w-fit max-w-[85%] rounded-2xl rounded-bl-sm bg-rose-brand-50 px-4 py-2.5 text-sm text-ink">Selamat berbahagia, semoga sakinah mawaddah warahmah.</div>
                    <div class="ml-auto w-fit max-w-[85%] rounded-2xl rounded-br-sm bg-cream px-4 py-2.5 text-sm text-ink">Insya Allah hadir bersama keluarga.</div>
                </div>
            </article>

            {{-- Gallery & story --}}
            <article class="tile flex flex-col justify-between gap-8 md:col-span-3" data-reveal style="--d: 80ms">
                <div class="grid grid-cols-4 gap-2" aria-hidden="true">
                    @foreach (['from-rose-brand-100 to-rose-brand-200', 'from-cream to-rose-brand-100', 'from-rose-brand-200 to-rose-brand-300', 'from-rose-brand-50 to-rose-brand-200'] as $tint)
                        <span @class(['aspect-[3/4] bg-gradient-to-b', $tint, 'rounded-t-full rounded-b-lg' => $loop->odd, 'rounded-lg' => $loop->even])></span>
                    @endforeach
                </div>
                <div class="flex items-end justify-between gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-ink">Galeri &amp; cerita cinta</h3>
                    <p class="mt-2 max-w-xs text-sm text-gray-600">Love story, musik latar, countdown, dan peta lokasi.</p>
                </div>
                <p class="shrink-0 text-right">
                    <span class="block font-display text-7xl leading-none text-rose-brand-500 italic">{{ $galleryMax }}</span>
                    <span class="text-xs text-gray-500">foto</span>
                </p>
                </div>
            </article>

            {{-- Switch theme: real palettes from the catalog --}}
            <article class="tile flex flex-col gap-6 md:col-span-6 md:flex-row md:items-center md:justify-between" data-reveal>
                <div>
                    <h3 class="text-lg font-semibold text-ink">Ganti tema kapan saja</h3>
                    <p class="mt-2 text-sm text-gray-600">Semua tema bisa dipakai dan diganti tanpa kehilangan data.</p>
                </div>
                <div class="flex flex-wrap gap-2 md:justify-end" aria-hidden="true">
                    @foreach ($themes->take(10) as $theme)
                        @php [$bg, $primary] = $registry->swatch($theme->slug); @endphp
                        <span class="h-9 w-9 rounded-full ring-1 ring-black/5" title="{{ $theme->name }}" style="background: linear-gradient(135deg, {{ $bg }} 50%, {{ $primary }} 50%)"></span>
                    @endforeach
                    @if ($themes->count() > 10)
                        <span class="flex h-9 items-center rounded-full bg-cream px-3 text-xs font-semibold text-ink">+{{ $themes->count() - 10 }}</span>
                    @endif
                </div>
            </article>
        </div>
    </div>
</section>
