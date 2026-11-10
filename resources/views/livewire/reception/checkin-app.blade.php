<div class="space-y-4">
    {{-- Live counters --}}
    <div class="grid grid-cols-3 gap-2 text-center" wire:poll.15s>
        <div class="rounded-2xl bg-white p-3 shadow-sm">
            <p class="text-2xl font-semibold">{{ $stats['checked_in'] }}</p>
            <p class="text-xs text-gray-500">dari {{ $stats['guests'] }} tamu hadir</p>
        </div>
        <div class="rounded-2xl bg-white p-3 shadow-sm">
            <p class="text-2xl font-semibold">{{ $stats['pax'] }}</p>
            <p class="text-xs text-gray-500">orang</p>
        </div>
        <div class="rounded-2xl bg-white p-3 shadow-sm">
            <p class="text-2xl font-semibold">{{ $stats['walk_in'] }}</p>
            <p class="text-xs text-gray-500">tamu dadakan</p>
        </div>
    </div>

    {{-- Result of the last action --}}
    @if ($result)
        @php
            $tone = match ($result['status']) {
                'ok' => 'border-emerald-300 bg-emerald-50 text-emerald-900',
                'already' => 'border-amber-300 bg-amber-50 text-amber-900',
                default => 'border-red-300 bg-red-50 text-red-900',
            };
        @endphp
        <div class="rounded-2xl border-2 p-4 {{ $tone }}" role="status" aria-live="polite" wire:key="result-{{ $result['id'] ?? 'x' }}-{{ $result['status'] }}">
            @if ($result['status'] === 'unknown')
                <p class="text-lg font-semibold">QR tidak dikenali</p>
                <p class="text-sm">Bukan tiket undangan ini. Cari nama tamu atau tambahkan sebagai tamu dadakan.</p>
            @else
                <p class="text-sm font-medium">{{ $result['status'] === 'ok' ? 'Check-in berhasil' : 'Sudah check-in pukul '.$result['time'] }}</p>
                <p class="mt-1 text-2xl font-semibold">{{ $result['name'] }}</p>
                @if ($result['group'] || $result['walk_in'])
                    <p class="text-sm">{{ $result['group'] }}{{ $result['group'] && $result['walk_in'] ? ' · ' : '' }}{{ $result['walk_in'] ? 'Tamu dadakan' : '' }}</p>
                @endif
                @if ($result['table'])
                    <p class="mt-2 inline-block rounded-full bg-white px-3 py-1 text-sm font-semibold">Meja {{ $result['table'] }}</p>
                @endif
                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-2" aria-label="Jumlah orang">
                        <button type="button" wire:click="setPax({{ $result['id'] }}, {{ max(1, $result['pax'] - 1) }})" class="h-9 w-9 rounded-full bg-white text-lg font-semibold shadow-sm" aria-label="Kurangi">&minus;</button>
                        <span class="min-w-16 text-center font-semibold">{{ $result['pax'] }} orang</span>
                        <button type="button" wire:click="setPax({{ $result['id'] }}, {{ min($maxPax, $result['pax'] + 1) }})" class="h-9 w-9 rounded-full bg-white text-lg font-semibold shadow-sm" aria-label="Tambah">+</button>
                    </div>
                    <button type="button" wire:click="undo({{ $result['id'] }})" wire:confirm="Batalkan check-in {{ $result['name'] }}?" class="ml-auto text-sm font-medium underline">Batalkan</button>
                </div>
                @if ($invitation->checkin_selfie)
                    <div class="mt-3 flex items-center gap-3" x-data="selfieCamera" wire:key="selfie-{{ $result['id'] }}">
                        @if ($result['selfie'])
                            <img src="{{ $result['selfie'] }}" alt="Foto {{ $result['name'] }}" class="h-14 w-14 rounded-full object-cover ring-2 ring-white">
                        @endif
                        <button type="button" x-on:click="show()" class="rounded-full bg-white px-4 py-1.5 text-sm font-medium shadow-sm">{{ $result['selfie'] ? 'Foto ulang' : 'Ambil foto tamu' }}</button>
                        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex flex-col bg-black" x-on:keydown.escape.window="hide()">
                            <div class="relative flex-1">
                                <video x-ref="selfieVideo" class="h-full w-full object-cover" playsinline muted></video>
                                <p x-show="error" x-text="error" class="absolute inset-x-4 top-4 rounded-xl bg-black/70 p-3 text-center text-sm text-white"></p>
                            </div>
                            <div class="flex items-center justify-between gap-3 bg-black p-4 text-white">
                                <button type="button" x-on:click="hide()" class="rounded-full px-4 py-2 text-sm">Batal</button>
                                <button type="button" x-on:click="capture()" x-bind:disabled="busy" class="h-16 w-16 rounded-full border-4 border-white bg-white/20 disabled:opacity-50" aria-label="Ambil foto"></button>
                                <button type="button" x-on:click="flip()" class="rounded-full px-4 py-2 text-sm">Balik kamera</button>
                            </div>
                        </div>
                        @error('selfie') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                @endif
                <div class="mt-3 flex flex-wrap gap-2 border-t border-current/15 pt-3" x-data="{ gift: false }">
                    <button type="button" wire:click="toggleSouvenir({{ $result['id'] }})" @class([
                        'rounded-full px-4 py-1.5 text-sm font-medium',
                        'bg-emerald-600 text-white' => $result['souvenir'],
                        'bg-white shadow-sm' => ! $result['souvenir'],
                    ])>{{ $result['souvenir'] ? 'Souvenir diberikan' : 'Beri souvenir' }}</button>
                    <button type="button" x-on:click="gift = !gift" class="rounded-full bg-white px-4 py-1.5 text-sm font-medium shadow-sm">
                        Catat hadiah @if ($result['gifts']) ({{ $result['gifts'] }}) @endif
                    </button>
                    <form x-show="gift" x-cloak wire:submit="recordGift" x-on:submit="gift = false" class="grid w-full gap-2 rounded-xl bg-white p-3 text-ink sm:grid-cols-[auto_1fr_1fr_auto]">
                        <select wire:model="giftKind" class="rounded-lg border-gray-200 text-sm" aria-label="Jenis hadiah">
                            @foreach ($giftKinds as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <input type="number" min="0" wire:model="giftAmount" placeholder="Nominal (opsional)" class="rounded-lg border-gray-200 text-sm" aria-label="Nominal">
                        <input type="text" wire:model="giftNote" maxlength="200" placeholder="Catatan, mis. nomor amplop" class="rounded-lg border-gray-200 text-sm" aria-label="Catatan">
                        <button type="submit" class="btn btn-primary px-4 py-1.5">Simpan</button>
                        @error('giftAmount') <p class="form-error sm:col-span-4">{{ $message }}</p> @enderror
                    </form>
                </div>
            @endif
        </div>
    @endif

    {{-- Modes --}}
    <div class="grid grid-cols-3 gap-1 rounded-full bg-gray-200 p-1 text-sm font-medium">
        @foreach (['scan' => 'Pindai QR', 'search' => 'Cari nama', 'walkin' => 'Tamu dadakan'] as $key => $label)
            <button type="button" wire:click="$set('mode', '{{ $key }}')" @class([
                'rounded-full px-2 py-2',
                'bg-white shadow-sm' => $mode === $key,
                'text-gray-600' => $mode !== $key,
            ])>{{ $label }}</button>
        @endforeach
    </div>

    @if ($mode === 'scan')
        <div wire:key="scanner" x-data="qrScanner" x-init="start()" x-on:selfie-open.window="stop()" x-on:selfie-close.window="start()" class="overflow-hidden rounded-2xl bg-black">
            <div class="relative aspect-square">
                <video x-ref="video" class="h-full w-full object-cover" playsinline muted></video>
                <canvas x-ref="canvas" class="hidden"></canvas>
                <div class="pointer-events-none absolute inset-10 rounded-3xl border-4 border-white/70"></div>
                <div x-show="error" x-cloak class="absolute inset-0 flex items-center justify-center bg-black/80 p-6 text-center text-sm text-white" x-text="error"></div>
            </div>
            <p class="bg-black px-4 py-3 text-center text-xs text-white/80">Arahkan kamera ke QR tiket tamu. Hasil muncul otomatis.</p>
        </div>
    @elseif ($mode === 'search')
        <div class="space-y-3" wire:key="search">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Ketik minimal 2 huruf nama atau grup" class="form-input" autofocus>
            <ul class="divide-y divide-gray-100 rounded-2xl bg-white shadow-sm">
                @forelse ($matches as $guest)
                    <li wire:key="g-{{ $guest->id }}" class="flex items-center justify-between gap-3 px-4 py-3" x-data="{ pax: {{ max(1, (int) ($guest->rsvp?->pax ?? 1)) }} }">
                        <div class="min-w-0">
                            <p class="truncate font-medium">{{ $guest->name }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $guest->group_name ?: 'Tanpa grup' }}
                                @if ($guest->rsvp) &middot; RSVP: {{ $guest->rsvp->attendance->label() }}@if ($guest->rsvp->pax) ({{ $guest->rsvp->pax }})@endif @endif
                            </p>
                        </div>
                        @if ($guest->checked_in_at)
                            <span class="shrink-0 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Hadir {{ $guest->checked_in_at->format('H.i') }}</span>
                        @else
                            <div class="flex shrink-0 items-center gap-2">
                                <select x-model.number="pax" class="rounded-lg border-gray-200 py-1 text-sm" aria-label="Jumlah orang">
                                    @for ($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                <button type="button" x-on:click="$wire.checkIn({{ $guest->id }}, pax)" class="btn btn-primary px-4 py-1.5">Check-in</button>
                            </div>
                        @endif
                    </li>
                @empty
                    <li class="px-4 py-6 text-center text-sm text-gray-500">
                        {{ mb_strlen(trim($search)) >= 2 ? 'Tidak ditemukan. Tambahkan sebagai tamu dadakan.' : 'Ketik nama tamu untuk mencari.' }}
                    </li>
                @endforelse
            </ul>
        </div>
    @else
        <form wire:submit="addWalkIn" class="space-y-3 rounded-2xl bg-white p-4 shadow-sm" wire:key="walkin">
            <p class="text-sm text-gray-500">Untuk tamu yang tidak ada di daftar. Tamu langsung tercatat hadir.</p>
            <div>
                <label for="walkin-name" class="form-label">Nama tamu</label>
                <input id="walkin-name" type="text" wire:model="walkInName" maxlength="100" class="form-input">
                @error('walkInName') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="walkin-pax" class="form-label">Jumlah orang</label>
                <input id="walkin-pax" type="number" min="1" max="{{ $maxPax }}" wire:model="walkInPax" class="form-input">
                @error('walkInPax') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="btn btn-primary w-full">Tambah &amp; check-in</button>
        </form>
    @endif
</div>
