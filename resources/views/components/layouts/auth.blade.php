@props(['title' => null, 'heading' => null, 'subheading' => null])
{{-- Split screen: brand panel (desktop only) + form column. --}}
<x-layouts.base :title="$title" robots="noindex, nofollow">
    <div class="grid min-h-screen lg:grid-cols-[minmax(0,5fr)_minmax(0,6fr)]">
        <aside class="relative hidden overflow-hidden bg-wine p-12 text-cream lg:flex lg:flex-col lg:justify-between" aria-hidden="true">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(60%_50%_at_70%_40%,rgb(183_110_121/0.28),transparent_70%)]"></div>

            <a href="{{ route('home') }}" class="relative w-fit" tabindex="-1"><x-brand.logo :name="$siteName" class="[&>span:last-child]:text-cream!"/></a>

            {{-- A closed invitation cover, the moment before it is opened --}}
            <div class="relative mx-auto w-64 rotate-[-4deg] rounded-3xl border border-cream/15 bg-cream/[0.04] p-2 shadow-[0_40px_80px_-30px_rgb(0_0_0/0.6)] xl:w-72">
                <div class="flex aspect-[3/4] flex-col items-center justify-center rounded-[1.35rem] bg-gradient-to-b from-rose-brand-50 via-cream to-rose-brand-100 px-6 text-center text-ink">
                    <x-brand.mark class="h-6 w-6 text-rose-brand-400"/>
                    <p class="mt-4 text-[9px] tracking-[0.3em] text-rose-brand-600 uppercase">The Wedding of</p>
                    <p class="mt-3 font-display text-4xl leading-none italic">Raka</p>
                    <p class="font-display text-xl text-rose-brand-400">&amp;</p>
                    <p class="font-display text-4xl leading-none italic">Nadia</p>
                    <div class="my-4 h-px w-12 bg-rose-brand-300"></div>
                    <p class="text-[10px] text-gray-500">Kepada Yth.</p>
                    <p class="text-xs font-medium">Bapak/Ibu/Saudara/i</p>
                </div>
            </div>

            <p class="display relative max-w-md text-[clamp(2.25rem,3.2vw,3.25rem)] text-cream">Setiap kabar bahagia <em class="text-rose-brand-300">layak dibuka dengan indah.</em></p>
        </aside>

        <main class="flex flex-col px-4 py-8 sm:px-10 lg:px-16 xl:px-24">
            <a href="{{ route('home') }}" class="w-fit lg:hidden" aria-label="{{ $siteName }}"><x-brand.logo :name="$siteName"/></a>

            <div class="mx-auto flex w-full max-w-md flex-1 flex-col justify-center py-12">
                @if ($heading)
                    <h1 class="page-title">{{ $heading }}</h1>
                @endif
                @if ($subheading)
                    <p class="mt-4 text-gray-600">{{ $subheading }}</p>
                @endif

                @if (session('status'))
                    <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="mt-10">
                    {{ $slot }}
                </div>

                {{ $footer ?? '' }}
            </div>

            <p class="mx-auto w-full max-w-md text-xs text-gray-400">&copy; {{ now()->year }} {{ $siteName }}</p>
        </main>
    </div>
</x-layouts.base>
