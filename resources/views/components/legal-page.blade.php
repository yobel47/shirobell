@props(['title', 'legal', 'description' => null])
<x-layouts.public :title="$title" :description="$description">
    <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 sm:py-16">
        <nav class="mb-8 flex flex-wrap gap-x-4 gap-y-2 text-sm" aria-label="Halaman informasi">
            @foreach ([
                'legal.terms' => 'Syarat & Ketentuan',
                'legal.privacy' => 'Kebijakan Privasi',
                'legal.refund' => 'Kebijakan Refund',
                'legal.contact' => 'Kontak',
            ] as $name => $label)
                <a href="{{ route($name) }}" @class([
                    'font-semibold text-rose-brand-600' => request()->routeIs($name),
                    'text-gray-500 hover:text-rose-brand-600' => ! request()->routeIs($name),
                ]) @if (request()->routeIs($name)) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
        </nav>

        <h1 class="font-serif text-3xl font-semibold sm:text-4xl">{{ $title }}</h1>
        <p class="mt-2 text-sm text-gray-500">Terakhir diperbarui {{ $legal['updated_at'] }}</p>

        <div class="legal mt-8">
            {{ $slot }}
        </div>
    </div>
</x-layouts.public>
