<x-layouts.app title="Riwayat Transaksi">
    <h1 class="font-serif text-3xl font-semibold">Riwayat Transaksi</h1>

    <div class="mt-6 space-y-3">
        @forelse ($orders as $order)
            <a href="{{ route('app.orders.show', $order) }}" class="card flex flex-col gap-2 p-4 transition hover:border-rose-brand-300 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                    <p class="font-semibold">{{ $order->invitation?->coupleName() ?? 'Undangan dihapus' }}</p>
                    <p class="text-xs text-gray-500"><span class="font-mono">{{ $order->number }}</span> &middot; {{ $order->typeLabel() }} &middot; {{ $order->created_at->translatedFormat('d M Y, H:i') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="font-semibold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span>
                </div>
            </a>
        @empty
            <div class="card py-10 text-center text-sm text-gray-500">Belum ada transaksi.</div>
        @endforelse

        {{ $orders->links() }}
    </div>
</x-layouts.app>
