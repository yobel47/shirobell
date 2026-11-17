<x-layouts.app title="Riwayat Transaksi">
    <p class="eyebrow">Transaksi</p>
    <h1 class="page-title mt-5">Riwayat <em>transaksi</em></h1>

    @if ($orders->isEmpty())
        <div class="mt-12 rounded-3xl bg-rose-brand-50 px-6 py-14 text-center">
            <p class="font-serif text-2xl font-medium">Belum ada transaksi.</p>
            <p class="mt-2 text-sm text-gray-600">Pembayaran undangan dan add-on akan tercatat di sini.</p>
        </div>
    @else
        {{-- Ledger: one row per order, amount set large on the right --}}
        <div class="mt-12 border-b border-rose-brand-900/8">
            @foreach ($orders as $order)
                <a href="{{ route('app.orders.show', $order) }}" class="group grid gap-3 border-t border-rose-brand-900/8 py-6 transition-colors sm:grid-cols-[1fr_auto] sm:items-center sm:gap-8">
                    <div class="min-w-0">
                        <p class="truncate font-serif text-2xl font-medium transition-colors group-hover:text-rose-brand-600">{{ $order->invitation?->coupleName() ?? 'Undangan dihapus' }}</p>
                        <p class="mt-1.5 text-xs text-gray-500"><span class="font-mono">{{ $order->number }}</span> &middot; {{ $order->typeLabel() }} &middot; {{ $order->created_at->translatedFormat('d M Y, H:i') }}</p>
                    </div>
                    <div class="flex items-center gap-4 sm:justify-end">
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span>
                        <span class="font-display text-2xl text-ink [font-variant-numeric:lining-nums]">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        <svg class="hidden h-4 w-4 text-gray-300 transition duration-300 ease-out-expo group-hover:translate-x-1 group-hover:text-rose-brand-500 sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m9 6 6 6-6 6"/></svg>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">{{ $orders->links() }}</div>
    @endif
</x-layouts.app>
