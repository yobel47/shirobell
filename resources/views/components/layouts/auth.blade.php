@props(['title' => null, 'heading' => null, 'subheading' => null])
<x-layouts.base :title="$title" robots="noindex, nofollow">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-10">
        <a href="{{ route('home') }}" class="mb-8 font-serif text-3xl font-semibold text-rose-brand-600">{{ $siteName }}</a>

        <div class="card w-full max-w-md">
            @if ($heading)
                <h1 class="font-serif text-2xl font-semibold">{{ $heading }}</h1>
            @endif
            @if ($subheading)
                <p class="mt-1 text-sm text-gray-500">{{ $subheading }}</p>
            @endif

            @if (session('status'))
                <div class="mt-4 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mt-6">
                {{ $slot }}
            </div>
        </div>

        {{ $footer ?? '' }}
    </div>
</x-layouts.base>
