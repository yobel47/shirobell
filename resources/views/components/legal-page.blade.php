@props(['title', 'legal', 'description' => null])
{{-- Legal pages: sticky page index on the left (desktop), long-form prose on the right. --}}
<x-layouts.public :title="$title" :description="$description">
    <div class="mx-auto max-w-6xl px-4 pt-14 pb-24 sm:px-6 sm:pt-20">
        <header class="max-w-3xl">
            <p class="eyebrow">Informasi</p>
            <h1 class="display mt-6 text-[clamp(2.75rem,6vw,5rem)] text-ink">{{ $title }}</h1>
            <p class="mt-6 text-sm text-gray-500">Terakhir diperbarui {{ $legal['updated_at'] }}</p>
        </header>

        <div class="mt-14 grid gap-10 border-t border-rose-brand-900/10 pt-10 lg:grid-cols-12 lg:gap-8">
            <nav class="lg:col-span-3" aria-label="Halaman informasi">
                <ul class="-mx-4 flex gap-2 overflow-x-auto px-4 lg:sticky lg:top-28 lg:mx-0 lg:flex-col lg:gap-1 lg:px-0">
                    @foreach ([
                        'legal.terms' => 'Syarat & Ketentuan',
                        'legal.privacy' => 'Kebijakan Privasi',
                        'legal.refund' => 'Kebijakan Refund',
                        'legal.contact' => 'Kontak',
                    ] as $name => $label)
                        <li class="shrink-0">
                            <a href="{{ route($name) }}" @class([
                                'block rounded-full px-4 py-2 text-sm whitespace-nowrap transition-colors lg:rounded-none lg:border-l-2 lg:px-4 lg:py-2.5',
                                'bg-ink font-medium text-cream lg:border-rose-brand-500 lg:bg-transparent lg:text-ink' => request()->routeIs($name),
                                'bg-white text-gray-500 ring-1 ring-rose-brand-900/10 hover:text-ink lg:border-transparent lg:bg-transparent lg:ring-0' => ! request()->routeIs($name),
                            ]) @if (request()->routeIs($name)) aria-current="page" @endif>{{ $label }}</a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <article class="legal max-w-2xl lg:col-span-8 lg:col-start-5">
                {{ $slot }}
            </article>
        </div>
    </div>
</x-layouts.public>
