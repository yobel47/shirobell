<x-legal-page title="Kebijakan Privasi" :legal="$legal" description="Cara {{ $legal['site'] }} mengumpulkan, memakai, dan melindungi data pribadi.">
    <p>
        Kebijakan Privasi ini menjelaskan bagaimana <strong>{{ $legal['business'] }}</strong> ("kami") sebagai
        pengelola {{ $legal['site'] }} mengumpulkan, memakai, menyimpan, dan melindungi data pribadi, sesuai
        Undang-Undang Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi.
    </p>

    <h2>1. Data yang kami kumpulkan</h2>
    <ul>
        <li><strong>Data akun:</strong> nama, email, nomor WhatsApp (opsional), dan kata sandi yang disimpan dalam bentuk terenkripsi (hash).</li>
        <li>
            <strong>Isi undangan:</strong> data yang Anda masukkan, seperti nama mempelai dan orang tua, foto,
            cerita, jadwal dan lokasi acara, musik, serta nomor rekening atau QRIS untuk amplop digital.
        </li>
        <li>
            <strong>Data tamu:</strong> nama tamu yang Anda masukkan, serta konfirmasi kehadiran, jumlah tamu,
            dan ucapan yang dikirim tamu. Untuk mencegah spam, kami menyimpan alamat IP pengirim ucapan dalam
            bentuk hash, bukan alamat aslinya.
        </li>
        <li>
            <strong>Data transaksi:</strong> nomor order, jumlah, status, dan metode pembayaran. Data kartu dan
            kredensial pembayaran diproses langsung oleh Midtrans dan tidak kami simpan.
        </li>
        <li>
            <strong>Foto tamu di hari acara:</strong> jika pemilik undangan mengaktifkan selfie check-in, petugas dapat memotret tamu
            saat tiba. Foto hanya dapat dilihat pemilik undangan dan di layar sapa acara, dan dapat dihapus kapan saja oleh pemilik
            atau atas permintaan tamu.
        </li>
        <li><strong>Data teknis:</strong> cookie sesi untuk menjaga Anda tetap masuk, serta log server untuk keamanan dan pemecahan masalah.</li>
    </ul>

    <h2>2. Tujuan penggunaan</h2>
    <ul>
        <li>Menyediakan layanan: membuat, menampilkan, dan mengelola undangan.</li>
        <li>Memproses pembayaran dan mengaktifkan undangan.</li>
        <li>Mengirim email layanan, seperti verifikasi akun, bukti pembayaran, dan pengingat masa aktif.</li>
        <li>Memberikan bantuan dan menangani pelanggaran.</li>
        <li>Menjaga keamanan layanan, mencegah penyalahgunaan, dan memenuhi kewajiban hukum.</li>
    </ul>
    <p>Kami tidak menjual data pribadi dan tidak memakainya untuk iklan pihak ketiga.</p>

    <h2>3. Data yang tampil untuk publik</h2>
    <p>
        Undangan yang aktif dapat dilihat siapa pun yang memiliki tautannya, termasuk isi yang Anda tampilkan
        seperti nama, foto, lokasi acara, nomor rekening amplop digital, dan ucapan tamu yang tidak disembunyikan.
        Anda mengatur sendiri bagian mana yang ditampilkan di editor undangan.
    </p>

    <h2>4. Pihak yang menerima data</h2>
    <ul>
        <li><strong>Midtrans</strong>, untuk memproses pembayaran (nomor order, jumlah, serta nama, email, dan nomor WhatsApp pembeli bila diisi).</li>
        <li><strong>Penyedia server dan penyedia email</strong>, yang menyimpan data atau mengirim email atas nama kami.</li>
        <li><strong>Pihak berwenang</strong>, bila diwajibkan oleh hukum.</li>
    </ul>

    <h2>5. Akses oleh admin</h2>
    <p>
        Admin kami dapat membuka dan mengubah isi undangan untuk memberikan bantuan atau menangani pelanggaran.
        Setiap akses dan perubahan oleh admin dicatat dalam log audit.
    </p>

    <h2>6. Penyimpanan dan keamanan</h2>
    <ul>
        <li>Data dikirim melalui koneksi terenkripsi (HTTPS).</li>
        <li>
            Data undangan tetap disimpan setelah masa aktif berakhir agar undangan dapat diperpanjang. Anda dapat
            meminta penghapusan kapan saja melalui kontak di bawah.
        </li>
        <li>Data transaksi disimpan sesuai kewajiban pencatatan keuangan yang berlaku.</li>
    </ul>

    <h2>7. Hak Anda</h2>
    <ul>
        <li>Mengakses dan memperbarui data. Sebagian besar data dapat Anda ubah sendiri di editor undangan.</li>
        <li>Meminta salinan data pribadi Anda.</li>
        <li>Meminta penghapusan akun dan data undangan.</li>
        <li>Menarik persetujuan pemrosesan data, dengan konsekuensi layanan tidak dapat lagi digunakan.</li>
    </ul>
    <p>Permintaan diproses paling lambat 14 hari kerja setelah identitas Anda kami verifikasi.</p>

    <h2>8. Data tamu</h2>
    <p>
        Saat Anda memasukkan nama tamu, Anda bertindak sebagai pihak yang menentukan tujuan pemrosesan data
        tersebut. Gunakan data tamu hanya untuk keperluan undangan Anda.
    </p>

    <h2>9. Perubahan kebijakan</h2>
    <p>Perubahan kebijakan ini akan dipublikasikan di halaman ini, dengan tanggal pembaruan di bagian atas.</p>

    <h2>10. Kontak</h2>
    @include('legal.partials.contact')
</x-legal-page>
