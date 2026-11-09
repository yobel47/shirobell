<x-layouts.app title="Undangan Saya">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="font-serif text-3xl font-semibold">Undangan Saya</h1>
            <p class="mt-1 text-sm text-gray-500">Halo, {{ auth()->user()->name }}. Kelola semua undangan Anda di sini.</p>
        </div>
    </div>

    <div class="card mt-8 flex flex-col items-center px-6 py-14 text-center">
        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-rose-brand-50 text-rose-brand-500">
            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
        <h2 class="mt-4 font-serif text-xl font-semibold">Belum ada undangan</h2>
        <p class="mt-1 max-w-sm text-sm text-gray-500">Fitur pembuatan undangan sedang disiapkan. Sebentar lagi Anda bisa memilih tema dan mulai mengisi undangan di sini.</p>
    </div>
</x-layouts.app>
