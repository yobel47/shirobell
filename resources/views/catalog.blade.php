@php
    $categoryLabel = $category ? $categories[$category] : null;
    $seoTitle = $categoryLabel ? 'Tema Undangan Pernikahan '.$categoryLabel : 'Katalog Tema Undangan Pernikahan Online';
    $seoDescription = $categoryLabel
        ? $themes->count().' tema undangan pernikahan online kategori '.$categoryLabel.', lengkap dengan pilihan warna. Lihat demo dan buat undanganmu sendiri.'
        : 'Pilih dari '.$total.' tema undangan pernikahan online: minimalis, floral, adat, luxury, islami, dan lainnya. Setiap tema punya beberapa pilihan warna.';
@endphp
<x-layouts.public :title="$seoTitle" :description="$seoDescription" :canonical="$category ? route('themes.index', ['kategori' => $category]) : route('themes.index')">
    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 sm:py-16">
        <h1 class="font-serif text-3xl font-semibold sm:text-4xl">{{ $categoryLabel ? 'Tema undangan '.$categoryLabel : 'Katalog tema undangan' }}</h1>
        <p class="mt-2 max-w-2xl text-gray-600">{{ $total }} tema, masing-masing dengan beberapa pilihan warna. Semua tema termasuk dalam paket dan bisa diganti kapan saja tanpa mengisi ulang data.</p>

        <nav class="mt-8 flex gap-2 overflow-x-auto pb-2" aria-label="Kategori tema">
            <a href="{{ route('themes.index') }}" @class([
                'shrink-0 rounded-full px-4 py-2 text-sm font-medium',
                'bg-rose-brand-500 text-white' => $category === null,
                'bg-white text-gray-600 ring-1 ring-gray-200 hover:bg-rose-brand-50' => $category !== null,
            ])>Semua <span class="opacity-70">{{ $total }}</span></a>
            @foreach ($categories as $key => $label)
                @continue(! $counts->get($key))
                <a href="{{ route('themes.index', ['kategori' => $key]) }}" @class([
                    'shrink-0 rounded-full px-4 py-2 text-sm font-medium',
                    'bg-rose-brand-500 text-white' => $category === $key,
                    'bg-white text-gray-600 ring-1 ring-gray-200 hover:bg-rose-brand-50' => $category !== $key,
                ]) @if ($category === $key) aria-current="page" @endif>{{ $label }} <span class="opacity-70">{{ $counts->get($key) }}</span></a>
            @endforeach
        </nav>

        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse ($themes as $theme)
                <x-theme-card :theme="$theme" />
            @empty
                <p class="col-span-full text-sm text-gray-500">Belum ada tema di kategori ini.</p>
            @endforelse
        </div>

        <div class="mt-12 text-center">
            <a href="{{ route('register') }}" class="btn btn-primary px-7 py-3 text-base">Buat undangan sekarang</a>
        </div>
    </section>
</x-layouts.public>
