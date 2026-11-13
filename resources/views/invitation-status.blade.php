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
    <main class="flex min-h-screen flex-col items-center justify-center px-4 py-16 text-center">
        <p class="font-serif text-2xl text-rose-brand-500 italic">{{ $siteName ?? config('app.name') }}</p>
        <h1 class="mt-6 font-serif text-3xl font-semibold">{{ $heading }}</h1>
        <p class="mt-3 max-w-md text-gray-500">{{ $body }}</p>
        <a href="{{ config('app.url') }}" class="btn btn-outline mt-8">Buat undangan Anda sendiri</a>
    </main>
</x-layouts.base>
