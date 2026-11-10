<x-layouts.app :title="'Order '.$order->number">
    <div class="mx-auto mb-6 max-w-xl">
        <a href="{{ route('app.orders.index') }}" class="text-sm text-gray-500 hover:text-rose-brand-600">&larr; Riwayat transaksi</a>
        <h1 class="mt-1 font-serif text-3xl font-semibold">Status pembayaran</h1>
    </div>

    <livewire:payment.order-status :order="$order" />
</x-layouts.app>
