@php $rp = fn (int $n) => 'Rp '.number_format($n, 0, ',', '.'); @endphp
<div class="grid gap-6 lg:grid-cols-[1fr_22rem]">
    <section class="card space-y-5 sm:p-8">
        <h2 class="font-serif text-3xl font-medium">{{ ['renewal' => 'Perpanjang undangan', 'event_day' => 'Add-on Hari-H'][$quote['type']] ?? 'Terbitkan undangan' }}</h2>
        <div class="flex items-start gap-4 rounded-xl bg-rose-brand-50 p-5">
            <div class="min-w-0">
                <p class="font-serif text-xl font-medium">{{ $invitation->coupleName() }}</p>
                <p class="truncate text-sm text-gray-500">{{ $invitation->slug }}.{{ config('app.domain') }}</p>
            </div>
        </div>
        @if ($quote['type'] === 'event_day')
        <ul class="check-list text-sm text-gray-600">
            <li>Aplikasi penerima tamu dengan scan QR dan pencarian nama, bisa dipakai di beberapa HP</li>
            <li>Tiket QR di undangan personal setiap tamu dan cetak kartu QR</li>
            <li>Layar sapa yang menampilkan nama tamu saat check-in</li>
            <li>Berlaku selama undangan aktif</li>
        </ul>
        @else
        <ul class="check-list text-sm text-gray-600">
            <li>Aktif {{ $quote['days'] }} hari {{ $quote['type'] === 'renewal' && $invitation->isActive() ? 'tambahan setelah '.$invitation->active_until->translatedFormat('d F Y') : 'sejak pembayaran berhasil' }}</li>
            <li>Semua tema dan fitur: galeri, cerita, musik, RSVP, ucapan, amplop digital, tamu tanpa batas wajar</li>
            <li>Watermark preview hilang dan undangan bisa dibuka tamu</li>
        </ul>
        @endif
        @if ($quote['type'] === 'new' && $invitation->events()->doesntExist())
            <p class="rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-800">Anda belum mengisi acara. Sebaiknya lengkapi undangan dulu sebelum membayar, meskipun data tetap bisa diubah setelah terbit.</p>
        @endif
    </section>

    <aside class="card space-y-5 self-start sm:p-8 lg:sticky lg:top-24">
        <h3 class="text-xs font-semibold tracking-[0.12em] text-rose-brand-600 uppercase">Ringkasan</h3>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-gray-500">{{ $quote['type'] === 'event_day' ? 'Add-on Hari-H' : 'Paket '.$quote['days'].' hari' }}</dt><dd>{{ $rp($quote['base']) }}</dd></div>
            @if ($quote['promo'])
                <div class="flex justify-between text-emerald-700">
                    <dt>Promo {{ $quote['promo']->code }} <button type="button" wire:click="removePromo" class="ml-1 text-xs text-gray-400 underline">hapus</button></dt>
                    <dd>- {{ $rp($quote['discount']) }}</dd>
                </div>
            @endif
            <div class="flex items-end justify-between border-t border-rose-brand-900/8 pt-4"><dt class="pb-1 font-semibold">Total</dt><dd class="font-display text-4xl leading-none [font-variant-numeric:lining-nums]">{{ $rp($quote['total']) }}</dd></div>
        </dl>

        @unless ($quote['promo'])
            <form wire:submit="applyPromo" class="flex gap-2">
                <input type="text" wire:model="promoInput" placeholder="Kode promo" maxlength="30" aria-label="Kode promo" class="form-input uppercase">
                <button type="submit" class="btn btn-outline shrink-0 px-4">Pakai</button>
            </form>
        @endunless
        @error('promoInput') <p class="form-error">{{ $message }}</p> @enderror
        @error('checkout') <p class="form-error">{{ $message }}</p> @enderror
        @error('promo') <p class="form-error">{{ $message }}</p> @enderror

        <button type="button" wire:click="pay" class="btn btn-primary btn-lg w-full" wire:loading.attr="disabled" wire:target="pay">
            <span wire:loading.remove wire:target="pay">{{ $quote['total'] === 0 ? 'Aktifkan sekarang' : 'Bayar sekarang' }}</span>
            <span wire:loading wire:target="pay">Memproses...</span>
        </button>
        <p class="text-center text-xs text-gray-500">Pembayaran diproses aman oleh Midtrans: QRIS, transfer virtual account, e-wallet, kartu, dan gerai retail.</p>
        <p class="text-center text-xs text-gray-500">
            Dengan membayar, Anda menyetujui
            <a href="{{ route('legal.terms') }}" target="_blank" class="text-rose-brand-600 hover:underline">Syarat & Ketentuan</a>
            dan
            <a href="{{ route('legal.refund') }}" target="_blank" class="text-rose-brand-600 hover:underline">Kebijakan Refund</a>.
        </p>
    </aside>
</div>
