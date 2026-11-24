<x-layouts.app :title="'Order '.$order->number">
    <div class="mx-auto mb-6 max-w-xl">
        <a href="{{ route('app.orders.index') }}" class="back-link">&larr; Riwayat transaksi</a>
        <h1 class="mt-4 page-title">Status pembayaran</h1>
    </div>

    <livewire:payment.order-status :order="$order" />
</x-layouts.app>
