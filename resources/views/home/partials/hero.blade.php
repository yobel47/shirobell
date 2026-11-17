{{-- Hero: editorial headline + an invitation mockup that really opens. --}}
@php
    $couples = ['budi-ani', 'raka-nadia', 'dimas-sekar', 'yoga-laras'];
@endphp
<section class="relative overflow-hidden">
    <div class="hero-glow pointer-events-none absolute inset-0" aria-hidden="true"></div>

    <div class="relative mx-auto grid max-w-6xl gap-14 px-4 pt-14 pb-16 sm:px-6 md:pt-20 lg:grid-cols-12 lg:gap-8 lg:pt-24 lg:pb-24">
        <div class="lg:col-span-8">
            <p class="eyebrow hero-fade" style="--d: 0ms">Undangan pernikahan online</p>

            <h1 class="display mt-7 text-[clamp(2.6rem,5.4vw,5.25rem)] text-ink">
                <span class="hero-line"><span style="--i: 0">Undangan cantik,</span></span>
                <span class="hero-line"><span style="--i: 1"><em>dibuat sendiri</em></span></span>
                <span class="hero-line"><span style="--i: 2">dalam hitungan menit.</span></span>
            </h1>

            <p class="hero-fade mt-8 max-w-xl text-lg leading-relaxed tracking-[-0.01em] text-gray-600" style="--d: 450ms">
                Pilih tema, isi data, bayar online, lalu bagikan link undangan dengan alamat Anda sendiri ke semua tamu lewat WhatsApp.
            </p>

            <div class="hero-fade mt-10 flex flex-col gap-3 sm:flex-row sm:items-center" style="--d: 550ms">
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg btn-arrow">
                    Buat Undangan Sekarang
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </a>
                <a href="#tema" class="btn btn-ghost btn-lg underline decoration-rose-brand-300 underline-offset-4">Lihat {{ $themes->count() }} tema</a>
            </div>
            <p class="hero-fade mt-5 text-sm text-gray-500" style="--d: 650ms">Gratis mencoba. Bayar hanya saat undangan siap diterbitkan.</p>
        </div>

        {{-- Invitation mockup --}}
        <div class="hero-fade mx-auto w-[17rem] sm:w-[18.5rem] lg:col-span-4 lg:mt-6 lg:mr-0" style="--d: 300ms" x-data="{ open: false }">
            <div class="phone">
                <div class="phone-screen">
                    {{-- Inside of the invitation --}}
                    <div class="phone-inside absolute inset-0 flex flex-col items-center justify-center gap-4 px-6 text-center" x-bind:class="open && 'is-open'" x-bind:aria-hidden="(!open).toString()">
                        <p class="text-[10px] tracking-[0.3em] text-rose-brand-600 uppercase" style="--i: 0">Sabtu, 14 Februari 2027</p>
                        <p class="font-display text-3xl text-ink italic" style="--i: 1">Budi &amp; Ani</p>
                        <div class="grid w-full grid-cols-4 gap-1.5" style="--i: 2">
                            @foreach (['88' => 'hari', '06' => 'jam', '42' => 'menit', '19' => 'detik'] as $value => $unit)
                                <div class="rounded-lg bg-rose-brand-50 py-2">
                                    <p class="font-display text-xl leading-none text-ink">{{ $value }}</p>
                                    <p class="mt-1 text-[9px] text-gray-500">{{ $unit }}</p>
                                </div>
                            @endforeach
                        </div>
                        <div class="w-full space-y-2 text-left" style="--i: 3">
                            <div class="rounded-xl border border-rose-brand-100 bg-white px-3.5 py-2.5">
                                <p class="text-[10px] font-semibold tracking-wider text-rose-brand-600 uppercase">Akad Nikah</p>
                                <p class="text-xs text-ink">08.00 WIB &middot; Masjid Al-Ikhlas</p>
                            </div>
                            <div class="rounded-xl border border-rose-brand-100 bg-white px-3.5 py-2.5">
                                <p class="text-[10px] font-semibold tracking-wider text-rose-brand-600 uppercase">Resepsi</p>
                                <p class="text-xs text-ink">11.00 WIB &middot; Gedung Melati</p>
                            </div>
                        </div>
                        <div class="grid w-full grid-cols-2 gap-2" style="--i: 4">
                            <span class="rounded-full bg-rose-brand-500 py-2 text-[11px] font-semibold text-white">Hadir</span>
                            <span class="rounded-full border border-rose-brand-200 py-2 text-[11px] font-semibold text-rose-brand-700">Berhalangan</span>
                        </div>
                        <button type="button" class="text-[11px] text-gray-500 underline underline-offset-2" style="--i: 5" x-on:click="open = false" x-bind:tabindex="open ? 0 : -1">Tutup undangan</button>
                    </div>

                    {{-- Cover --}}
                    <div class="phone-cover absolute inset-0 flex flex-col items-center justify-center bg-gradient-to-b from-rose-brand-50 via-cream to-rose-brand-100 px-6 text-center" x-bind:class="open && 'is-open'">
                        <x-brand.mark class="h-7 w-7 text-rose-brand-400"/>
                        <p class="mt-5 text-[10px] tracking-[0.3em] text-rose-brand-600 uppercase">The Wedding of</p>
                        <p class="mt-4 font-display text-5xl leading-none text-ink italic">Budi</p>
                        <p class="my-1 font-display text-2xl text-rose-brand-400">&amp;</p>
                        <p class="font-display text-5xl leading-none text-ink italic">Ani</p>
                        <div class="my-6 h-px w-14 bg-rose-brand-300"></div>
                        <p class="text-[10px] text-gray-500">Kepada Yth.</p>
                        <p class="mt-0.5 text-sm font-medium text-ink">Bapak/Ibu/Saudara/i</p>
                        <button type="button" class="mt-6 rounded-full bg-rose-brand-500 px-5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-rose-brand-600" x-on:click="open = true" x-bind:tabindex="open ? -1 : 0">
                            Buka Undangan
                        </button>
                    </div>
                </div>
            </div>

            {{-- Personal address, cycling through example couples --}}
            <p class="mt-6 text-center text-sm text-gray-500">
                <span class="sr-only">Contoh alamat undangan: budi-ani.{{ $domain }}</span>
                <span aria-hidden="true"
                      x-data="{ i: 0, n: {{ count($couples) }}, init() { if (matchMedia('(prefers-reduced-motion: reduce)').matches) return; setInterval(() => this.i = (this.i + 1) % this.n, 2600) } }">
                    <span class="cycle justify-items-end font-medium text-rose-brand-600">
                        @foreach ($couples as $couple)
                            <span x-bind:class="i === {{ $loop->index }} ? 'translate-y-0 opacity-100' : (i === ({{ $loop->index }} + 1) % n ? '-translate-y-full opacity-0' : 'translate-y-full opacity-0')"
                                  @class(['translate-y-full opacity-0' => ! $loop->first])>{{ $couple }}</span>
                        @endforeach
                    </span>.{{ $domain }}
                </span>
            </p>
        </div>
    </div>

    {{-- Facts strip: real numbers from settings and the theme catalog --}}
    <div class="relative border-y border-rose-brand-900/10">
        <dl class="mx-auto grid max-w-6xl grid-cols-3 divide-x divide-rose-brand-900/10 px-4 sm:px-6">
            @foreach ([
                [$themes->count(), 'tema siap pakai'],
                [$durationDays, 'hari undangan aktif'],
                [$galleryMax, 'foto di galeri'],
            ] as [$figure, $label])
                <div class="py-6 pl-4 first:pl-0 sm:py-8 sm:pl-8">
                    <dt class="sr-only">{{ $label }}</dt>
                    <dd>
                        <span class="block font-display text-4xl leading-none text-ink sm:text-5xl">{{ $figure }}</span>
                        <span class="mt-2 block text-xs text-gray-500 sm:text-sm">{{ $label }}</span>
                    </dd>
                </div>
            @endforeach
        </dl>
    </div>
</section>
