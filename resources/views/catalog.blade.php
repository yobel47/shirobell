@php
    $categoryLabel = $category ? $categories[$category] : null;
    $seoTitle = $categoryLabel ? 'Tema Undangan Pernikahan '.$categoryLabel : 'Katalog Tema Undangan Pernikahan Online';
    $seoDescription = $categoryLabel
        ? $themes->count().' tema undangan pernikahan online kategori '.$categoryLabel.', lengkap dengan pilihan warna. Lihat demo dan buat undanganmu sendiri.'
        : 'Pilih dari '.$total.' tema undangan pernikahan online: minimalis, floral, adat, luxury, islami, dan lainnya. Setiap tema punya beberapa pilihan warna.';
@endphp
<x-layouts.public :title="$seoTitle" :description="$seoDescription" :canonical="$category ? route('themes.index', ['kategori' => $category]) : route('themes.index')">
    <section class="mx-auto max-w-6xl px-4 pt-14 pb-20 sm:px-6 sm:pt-20">
        <div class="grid gap-6 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-8">
                <p class="eyebrow">Katalog &middot; {{ $total }} tema</p>
                <h1 class="display mt-6 text-[clamp(2.75rem,6vw,5.5rem)] text-ink">
                    @if ($categoryLabel)
                        Tema undangan <em>{{ $categoryLabel }}</em>
                    @else
                        Katalog tema <em>undangan</em>
                    @endif
                </h1>
            </div>
            <p class="max-w-md text-gray-600 lg:col-span-4 lg:justify-self-end">Setiap tema punya beberapa pilihan warna. Semua termasuk dalam paket dan bisa diganti kapan saja tanpa mengisi ulang data.</p>
        </div>

        {{-- Category tabs: underline marks the current one --}}
        <nav class="-mx-4 mt-12 overflow-x-auto border-b border-rose-brand-900/10 px-4 sm:mx-0 sm:px-0" aria-label="Kategori tema">
            <ul class="-mb-px flex gap-7">
                <li>
                    <a href="{{ route('themes.index') }}" @class([
                        'flex items-baseline gap-1.5 border-b-2 py-4 text-sm font-medium whitespace-nowrap transition-colors',
                        'border-rose-brand-500 text-ink' => $category === null,
                        'border-transparent text-gray-500 hover:text-ink' => $category !== null,
                    ]) @if ($category === null) aria-current="page" @endif>Semua <span class="font-display text-base text-rose-brand-500">{{ $total }}</span></a>
                </li>
                @foreach ($categories as $key => $label)
                    @continue(! $counts->get($key))
                    <li>
                        <a href="{{ route('themes.index', ['kategori' => $key]) }}" @class([
                            'flex items-baseline gap-1.5 border-b-2 py-4 text-sm font-medium whitespace-nowrap transition-colors',
                            'border-rose-brand-500 text-ink' => $category === $key,
                            'border-transparent text-gray-500 hover:text-ink' => $category !== $key,
                        ]) @if ($category === $key) aria-current="page" @endif>{{ $label }} <span class="font-display text-base text-rose-brand-500">{{ $counts->get($key) }}</span></a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="mt-10 grid gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse ($themes as $theme)
                <div data-reveal style="--d: {{ ($loop->index % 4) * 60 }}ms"><x-theme-card :theme="$theme" /></div>
            @empty
                <p class="col-span-full py-16 text-center font-serif text-2xl text-gray-500">Belum ada tema di kategori ini.</p>
            @endforelse
        </div>
    </section>

    <section class="bg-rose-brand-50">
        <div class="mx-auto flex max-w-6xl flex-col gap-8 px-4 py-16 sm:px-6 md:flex-row md:items-center md:justify-between">
            <p class="display max-w-xl text-[clamp(2.25rem,4vw,3.5rem)] text-ink">Sudah menemukan <em>yang cocok?</em></p>
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg btn-arrow shrink-0">
                Buat undangan sekarang
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
            </a>
        </div>
    </section>
</x-layouts.public>
