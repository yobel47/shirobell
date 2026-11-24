@php
    $messages = [
        'not-found' => ['Undangan tidak ditemukan', 'Alamat undangan ini tidak terdaftar. Periksa kembali tautan yang Anda terima.'],
        'draft' => ['Undangan belum diterbitkan', 'Undangan ini sedang disiapkan oleh pemiliknya. Silakan kembali lagi nanti.'],
        'expired' => ['Masa aktif undangan telah berakhir', 'Terima kasih atas perhatian Anda. Undangan ini sudah tidak ditampilkan.'],
        'suspended' => ['Undangan tidak tersedia', 'Undangan ini sedang tidak dapat ditampilkan.'],
    ];
    [$heading, $body] = $messages[$state] ?? $messages['not-found'];
@endphp
<x-layouts.base :title="$heading" robots="noindex, nofollow">
    <main class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden px-4 py-16 text-center">
        <x-brand.mark class="pointer-events-none absolute -bottom-40 left-1/2 h-[36rem] w-[36rem] -translate-x-1/2 text-rose-brand-100/60"/>
        <div class="relative flex flex-col items-center">
            <x-brand.logo :name="$siteName ?? null"/>
            <div class="my-10 h-px w-16 bg-rose-brand-300"></div>
            <h1 class="page-title max-w-xl">{{ $heading }}</h1>
            <p class="mt-5 max-w-md text-gray-600">{{ $body }}</p>
            <a href="{{ config('app.url') }}" class="btn btn-outline btn-lg mt-10">Buat undangan Anda sendiri</a>
        </div>
    </main>
</x-layouts.base>
