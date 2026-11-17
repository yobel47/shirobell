@props(['title' => null, 'wide' => false, 'fonts' => ['inter', 'cormorant-garamond']])
<x-layouts.base :title="$title" :fonts="$fonts" robots="noindex, nofollow">
    @isset($headStart)
        <x-slot:headStart>{{ $headStart }}</x-slot:headStart>
    @endisset
    <header x-data="{ open: false }" class="sticky top-0 z-40 border-b border-rose-brand-900/8 bg-cream/85 backdrop-blur-md">
        {{-- Same width as <main>, so the logo lines up with the page title on wide pages. --}}
        <nav @class(["mx-auto flex items-center justify-between px-4 py-3 sm:px-6", "max-w-[88rem]" => $wide, "max-w-6xl" => ! $wide])>
            <div class="flex items-center gap-10">
                <a href="{{ auth()->user()->isAdmin() ? url('/admin') : route('app.dashboard') }}" aria-label="{{ $siteName }}"><x-brand.logo :name="$siteName" size="sm"/></a>
                @if (auth()->user()->isAdmin())
                    <a href="{{ url('/admin') }}" class="nav-link hidden md:inline-flex">Panel Admin</a>
                @else
                <div class="hidden items-center gap-8 md:flex">
                    <a href="{{ route('app.dashboard') }}" @class(['nav-link', 'is-current' => request()->routeIs('app.dashboard', 'app.invitations.*')])>Undangan Saya</a>
                    <a href="{{ route('app.orders.index') }}" @class(['nav-link', 'is-current' => request()->routeIs('app.orders.*', 'app.checkout')])>Transaksi</a>
                </div>
                @endif
            </div>

            <div class="relative" x-on:click.outside="open = false">
                <button type="button" class="flex items-center gap-2 rounded-full border border-rose-brand-900/10 bg-white/70 py-1 pr-3 pl-1 transition hover:border-rose-brand-300" x-on:click="open = !open" x-bind:aria-expanded="open.toString()">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-rose-brand-500 font-display text-lg leading-none text-white">
                        {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <span class="hidden max-w-40 truncate text-sm font-medium sm:block">{{ auth()->user()->name }}</span>
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                </button>

                <div x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-60 origin-top-right rounded-2xl border border-rose-brand-900/8 bg-white p-1.5 shadow-[0_24px_48px_-20px_rgb(89_52_59/0.35)]">
                    <div class="border-b border-gray-100 px-3 py-2">
                        <p class="truncate text-sm font-medium">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs text-gray-500">{{ auth()->user()->email }}</p>
                    </div>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ url('/admin') }}" class="mt-1 block rounded-lg px-3 py-2 text-sm hover:bg-rose-brand-50">Panel Admin</a>
                    @else
                        <a href="{{ route('app.dashboard') }}" class="mt-1 block rounded-lg px-3 py-2 text-sm hover:bg-rose-brand-50 md:hidden">Undangan Saya</a>
                        <a href="{{ route('app.orders.index') }}" class="block rounded-lg px-3 py-2 text-sm hover:bg-rose-brand-50 md:hidden">Transaksi</a>
                    @endif
                    @unless (auth()->user()->isAdmin())
                        <a href="{{ route('app.profile') }}" class="block rounded-lg px-3 py-2 text-sm hover:bg-rose-brand-50">Profil &amp; kata sandi</a>
                    @endunless
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50">Keluar</button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    <main @class(["mx-auto px-4 py-8 sm:px-6 sm:py-14", "max-w-[88rem]" => $wide, "max-w-6xl" => ! $wide])>
        @if (session('status'))
            @php
                $statusMessages = [
                    'profile-information-updated' => 'Profil berhasil diperbarui.',
                    'password-updated' => 'Kata sandi berhasil diubah.',
                    'verification-link-sent' => 'Tautan verifikasi baru sudah dikirim ke email Anda.',
                ];
            @endphp
            <div class="mb-8 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>{{ $statusMessages[session('status')] ?? session('status') }}</div>
        @endif

        {{ $slot }}
    </main>

    <div x-data="{ show: false, message: '', timer: null }"
         x-on:notify.window="message = $event.detail.message; show = true; clearTimeout(timer); timer = setTimeout(() => show = false, 2500)"
         x-show="show" x-cloak x-transition
         role="status" aria-live="polite"
         class="fixed inset-x-0 bottom-20 z-50 mx-auto w-fit max-w-[90vw] rounded-full bg-wine px-5 py-3 text-sm text-cream shadow-[0_20px_40px_-16px_rgb(46_28_33/0.6)] sm:bottom-6">
        <span x-text="message"></span>
    </div>
</x-layouts.base>
