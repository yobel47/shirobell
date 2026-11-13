@props(['title' => null, 'description' => null, 'canonical' => null, 'image' => null])
<x-layouts.base :title="$title" :description="$description" :canonical="$canonical" :image="$image">
    @isset($head)
        <x-slot:head>{{ $head }}</x-slot:head>
    @endisset
    <header x-data="{ open: false }" class="sticky top-0 z-40 border-b border-rose-brand-100/70 bg-cream/90 backdrop-blur">
        <nav class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:px-6">
            <a href="{{ route('home') }}" class="font-serif text-2xl font-semibold text-rose-brand-600">{{ $siteName }}</a>

            <div class="hidden items-center gap-1 md:flex">
                <a href="{{ route('themes.index') }}" class="btn btn-ghost">Tema</a>
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
                <a href="{{ route('themes.index') }}" class="rounded-lg px-3 py-2.5">Tema</a>
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
        @if ($supportContacts['whatsapp_url'] || $supportContacts['instagram_url'] || $supportContacts['email'])
            <div class="mx-auto flex max-w-6xl flex-wrap items-center gap-x-5 gap-y-2 px-4 pb-6 text-sm text-gray-500 sm:px-6">
                <span class="font-medium text-ink">Hubungi kami</span>
                @if ($supportContacts['whatsapp_url'])
                    <a href="{{ $supportContacts['whatsapp_url'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 hover:text-rose-brand-600"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm0 18.2c-1.5 0-3-.4-4.3-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.2-.4.2-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2 .7 2.8.6.4-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.3-.2-.5-.3Z"/></svg> {{ $supportContacts['whatsapp'] }}</a>
                @endif
                @if ($supportContacts['instagram_url'])
                    <a href="{{ $supportContacts['instagram_url'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 hover:text-rose-brand-600"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg> {{ '@'.$supportContacts['instagram'] }}</a>
                @endif
                @if ($supportContacts['email'])
                    <a href="mailto:{{ $supportContacts['email'] }}" class="inline-flex items-center gap-1.5 hover:text-rose-brand-600"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg> {{ $supportContacts['email'] }}</a>
                @endif
            </div>
        @endif
        <p @class(["border-t border-rose-brand-50 px-4 pt-4 text-center text-xs text-gray-400", "pb-24 sm:pb-4" => $supportContacts['whatsapp_url'], "pb-4" => ! $supportContacts['whatsapp_url']])>&copy; {{ now()->year }} {{ $siteName }}. Hak cipta dilindungi.</p>
    </footer>

    @if ($supportContacts['whatsapp_url'])
        {{-- Floating chat button on public pages (never on guest invitations). --}}
        <a href="{{ $supportContacts['whatsapp_url'] }}?text={{ rawurlencode('Halo '.$siteName.', saya ingin bertanya tentang undangan digital.') }}" target="_blank" rel="noopener"
           class="fixed right-4 bottom-4 z-40 inline-flex items-center gap-2 rounded-full bg-white px-4 py-3 text-sm font-semibold text-ink shadow-lg ring-1 ring-black/5 transition hover:shadow-xl">
            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-[#25d366] text-white"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm0 18.2c-1.5 0-3-.4-4.3-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.2-.4.2-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2 .7 2.8.6.4-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.3-.2-.5-.3Z"/></svg></span>
            Tanya kami
        </a>
    @endif
</x-layouts.base>
