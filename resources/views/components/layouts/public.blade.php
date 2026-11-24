@props(['title' => null, 'description' => null, 'canonical' => null, 'image' => null])
<x-layouts.base :title="$title" :description="$description" :canonical="$canonical" :image="$image">
    @isset($head)
        <x-slot:head>{{ $head }}</x-slot:head>
    @endisset
    <header x-data="{ open: false }" class="vt-header sticky top-0 z-40 border-b border-rose-brand-900/8 bg-cream/85 backdrop-blur-md">
        <nav class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3.5 sm:px-6">
            <a href="{{ route('home') }}" aria-label="{{ $siteName }}"><x-brand.logo :name="$siteName"/></a>

            <div class="hidden items-center gap-8 md:flex">
                @foreach ([
                    [route('themes.index'), 'Tema', request()->routeIs('themes.*')],
                    [route('home').'#fitur', 'Fitur', false],
                    [route('home').'#harga', 'Harga', false],
                    [route('home').'#faq', 'FAQ', false],
                ] as [$href, $label, $current])
                    <a href="{{ $href }}" @class(['nav-link', 'is-current' => $current]) @if ($current) aria-current="page" @endif>{{ $label }}</a>
                @endforeach
            </div>

            <div class="hidden items-center gap-2 md:flex">
                @auth
                    <a href="{{ route('app.dashboard') }}" class="btn btn-primary">Undangan Saya</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Buat Undangan</a>
                @endauth
            </div>

            <button type="button" class="-mr-2 flex h-11 w-11 items-center justify-center rounded-full text-ink md:hidden" x-on:click="open = !open" x-bind:aria-expanded="open.toString()" aria-label="Buka menu">
                <span class="relative block h-3 w-5" aria-hidden="true">
                    <span class="absolute inset-x-0 top-0 h-px bg-current transition duration-300" x-bind:class="open && 'translate-y-[5.5px] rotate-45'"></span>
                    <span class="absolute inset-x-0 bottom-0 h-px bg-current transition duration-300" x-bind:class="open && '-translate-y-[5.5px] -rotate-45'"></span>
                </span>
            </button>
        </nav>

        <div x-show="open" x-cloak x-transition.opacity class="border-t border-rose-brand-900/8 bg-cream px-4 pt-2 pb-6 md:hidden">
            <div class="flex flex-col">
                @foreach ([[route('themes.index'), 'Tema'], [route('home').'#fitur', 'Fitur'], [route('home').'#harga', 'Harga'], [route('home').'#faq', 'FAQ']] as [$href, $label])
                    <a href="{{ $href }}" class="border-b border-rose-brand-900/8 py-4 font-display text-3xl text-ink" x-on:click="open = false">{{ $label }}</a>
                @endforeach
            </div>
            @auth
                <a href="{{ route('app.dashboard') }}" class="btn btn-primary btn-lg mt-6 w-full">Undangan Saya</a>
            @else
                <div class="mt-6 grid grid-cols-2 gap-2">
                    <a href="{{ route('login') }}" class="btn btn-outline btn-lg">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Buat Undangan</a>
                </div>
            @endauth
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

    <footer class="bg-wine text-cream">
        <div class="mx-auto max-w-6xl px-4 pt-16 pb-10 sm:px-6 sm:pt-20">
            <div class="grid gap-12 lg:grid-cols-12">
                <div class="lg:col-span-6">
                    <x-brand.mark class="h-9 w-9 text-rose-brand-300"/>
                    <p class="display mt-6 text-[clamp(2.25rem,4.5vw,3.75rem)] text-cream">Kabar bahagia, <em class="text-rose-brand-300">dibagikan dengan indah.</em></p>
                    <a href="{{ route('register') }}" class="btn btn-light btn-arrow mt-8">
                        Buat undangan gratis
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
                    </a>
                </div>

                <nav class="grid grid-cols-2 gap-8 text-sm lg:col-span-5 lg:col-start-8" aria-label="Informasi">
                    <div>
                        <p class="text-xs font-semibold tracking-[0.12em] text-cream/50 uppercase">Produk</p>
                        <ul class="mt-4 space-y-3">
                            <li><a href="{{ route('themes.index') }}" class="footer-link">Katalog tema</a></li>
                            <li><a href="{{ route('home') }}#fitur" class="footer-link">Fitur</a></li>
                            <li><a href="{{ route('home') }}#harga" class="footer-link">Harga</a></li>
                            <li><a href="{{ route('home') }}#faq" class="footer-link">FAQ</a></li>
                        </ul>
                    </div>
                    <div>
                        <p class="text-xs font-semibold tracking-[0.12em] text-cream/50 uppercase">Informasi</p>
                        <ul class="mt-4 space-y-3">
                            <li><a href="{{ route('legal.terms') }}" class="footer-link">Syarat &amp; Ketentuan</a></li>
                            <li><a href="{{ route('legal.privacy') }}" class="footer-link">Kebijakan Privasi</a></li>
                            <li><a href="{{ route('legal.refund') }}" class="footer-link">Kebijakan Refund</a></li>
                            <li><a href="{{ route('legal.contact') }}" class="footer-link">Kontak</a></li>
                        </ul>
                    </div>
                </nav>
            </div>

            @if ($supportContacts['whatsapp_url'] || $supportContacts['instagram_url'] || $supportContacts['email'])
                <div class="mt-14 flex flex-wrap items-center gap-x-6 gap-y-3 border-t border-cream/10 pt-8 text-sm">
                    <span class="text-cream/50">Hubungi kami</span>
                    @if ($supportContacts['whatsapp_url'])
                        <a href="{{ $supportContacts['whatsapp_url'] }}" target="_blank" rel="noopener" class="footer-link inline-flex items-center gap-2"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm0 18.2c-1.5 0-3-.4-4.3-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.2-.4.2-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2 .7 2.8.6.4-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.3-.2-.5-.3Z"/></svg> {{ $supportContacts['whatsapp'] }}</a>
                    @endif
                    @if ($supportContacts['instagram_url'])
                        <a href="{{ $supportContacts['instagram_url'] }}" target="_blank" rel="noopener" class="footer-link inline-flex items-center gap-2"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg> {{ '@'.$supportContacts['instagram'] }}</a>
                    @endif
                    @if ($supportContacts['email'])
                        <a href="mailto:{{ $supportContacts['email'] }}" class="footer-link inline-flex items-center gap-2"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg> {{ $supportContacts['email'] }}</a>
                    @endif
                </div>
            @endif

            <div @class(["mt-8 flex flex-col gap-3 border-t border-cream/10 pt-6 text-xs text-cream/45 sm:flex-row sm:items-center sm:justify-between", "pb-16 sm:pb-0" => $supportContacts['whatsapp_url']])>
                <p>{{ $siteName }} &middot; Undangan pernikahan online</p>
                <p>&copy; {{ now()->year }} {{ $siteName }}. Hak cipta dilindungi.</p>
            </div>
        </div>
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
