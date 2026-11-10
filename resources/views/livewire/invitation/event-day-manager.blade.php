<div class="space-y-6">
    @if (! $invitation->isActive())
        <div class="card">
            <h2 class="font-serif text-lg font-semibold">Fitur hari-H</h2>
            <p class="mt-1 text-sm text-gray-500">Aplikasi penerima tamu, tiket QR, cetak kartu QR, dan layar sapa tersedia setelah undangan aktif.</p>
            @unless (\App\Policies\InvitationPolicy::actingAsAdmin(auth()->user(), $invitation))
                <a href="{{ route('app.checkout', $invitation) }}" class="btn btn-primary mt-4">Aktifkan undangan</a>
            @endunless
        </div>
    @elseif (! $unlocked)
        <div class="card">
            <h2 class="font-serif text-lg font-semibold">Add-on Hari-H</h2>
            <p class="mt-1 text-sm text-gray-500">Aplikasi penerima tamu dengan scan QR, tiket QR untuk setiap tamu, cetak kartu QR, dan layar sapa yang menampilkan nama tamu saat tiba.</p>
            <p class="mt-4 font-serif text-3xl font-semibold">Rp {{ number_format($addonPrice, 0, ',', '.') }}</p>
            <p class="text-sm text-gray-500">Sekali bayar, berlaku selama undangan aktif.</p>
            @unless (\App\Policies\InvitationPolicy::actingAsAdmin(auth()->user(), $invitation))
                <a href="{{ route('app.checkout', [$invitation, 'produk' => 'hari-h']) }}" class="btn btn-primary mt-4">Beli add-on</a>
            @endunless
        </div>
    @elseif (! $open)
        <div class="card">
            <h2 class="font-serif text-lg font-semibold">Penerima tamu</h2>
            <p class="mt-1 text-sm text-gray-500">Aktifkan untuk mendapatkan tautan dan PIN bagi petugas penerima tamu. Setelah aktif, setiap tamu juga mendapat tiket QR di undangan personalnya.</p>
            <button type="button" wire:click="enable" class="btn btn-primary mt-4">Aktifkan penerima tamu</button>
        </div>
    @else
        @php $link = route('reception.show', $invitation->checkin_token); @endphp
        <div class="grid gap-4 md:grid-cols-4" wire:poll.20s>
            @foreach ([
                ['Tamu hadir', $stats['checked_in'].' / '.$stats['guests']],
                ['Total orang', $stats['pax']],
                ['Tamu dadakan', $stats['walk_in']],
                ['Belum hadir', max(0, $stats['guests'] - $stats['checked_in'])],
            ] as [$label, $value])
                <div class="card p-4">
                    <p class="text-sm text-gray-500">{{ $label }}</p>
                    <p class="mt-1 text-2xl font-semibold">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        <div class="card space-y-4">
            <div>
                <h2 class="font-serif text-lg font-semibold">Akses petugas penerima tamu</h2>
                <p class="text-sm text-gray-500">Bagikan tautan dan PIN hanya kepada petugas. Satu tautan bisa dipakai di beberapa HP sekaligus.</p>
            </div>
            <div class="grid gap-3 sm:grid-cols-[1fr_auto]"
                 x-data="{ copied: null, copy(text, key) { navigator.clipboard.writeText(text).then(() => { this.copied = key; setTimeout(() => this.copied = null, 1500) }) } }">
                <div class="min-w-0 rounded-xl bg-gray-50 px-4 py-3">
                    <p class="text-xs text-gray-500">Tautan</p>
                    <p class="truncate font-mono text-sm">{{ $link }}</p>
                </div>
                <button type="button" x-on:click="copy(@js($link), 'link')" class="btn btn-outline">
                    <span x-text="copied === 'link' ? 'Tersalin' : 'Salin tautan'">Salin tautan</span>
                </button>
                <div class="rounded-xl bg-gray-50 px-4 py-3">
                    <p class="text-xs text-gray-500">PIN</p>
                    <p class="font-mono text-2xl tracking-[0.4em]">{{ $invitation->checkin_pin }}</p>
                </div>
                <button type="button" x-on:click="copy(@js((string) $invitation->checkin_pin), 'pin')" class="btn btn-outline">
                    <span x-text="copied === 'pin' ? 'Tersalin' : 'Salin PIN'">Salin PIN</span>
                </button>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ $link }}" target="_blank" class="btn btn-primary">Buka aplikasi penerima tamu</a>
                <a href="{{ route('reception.screen', $invitation->checkin_token) }}" target="_blank" class="btn btn-outline">Buka layar sapa</a>
                <a href="{{ route('app.invitations.event-day.cards', $invitation) }}" target="_blank" class="btn btn-outline">Cetak kartu QR tamu</a>
            </div>
            <div class="flex flex-wrap gap-4 border-t border-gray-100 pt-4 text-sm">
                <button type="button" wire:click="resetAccess" wire:confirm="Buat tautan dan PIN baru? Semua perangkat petugas harus masuk ulang."
                        class="font-medium text-rose-brand-700 hover:underline">Buat tautan &amp; PIN baru</button>
                <button type="button" wire:click="disable" wire:confirm="Nonaktifkan penerima tamu? Tiket QR di undangan juga disembunyikan. Data kehadiran tetap tersimpan."
                        class="font-medium text-gray-500 hover:underline">Nonaktifkan</button>
            </div>
        </div>

        <div class="card">
            <h2 class="font-serif text-lg font-semibold">Baru tiba</h2>
            <ul class="mt-3 divide-y divide-gray-100" wire:poll.20s>
                @forelse ($arrivals as $guest)
                    <li class="flex items-center justify-between gap-3 py-2 text-sm">
                        <span class="font-medium">{{ $guest->name }}@if ($guest->group_name) <span class="text-gray-500">&middot; {{ $guest->group_name }}</span>@endif</span>
                        <span class="shrink-0 text-gray-500">{{ $guest->checked_in_at->format('H.i') }} &middot; {{ $guest->checked_in_pax }} orang</span>
                    </li>
                @empty
                    <li class="py-4 text-center text-sm text-gray-500">Belum ada tamu yang check-in.</li>
                @endforelse
            </ul>
        </div>
    @endif
</div>
