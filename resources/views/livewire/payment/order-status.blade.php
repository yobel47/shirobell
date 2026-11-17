@use('App\Enums\OrderStatus')
@php $rp = fn (int $n) => 'Rp '.number_format($n, 0, ',', '.'); @endphp
<div class="mx-auto max-w-xl space-y-6" @if ($order->isPending()) wire:poll.10s="refreshFromDatabase" @endif>
    <section class="card px-6 py-10 text-center sm:px-10">
        @switch($order->status)
            @case(OrderStatus::Paid)
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-rose-brand-50 text-rose-brand-500">
                    <x-brand.mark class="bell-auto h-10 w-10"/>
                </div>
                <h2 class="mt-6 font-serif text-4xl font-medium">Pembayaran berhasil</h2>
                <p class="mx-auto mt-3 max-w-sm text-sm text-gray-600">
                    Undangan {{ $invitation->coupleName() }} aktif sampai {{ $invitation->active_until?->translatedFormat('d F Y') }}.
                </p>
                <div class="mt-8 flex flex-wrap justify-center gap-2">
                    <a href="{{ route('app.invitations.guests', $invitation) }}" class="btn btn-primary">Bagikan ke tamu</a>
                    <a href="{{ $invitation->publicUrl() }}" target="_blank" rel="noopener" class="btn btn-outline">Buka undangan</a>
                </div>
                @break
            @case(OrderStatus::Pending)
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-amber-50 text-amber-600">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <h2 class="mt-6 font-serif text-4xl font-medium">Menunggu pembayaran</h2>
                <p class="mx-auto mt-3 max-w-sm text-sm text-gray-600">
                    Selesaikan pembayaran sebelum {{ $order->expires_at?->translatedFormat('d F Y, H:i') }}. Halaman ini diperbarui otomatis setelah pembayaran diterima.
                </p>
                <div class="mt-8 flex flex-wrap justify-center gap-2">
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
                <h2 class="font-serif text-4xl font-medium">{{ $order->status->label() }}</h2>
                <p class="mx-auto mt-3 max-w-sm text-sm text-gray-600">Pembayaran tidak diterima. Silakan buat pembayaran baru.</p>
                @unless ($invitation->trashed())
                    <a href="{{ route('app.checkout', $invitation) }}" class="btn btn-primary mt-6">Bayar ulang</a>
                @endunless
        @endswitch

        @if ($notice)
            <p class="mt-4 text-sm text-gray-500" role="status">{{ $notice }}</p>
        @endif
    </section>

    <section class="card sm:p-8">
        <dl class="space-y-3 text-sm">
            <div class="flex justify-between"><dt class="text-gray-500">Nomor order</dt><dd class="font-mono">{{ $order->number }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Jenis</dt><dd>{{ $order->typeLabel() }}@if ($order->duration_days) {{ $order->duration_days }} hari @endif</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Harga</dt><dd>{{ $rp($order->base_amount) }}</dd></div>
            @if ($order->discount_amount)
                <div class="flex justify-between text-emerald-700"><dt>Diskon</dt><dd>- {{ $rp($order->discount_amount) }}</dd></div>
            @endif
            <div class="flex items-end justify-between border-y border-rose-brand-900/8 py-3"><dt class="font-semibold">Total</dt><dd class="font-display text-3xl leading-none [font-variant-numeric:lining-nums]">{{ $rp($order->total_amount) }}</dd></div>
            @if ($order->payment_method)
                <div class="flex justify-between"><dt class="text-gray-500">Metode</dt><dd>{{ str_replace('_', ' ', $order->payment_method) }}{{ $order->payment_channel ? ' ('.strtoupper($order->payment_channel).')' : '' }}</dd></div>
            @endif
            <div class="flex justify-between"><dt class="text-gray-500">Status</dt><dd><span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span></dd></div>
        </dl>
    </section>
</div>
