<x-layouts.public>
    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div class="mx-auto grid max-w-6xl items-center gap-10 px-4 pt-12 pb-16 sm:px-6 md:grid-cols-2 md:pt-20 md:pb-24">
            <div>
                <p class="inline-flex rounded-full bg-rose-brand-100 px-3 py-1 text-xs font-semibold tracking-wide text-rose-brand-700 uppercase">Undangan pernikahan online</p>
                <h1 class="mt-5 font-serif text-4xl leading-tight font-semibold sm:text-5xl">
                    Undangan cantik, <span class="text-rose-brand-500 italic">dibuat sendiri</span> dalam hitungan menit.
                </h1>
                <p class="mt-5 max-w-lg text-base text-gray-600 sm:text-lg">
                    Pilih tema, isi data, bayar online, lalu bagikan link undangan dengan alamat Anda sendiri ke semua tamu lewat WhatsApp.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('register') }}" class="btn btn-primary px-7 py-3 text-base">Buat Undangan Sekarang</a>
                    <a href="#tema" class="btn btn-outline px-7 py-3 text-base">Lihat Tema</a>
                </div>
                <p class="mt-4 text-sm text-gray-500">Gratis mencoba. Bayar hanya saat undangan siap diterbitkan.</p>
            </div>

            {{-- Phone mockup --}}
            <div class="mx-auto w-64 sm:w-72">
                <div class="rounded-[2.5rem] border-8 border-ink bg-white p-1 shadow-2xl">
                    <div class="flex aspect-[9/17] flex-col items-center justify-center rounded-[2rem] bg-gradient-to-b from-rose-brand-50 via-white to-rose-brand-100 px-6 text-center">
                        <p class="text-[10px] tracking-[0.3em] text-rose-brand-600 uppercase">The Wedding of</p>
                        <p class="mt-4 font-serif text-4xl text-ink italic">Budi</p>
                        <p class="font-serif text-2xl text-rose-brand-400">&amp;</p>
                        <p class="font-serif text-4xl text-ink italic">Ani</p>
                        <div class="my-5 h-px w-16 bg-rose-brand-300"></div>
                        <p class="text-xs text-gray-500">Sabtu, 14 Februari 2027</p>
                        <p class="mt-6 text-[10px] text-gray-400">Kepada Yth.</p>
                        <p class="text-sm font-medium">Bapak/Ibu/Saudara/i</p>
                        <span class="mt-5 rounded-full bg-rose-brand-500 px-4 py-1.5 text-[11px] text-white">Buka Undangan</span>
                    </div>
                </div>
                <p class="mt-4 text-center text-sm text-gray-500"><span class="font-medium text-rose-brand-600">budi-ani</span>.{{ $domain }}</p>
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section class="bg-white py-16 sm:py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <h2 class="text-center font-serif text-3xl font-semibold">Semudah 3 langkah</h2>
            <div class="mt-10 grid gap-6 md:grid-cols-3">
                @foreach ([
                    ['1', 'Pilih tema & isi data', 'Pilih tema favorit, isi nama mempelai, acara, foto, dan cerita Anda. Lihat hasilnya langsung.'],
                    ['2', 'Bayar online', 'Bayar dengan QRIS, transfer bank (virtual account), atau e-wallet. Undangan langsung aktif otomatis.'],
                    ['3', 'Bagikan ke tamu', 'Kirim link personal untuk setiap tamu lewat WhatsApp. Pantau RSVP dan ucapan secara real-time.'],
                ] as [$step, $title, $text])
                    <div class="card">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-rose-brand-500 font-semibold text-white">{{ $step }}</span>
                        <h3 class="mt-4 text-lg font-semibold">{{ $title }}</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section id="fitur" class="scroll-mt-20 py-16 sm:py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <h2 class="text-center font-serif text-3xl font-semibold">Semua fitur, satu harga</h2>
            <p class="mx-auto mt-3 max-w-2xl text-center text-gray-600">Tidak ada paket bertingkat. Sekali bayar, semua fitur dan semua tema terbuka untuk Anda.</p>
            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    ['Alamat sendiri', 'Undangan di subdomain pilihan Anda, contoh nama-anda.'.$domain],
                    ['Ganti tema kapan saja', 'Semua tema bisa dipakai dan diganti tanpa kehilangan data.'],
                    ['Nama tamu personal', 'Setiap tamu mendapat link dengan namanya sendiri di sampul undangan.'],
                    ['RSVP & buku ucapan', 'Tamu konfirmasi kehadiran dan kirim doa. Rekap otomatis untuk Anda.'],
                    ['Amplop digital', 'Tampilkan QRIS dan nomor rekening Anda, agar tamu yang berhalangan tetap bisa memberi.'],
                    ['Galeri & cerita cinta', 'Hingga '.$galleryMax.' foto, love story, musik latar, countdown, dan peta lokasi.'],
                ] as [$title, $text])
                    <div class="rounded-2xl border border-rose-brand-100 bg-white p-5">
                        <h3 class="font-semibold text-rose-brand-700">{{ $title }}</h3>
                        <p class="mt-1.5 text-sm text-gray-600">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Themes (catalog filled in M2) --}}
    <section id="tema" class="scroll-mt-20 bg-white py-16 sm:py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <h2 class="text-center font-serif text-3xl font-semibold">Pilihan tema</h2>
            <p class="mx-auto mt-3 max-w-2xl text-center text-gray-600">Tema elegan yang dirancang untuk tampil sempurna di layar HP.</p>
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach (['Rose Garden', 'Classic Gold', 'Sage Minimalist'] as $themeName)
                    <div class="overflow-hidden rounded-2xl border border-rose-brand-100">
                        <div class="flex aspect-[4/5] items-center justify-center bg-gradient-to-br from-rose-brand-50 to-rose-brand-100">
                            <span class="font-serif text-2xl text-rose-brand-600 italic">{{ $themeName }}</span>
                        </div>
                        <div class="flex items-center justify-between p-4">
                            <p class="font-medium">{{ $themeName }}</p>
                            <span class="text-xs text-gray-400">Segera hadir</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Pricing --}}
    <section id="harga" class="scroll-mt-20 py-16 sm:py-20">
        <div class="mx-auto max-w-md px-4 sm:px-6">
            <h2 class="text-center font-serif text-3xl font-semibold">Harga</h2>
            <div class="card mt-8 border-rose-brand-300 text-center">
                <p class="text-sm font-semibold tracking-wide text-rose-brand-600 uppercase">Paket Lengkap</p>
                <p class="mt-3 font-serif text-5xl font-semibold">Rp {{ number_format($price, 0, ',', '.') }}</p>
                <p class="mt-1 text-sm text-gray-500">aktif {{ $durationDays }} hari, bisa diperpanjang</p>
                <ul class="mt-6 space-y-2.5 text-left text-sm">
                    @foreach ([
                        'Semua tema, bebas ganti kapan saja',
                        'Subdomain pribadi',
                        'Tamu tanpa batas + link personal',
                        'RSVP & buku ucapan',
                        'Amplop digital (QRIS & rekening)',
                        'Galeri hingga '.$galleryMax.' foto, musik, love story',
                    ] as $item)
                        <li class="flex gap-2.5">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-rose-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('register') }}" class="btn btn-primary mt-8 w-full py-3">Mulai Buat Undangan</a>
                <p class="mt-3 text-xs text-gray-500">Punya kode promo? Masukkan saat pembayaran.</p>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section id="faq" class="scroll-mt-20 bg-white py-16 sm:py-20">
        <div class="mx-auto max-w-3xl px-4 sm:px-6">
            <h2 class="text-center font-serif text-3xl font-semibold">Pertanyaan umum</h2>
            <div class="mt-8 divide-y divide-rose-brand-100 rounded-2xl border border-rose-brand-100">
                @foreach ([
                    ['Apakah saya bisa mencoba dulu sebelum membayar?', 'Bisa. Daftar gratis, buat undangan, dan lihat preview-nya. Pembayaran hanya diperlukan saat Anda ingin menerbitkan undangan.'],
                    ['Metode pembayaran apa saja yang tersedia?', 'QRIS, transfer bank melalui virtual account, dan e-wallet. Undangan aktif otomatis begitu pembayaran berhasil.'],
                    ['Berapa lama undangan aktif?', 'Undangan aktif selama '.$durationDays.' hari sejak pembayaran, dan bisa diperpanjang kapan saja.'],
                    ['Bagaimana cara kerja amplop digital?', 'Anda mengunggah QRIS atau mencantumkan rekening milik Anda sendiri. Tamu mengirim hadiah langsung ke Anda, tanpa perantara.'],
                    ['Apakah bisa ganti tema setelah membayar?', 'Bisa, kapan saja dan tanpa biaya tambahan. Data undangan Anda tetap tersimpan.'],
                ] as [$question, $answer])
                    <details class="group p-5">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-medium">
                            {{ $question }}
                            <svg class="h-5 w-5 shrink-0 text-rose-brand-500 transition group-open:rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                        </summary>
                        <p class="mt-3 text-sm text-gray-600">{{ $answer }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-16 sm:py-20">
        <div class="mx-auto max-w-4xl px-4 text-center sm:px-6">
            <h2 class="font-serif text-3xl font-semibold sm:text-4xl">Siap membagikan kabar bahagia?</h2>
            <a href="{{ route('register') }}" class="btn btn-primary mt-8 px-8 py-3 text-base">Buat Undangan Gratis</a>
        </div>
    </section>
</x-layouts.public>
