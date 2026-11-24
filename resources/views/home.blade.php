@php
    // One source for the visible FAQ and its structured data.
    $faqs = [
        ['Apakah saya bisa mencoba dulu sebelum membayar?', 'Bisa. Daftar gratis, buat undangan, dan lihat preview-nya. Pembayaran hanya diperlukan saat Anda ingin menerbitkan undangan.'],
        ['Metode pembayaran apa saja yang tersedia?', 'QRIS, transfer bank melalui virtual account, dan e-wallet. Undangan aktif otomatis begitu pembayaran berhasil.'],
        ['Berapa lama undangan aktif?', 'Undangan aktif selama '.$durationDays.' hari sejak pembayaran, dan bisa diperpanjang kapan saja.'],
        ['Bagaimana cara kerja amplop digital?', 'Anda mengunggah QRIS atau mencantumkan rekening milik Anda sendiri. Tamu mengirim hadiah langsung ke Anda, tanpa perantara.'],
        ['Apakah bisa ganti tema setelah membayar?', 'Bisa, kapan saja dan tanpa biaya tambahan. Data undangan Anda tetap tersimpan.'],
    ];
    $settingService = app(\App\Services\SettingService::class);
    $siteName = (string) $settingService->get('site_name');
    $contacts = $settingService->supportContacts();
    $organization = array_filter([
        '@type' => 'Organization',
        '@id' => route('home').'#organization',
        'name' => $siteName,
        'url' => route('home'),
        'logo' => asset('apple-touch-icon.png'),
        'sameAs' => array_values(array_filter([$contacts['instagram_url']])) ?: null,
        'contactPoint' => ($contacts['whatsapp'] || $contacts['email']) ? array_filter([
            '@type' => 'ContactPoint',
            'contactType' => 'customer support',
            'telephone' => $contacts['whatsapp'],
            'email' => $contacts['email'],
            'availableLanguage' => ['Indonesian'],
        ]) : null,
    ]);
    $structuredData = [
        '@context' => 'https://schema.org',
        '@graph' => [
            $organization,
            ['@type' => 'WebSite', '@id' => route('home').'#website', 'name' => $siteName, 'url' => route('home'), 'inLanguage' => 'id-ID', 'publisher' => ['@id' => route('home').'#organization']],
            [
                '@type' => 'Service',
                'name' => 'Undangan pernikahan online',
                'serviceType' => 'Undangan pernikahan digital',
                'provider' => ['@id' => route('home').'#organization'],
                'areaServed' => 'ID',
                'offers' => ['@type' => 'Offer', 'price' => $price, 'priceCurrency' => 'IDR', 'url' => route('register'), 'availability' => 'https://schema.org/InStock'],
            ],
            [
                '@type' => 'FAQPage',
                'mainEntity' => array_map(fn ($f) => ['@type' => 'Question', 'name' => $f[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f[1]]], $faqs),
            ],
        ],
    ];
@endphp
<x-layouts.public title="Undangan Pernikahan Online, Buat Sendiri dalam Hitungan Menit"
                  description="Buat undangan pernikahan online sendiri dengan {{ $themes->count() }} tema elegan: galeri, RSVP, amplop digital, dan link personal untuk setiap tamu. Mulai Rp {{ number_format($price, 0, ',', '.') }}, aktif {{ $durationDays }} hari."
                  :canonical="route('home')">
    <x-slot:head>
        <script type="application/ld+json" nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">@json($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)</script>
    </x-slot:head>
    @include('home.partials.hero')
    @include('home.partials.steps')
    @include('home.partials.features')
    @include('home.partials.themes')
    @include('home.partials.pricing')
    @include('home.partials.faq')
    @include('home.partials.cta')
</x-layouts.public>
