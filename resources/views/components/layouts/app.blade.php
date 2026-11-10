@props(['title' => null])
<x-layouts.base :title="$title">
    <header x-data="{ open: false }" class="sticky top-0 z-40 border-b border-rose-brand-100 bg-white">
        <nav class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:px-6">
            <div class="flex items-center gap-6">
                <a href="{{ auth()->user()->isAdmin() ? url('/admin') : route('app.dashboard') }}" class="font-serif text-2xl font-semibold text-rose-brand-600">{{ $siteName }}</a>
                @if (auth()->user()->isAdmin())
                    <a href="{{ url('/admin') }}" class="btn btn-ghost hidden md:inline-flex">Panel Admin</a>
                @else
                <div class="hidden items-center gap-1 md:flex">
                    <a href="{{ route('app.dashboard') }}" @class(['btn btn-ghost', 'bg-rose-brand-50 text-rose-brand-700' => request()->routeIs('app.dashboard')])>Undangan Saya</a>
                    <a href="{{ route('app.orders.index') }}" @class(['btn btn-ghost', 'bg-rose-brand-50 text-rose-brand-700' => request()->routeIs('app.orders.*')])>Transaksi</a>
                </div>
                @endif
            </div>

            <div class="relative" x-on:click.outside="open = false">
                <button type="button" class="flex items-center gap-2 rounded-full py-1 pr-2 pl-1 hover:bg-rose-brand-50" x-on:click="open = !open">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-rose-brand-100 text-sm font-semibold text-rose-brand-700">
                        {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <span class="hidden max-w-40 truncate text-sm font-medium sm:block">{{ auth()->user()->name }}</span>
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                </button>

                <div x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-56 rounded-xl border border-rose-brand-100 bg-white p-1.5 shadow-lg">
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
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50">Keluar</button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-6 sm:px-6 sm:py-10">
        @if (session('status'))
            <div class="mb-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
        @endif

        {{ $slot }}
    </main>

    <div x-data="{ show: false, message: '', timer: null }"
         x-on:notify.window="message = $event.detail.message; show = true; clearTimeout(timer); timer = setTimeout(() => show = false, 2500)"
         x-show="show" x-cloak x-transition
         role="status" aria-live="polite"
         class="fixed inset-x-0 bottom-20 z-50 mx-auto w-fit max-w-[90vw] rounded-full bg-ink px-5 py-2.5 text-sm text-white shadow-lg sm:bottom-6">
        <span x-text="message"></span>
    </div>
</x-layouts.base>
