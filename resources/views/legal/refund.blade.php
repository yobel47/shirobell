<x-legal-page title="Kebijakan Refund" :legal="$legal" description="Ketentuan pengembalian dana layanan {{ $legal['site'] }}.">
    <p>
        Paket {{ $legal['site'] }} adalah produk digital yang langsung aktif setelah pembayaran dikonfirmasi.
        Karena itu, pengembalian dana hanya diberikan dalam kondisi di bawah ini.
    </p>

    <h2>1. Kondisi yang dapat dikembalikan</h2>
    <ul>
        <li><strong>Pembayaran ganda:</strong> Anda membayar lebih dari sekali untuk order yang sama.</li>
        <li>
            <strong>Undangan tidak aktif:</strong> pembayaran berhasil, tetapi undangan tidak aktif dalam 1x24 jam
            dan kami tidak dapat memperbaikinya.
        </li>
        <li>
            <strong>Gangguan berat:</strong> undangan tidak dapat diakses tamu lebih dari 3x24 jam berturut-turut
            selama masa aktif karena kesalahan di pihak kami.
        </li>
    </ul>

    <h2>2. Kondisi yang tidak dapat dikembalikan</h2>
    <ul>
        <li>Berubah pikiran setelah undangan aktif.</li>
        <li>Salah ketik atau salah isi. Isi undangan tetap bisa Anda ubah selama masa aktif.</li>
        <li>Masa aktif sudah berakhir.</li>
        <li>Undangan ditangguhkan karena melanggar <a href="{{ route('legal.terms') }}">Syarat & Ketentuan</a>.</li>
        <li>Order yang dibayar penuh dengan kode promo (total Rp 0).</li>
        <li>Masalah transaksi amplop digital antara tamu dan pemilik undangan, karena dana tersebut tidak melewati kami.</li>
    </ul>

    <h2>3. Cara mengajukan</h2>
    <ol>
        <li>Hubungi kami paling lambat <strong>7 hari</strong> setelah pembayaran melalui kontak di bawah.</li>
        <li>Sertakan email akun, nomor order (contoh: INV-20261110-AB12C), dan alasan pengajuan.</li>
        <li>Kami meninjau dan memberi jawaban paling lambat 3 hari kerja.</li>
        <li>
            Jika disetujui, dana dikembalikan paling lambat 14 hari kerja melalui metode pembayaran semula atau
            transfer bank ke rekening atas nama pemilik akun. Undangan terkait akan dinonaktifkan.
        </li>
    </ol>

    <h2>4. Kontak</h2>
    @include('legal.partials.contact')
</x-legal-page>
