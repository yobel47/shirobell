@props(['title' => null, 'description' => null])
<x-layouts.base :title="$title" :description="$description">
    <header x-data="{ open: false }" class="sticky top-0 z-40 border-b border-rose-brand-100/70 bg-cream/90 backdrop-blur">
        <nav class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:px-6">
            <a href="{{ route('home') }}" class="font-serif text-2xl font-semibold text-rose-brand-600">{{ $siteName }}</a>

            <div class="hidden items-center gap-1 md:flex">
                <a href="{{ route('home') }}#tema" class="btn btn-ghost">Tema</a>
                <a href="{{ route('home') }}#fitur" class="btn btn-ghost">Fitur</a>
                <a href="{{ route('home') }}#harga" class="btn btn-ghost">Harga</a>
                <a href="{{ route('home') }}#faq" class="btn btn-ghost">FAQ</a>
            </div>

            <div class="hidden items-center gap-2 md:flex">
                @auth
                    <a href="{{ route('app.dashboard') }}" class="btn btn-primary">Undangan Saya</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Buat Undangan</a>
                @endauth
            </div>

            <button type="button" class="btn btn-ghost px-3 md:hidden" x-on:click="open = !open" aria-label="Buka menu">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
        </nav>

        <div x-show="open" x-cloak x-transition class="border-t border-rose-brand-100 bg-cream px-4 pb-4 md:hidden">
            <div class="flex flex-col py-2">
                <a href="{{ route('home') }}#tema" class="rounded-lg px-3 py-2.5" x-on:click="open = false">Tema</a>
                <a href="{{ route('home') }}#fitur" class="rounded-lg px-3 py-2.5" x-on:click="open = false">Fitur</a>
                <a href="{{ route('home') }}#harga" class="rounded-lg px-3 py-2.5" x-on:click="open = false">Harga</a>
                <a href="{{ route('home') }}#faq" class="rounded-lg px-3 py-2.5" x-on:click="open = false">FAQ</a>
            </div>
            @auth
                <a href="{{ route('app.dashboard') }}" class="btn btn-primary w-full">Undangan Saya</a>
            @else
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('login') }}" class="btn btn-outline">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Buat Undangan</a>
                </div>
            @endauth
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

    <footer class="border-t border-rose-brand-100 bg-white">
        <div class="mx-auto flex max-w-6xl flex-col gap-4 px-4 py-8 text-sm text-gray-500 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <p><span class="font-serif text-lg text-rose-brand-600">{{ $siteName }}</span> &middot; Undangan pernikahan online</p>
            <nav class="flex flex-wrap gap-x-4 gap-y-2" aria-label="Informasi">
                <a href="{{ route('legal.terms') }}" class="hover:text-rose-brand-600">Syarat & Ketentuan</a>
                <a href="{{ route('legal.privacy') }}" class="hover:text-rose-brand-600">Kebijakan Privasi</a>
                <a href="{{ route('legal.refund') }}" class="hover:text-rose-brand-600">Kebijakan Refund</a>
                <a href="{{ route('legal.contact') }}" class="hover:text-rose-brand-600">Kontak</a>
            </nav>
        </div>
        <p class="border-t border-rose-brand-50 px-4 py-4 text-center text-xs text-gray-400">&copy; {{ now()->year }} {{ $siteName }}. Hak cipta dilindungi.</p>
    </footer>
</x-layouts.base>
