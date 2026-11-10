<x-legal-page title="Syarat & Ketentuan" :legal="$legal" description="Syarat dan ketentuan penggunaan layanan undangan pernikahan online {{ $legal['site'] }}.">
    <p>
        Syarat & Ketentuan ini mengatur penggunaan layanan {{ $legal['site'] }} di {{ $legal['domain'] }}
        yang dikelola oleh <strong>{{ $legal['business'] }}</strong> ("kami"). Dengan membuat akun atau memakai
        layanan, Anda ("pengguna") menyetujui seluruh ketentuan di halaman ini.
    </p>

    <h2>1. Layanan</h2>
    <p>
        {{ $legal['site'] }} adalah layanan pembuatan undangan pernikahan online. Pengguna membuat, mengisi, dan
        menerbitkan undangan sendiri. Setiap undangan memiliki alamat sendiri berupa subdomain
        (contoh: <strong>nama-pasangan.{{ $legal['domain'] }}</strong>) dan dapat dibagikan kepada tamu.
    </p>

    <h2>2. Akun</h2>
    <ul>
        <li>Pengguna wajib memberikan nama dan alamat email yang benar serta memverifikasi email tersebut.</li>
        <li>Pengguna bertanggung jawab menjaga kerahasiaan kata sandi dan atas semua aktivitas di akunnya.</li>
        <li>Kami dapat menangguhkan atau menutup akun yang melanggar ketentuan ini.</li>
    </ul>

    <h2>3. Harga, pembayaran, dan masa aktif</h2>
    <ul>
        <li>
            Membuat dan menyunting undangan tidak dipungut biaya. Undangan baru dapat diakses tamu setelah paket
            dibayar. Harga paket saat ini <strong>Rp {{ number_format($legal['price'], 0, ',', '.') }}</strong>
            per undangan untuk masa aktif <strong>{{ $legal['days'] }} hari</strong>. Harga yang berlaku adalah
            harga yang tertera di halaman pembayaran saat order dibuat.
        </li>
        <li>
            Pembayaran diproses oleh mitra payment gateway kami, Midtrans (PT Midtrans). Kami tidak menyimpan data
            kartu atau kredensial pembayaran Anda.
        </li>
        <li>Undangan aktif otomatis setelah pembayaran dikonfirmasi oleh payment gateway.</li>
        <li>
            Setelah masa aktif berakhir, undangan tidak lagi dapat diakses tamu. Data undangan tetap tersimpan dan
            undangan dapat diaktifkan kembali dengan perpanjangan.
        </li>
        <li>Kode promo hanya berlaku sesuai ketentuan masing-masing dan tidak dapat diuangkan.</li>
        <li>Pengembalian dana diatur dalam <a href="{{ route('legal.refund') }}">Kebijakan Refund</a>.</li>
    </ul>

    <h2>4. Konten undangan</h2>
    <ul>
        <li>
            Pengguna bertanggung jawab penuh atas seluruh konten yang diunggah atau ditulis, termasuk nama, foto,
            musik, teks, lokasi acara, dan informasi rekening.
        </li>
        <li>
            Pengguna menjamin memiliki hak atau izin atas konten tersebut, termasuk hak cipta foto dan musik,
            serta izin dari orang yang namanya atau fotonya ditampilkan.
        </li>
        <li>
            Dilarang memuat konten yang melanggar hukum, mengandung SARA, pornografi, penipuan, ujaran kebencian,
            atau melanggar hak pihak lain.
        </li>
        <li>
            Undangan yang aktif dapat dilihat siapa pun yang memiliki tautannya. Pertimbangkan hal ini sebelum
            menampilkan informasi pribadi.
        </li>
    </ul>

    <h2>5. Amplop digital</h2>
    <p>
        Fitur amplop digital menampilkan nomor rekening atau QRIS milik pengguna sendiri. Dana dari tamu dikirim
        langsung ke rekening tersebut. Kami tidak menerima, menyimpan, atau meneruskan dana tersebut dan tidak
        bertanggung jawab atas kesalahan nomor rekening yang diisi pengguna maupun transaksi antara tamu dan
        pengguna.
    </p>

    <h2>6. Penangguhan dan bantuan oleh admin</h2>
    <ul>
        <li>
            Kami dapat menangguhkan undangan yang melanggar ketentuan ini atau atas permintaan pihak berwenang.
            Undangan yang ditangguhkan karena pelanggaran tidak berhak atas pengembalian dana.
        </li>
        <li>
            Untuk keperluan bantuan teknis, perbaikan, atau penanganan pelanggaran, admin kami dapat membuka dan
            mengubah isi undangan. Setiap akses dan perubahan oleh admin dicatat.
        </li>
    </ul>

    <h2>7. Ketersediaan layanan</h2>
    <p>
        Kami berupaya menjaga layanan tetap tersedia, tetapi tidak menjamin layanan bebas gangguan. Pemeliharaan
        terjadwal maupun gangguan di luar kendali kami (misalnya gangguan penyedia server, jaringan, atau payment
        gateway) dapat membuat layanan sementara tidak tersedia.
    </p>

    <h2>8. Batasan tanggung jawab</h2>
    <p>
        Sepanjang diizinkan hukum, tanggung jawab kami atas kerugian yang timbul dari penggunaan layanan terbatas
        pada jumlah yang dibayarkan pengguna untuk undangan yang bersangkutan.
    </p>

    <h2>9. Perubahan ketentuan</h2>
    <p>
        Kami dapat memperbarui Syarat & Ketentuan ini. Perubahan berlaku sejak dipublikasikan di halaman ini,
        dengan tanggal pembaruan di bagian atas. Penggunaan layanan setelah perubahan berarti pengguna menyetujui
        ketentuan yang baru.
    </p>

    <h2>10. Hukum yang berlaku</h2>
    <p>
        Ketentuan ini tunduk pada hukum Republik Indonesia. Perselisihan diselesaikan terlebih dahulu secara
        musyawarah.
    </p>

    <h2>11. Kontak</h2>
    @include('legal.partials.contact')
</x-legal-page>
