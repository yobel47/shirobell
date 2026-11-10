@use('App\Enums\OrderStatus')
@php $rp = fn (int $n) => 'Rp '.number_format($n, 0, ',', '.'); @endphp
<div class="mx-auto max-w-xl space-y-6" @if ($order->isPending()) wire:poll.10s="refreshFromDatabase" @endif>
    <section class="card text-center">
        @switch($order->status)
            @case(OrderStatus::Paid)
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                </div>
                <h2 class="mt-4 font-serif text-2xl font-semibold">Pembayaran berhasil</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Undangan {{ $invitation->coupleName() }} aktif sampai {{ $invitation->active_until?->translatedFormat('d F Y') }}.
                </p>
                <div class="mt-6 flex flex-wrap justify-center gap-2">
                    <a href="{{ route('app.invitations.guests', $invitation) }}" class="btn btn-primary">Bagikan ke tamu</a>
                    <a href="{{ $invitation->publicUrl() }}" target="_blank" rel="noopener" class="btn btn-outline">Buka undangan</a>
                </div>
                @break
            @case(OrderStatus::Pending)
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-amber-50 text-amber-600">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <h2 class="mt-4 font-serif text-2xl font-semibold">Menunggu pembayaran</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Selesaikan pembayaran sebelum {{ $order->expires_at?->translatedFormat('d F Y, H:i') }}. Halaman ini diperbarui otomatis setelah pembayaran diterima.
                </p>
                <div class="mt-6 flex flex-wrap justify-center gap-2">
                    @if ($order->canContinuePayment())
                        <a href="{{ $order->payment_url }}" class="btn btn-primary">Lanjutkan pembayaran</a>
                    @endif
                    <button type="button" wire:click="check" class="btn btn-outline" wire:loading.attr="disabled" wire:target="check">
                        <span wire:loading.remove wire:target="check">Cek status pembayaran</span>
                        <span wire:loading wire:target="check">Mengecek...</span>
                    </button>
                </div>
                @break
            @default
                <h2 class="font-serif text-2xl font-semibold">{{ $order->status->label() }}</h2>
                <p class="mt-1 text-sm text-gray-500">Pembayaran tidak diterima. Silakan buat pembayaran baru.</p>
                @unless ($invitation->trashed())
                    <a href="{{ route('app.checkout', $invitation) }}" class="btn btn-primary mt-6">Bayar ulang</a>
                @endunless
        @endswitch

        @if ($notice)
            <p class="mt-4 text-sm text-gray-500" role="status">{{ $notice }}</p>
        @endif
    </section>

    <section class="card">
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-gray-500">Nomor order</dt><dd class="font-mono">{{ $order->number }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Jenis</dt><dd>{{ $order->typeLabel() }} {{ $order->duration_days }} hari</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Harga</dt><dd>{{ $rp($order->base_amount) }}</dd></div>
            @if ($order->discount_amount)
                <div class="flex justify-between text-emerald-700"><dt>Diskon</dt><dd>- {{ $rp($order->discount_amount) }}</dd></div>
            @endif
            <div class="flex justify-between font-semibold"><dt>Total</dt><dd>{{ $rp($order->total_amount) }}</dd></div>
            @if ($order->payment_method)
                <div class="flex justify-between"><dt class="text-gray-500">Metode</dt><dd>{{ str_replace('_', ' ', $order->payment_method) }}{{ $order->payment_channel ? ' ('.strtoupper($order->payment_channel).')' : '' }}</dd></div>
            @endif
            <div class="flex justify-between"><dt class="text-gray-500">Status</dt><dd><span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span></dd></div>
        </dl>
    </section>
</div>
