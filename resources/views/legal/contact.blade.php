<x-legal-page title="Kontak" :legal="$legal" description="Hubungi tim {{ $legal['site'] }} untuk bantuan undangan, pembayaran, atau pertanyaan lain.">
    <p>
        Ada pertanyaan tentang undangan, pembayaran, atau butuh bantuan mengisi undangan? Hubungi kami melalui
        salah satu kontak di bawah. Untuk masalah pembayaran, sertakan email akun dan nomor order agar kami bisa
        membantu lebih cepat.
    </p>

    <div class="grid gap-4 sm:grid-cols-2">
        @if ($legal['whatsapp_url'])
            <a href="{{ $legal['whatsapp_url'] }}" target="_blank" rel="noopener" class="card block no-underline hover:border-rose-brand-300">
                <span class="block text-sm text-gray-500">WhatsApp</span>
                <span class="mt-1 block text-lg font-semibold text-ink">{{ $legal['whatsapp'] }}</span>
            </a>
        @endif
        @if ($legal['email'])
            <a href="mailto:{{ $legal['email'] }}" class="card block no-underline hover:border-rose-brand-300">
                <span class="block text-sm text-gray-500">Email</span>
                <span class="mt-1 block text-lg font-semibold break-all text-ink">{{ $legal['email'] }}</span>
            </a>
        @endif
    </div>

    @if (! $legal['email'] && ! $legal['whatsapp_url'])
        <p>Kontak support belum tersedia. Silakan kembali lagi nanti.</p>
    @endif

    <h2>Pengelola</h2>
    <p>
        <strong>{{ $legal['business'] }}</strong>
        @if ($legal['address'])
            <br><span class="whitespace-pre-line">{{ $legal['address'] }}</span>
        @endif
    </p>

    <h2>Informasi lain</h2>
    <ul>
        <li><a href="{{ route('legal.terms') }}">Syarat & Ketentuan</a></li>
        <li><a href="{{ route('legal.privacy') }}">Kebijakan Privasi</a></li>
        <li><a href="{{ route('legal.refund') }}">Kebijakan Refund</a></li>
        <li><a href="{{ route('home') }}#faq">Pertanyaan yang sering diajukan</a></li>
    </ul>
</x-legal-page>
