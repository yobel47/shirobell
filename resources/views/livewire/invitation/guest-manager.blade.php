<div class="space-y-6">
    @unless ($isActive)
        <div class="rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-800">
            Undangan belum diterbitkan. Anda bisa menyiapkan daftar tamu sekarang; link baru bisa dibuka tamu setelah undangan aktif.
        </div>
    @endunless

    <dl class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        @foreach (['total' => 'Total tamu', 'shared' => 'Sudah dikirim', 'opened' => 'Sudah dibuka', 'rsvp' => 'Sudah RSVP'] as $key => $label)
            <div class="card p-4">
                <dt class="text-xs text-gray-500">{{ $label }}</dt>
                <dd class="mt-1 font-serif text-2xl font-semibold">{{ number_format($stats[$key], 0, ',', '.') }}</dd>
            </div>
        @endforeach
    </dl>

    {{-- Add guests --}}
    <section class="card space-y-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="font-serif text-lg font-semibold">Tambah tamu</h2>
                <p class="text-sm text-gray-500">Tempel banyak nama sekaligus, satu nama per baris.</p>
            </div>
            @unless ($showBulk)
                <button type="button" wire:click="$set('showBulk', true)" class="btn btn-primary">Tambah tamu</button>
            @endunless
        </div>
        @if ($showBulk)
            <form wire:submit="addBulk" class="space-y-4">
                <div>
                    <label for="bulk" class="form-label">Daftar nama</label>
                    <textarea id="bulk" wire:model="bulk" rows="6" class="form-input font-mono" placeholder="Bapak Joko Susilo&#10;Ibu Rina - 081234567890&#10;Keluarga Besar Hartono"></textarea>
                    <p class="mt-1.5 text-xs text-gray-500">Nomor WhatsApp opsional, tulis setelah tanda "-". Maksimal {{ config('shirobell.invitation.bulk_guest_lines') }} nama sekali tambah.</p>
                    @error('bulk') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="max-w-xs">
                    <x-editor.field model="bulkGroup" label="Grup (opsional)" placeholder="Keluarga, Kantor, ..." maxlength="50" list="guest-groups" />
                    <datalist id="guest-groups">
                        @foreach ($groups as $name)
                            <option value="{{ $name }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" wire:click="$set('showBulk', false)" class="btn btn-ghost">Batal</button>
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="addBulk">Simpan tamu</button>
                </div>
            </form>
        @endif
    </section>

    {{-- WhatsApp template --}}
    <details class="card group">
        <summary class="cursor-pointer list-none font-serif text-lg font-semibold">
            Template pesan WhatsApp
            <span class="ml-1 text-sm font-normal text-gray-500 group-open:hidden">(klik untuk mengubah)</span>
        </summary>
        <form wire:submit="saveTemplate" class="mt-4 space-y-3">
            <textarea wire:model="template" rows="10" class="form-input" aria-label="Template pesan WhatsApp"></textarea>
            @error('template') <p class="form-error">{{ $message }}</p> @enderror
            <p class="text-xs text-gray-500">Kode yang otomatis diganti: <code>{nama_tamu}</code>, <code>{link}</code>, <code>{nama_pasangan}</code>.</p>
            <div class="flex justify-end gap-2">
                <button type="button" wire:click="resetTemplate" class="btn btn-ghost">Kembalikan bawaan</button>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="saveTemplate">Simpan template</button>
            </div>
        </form>
    </details>

    {{-- Guest list --}}
    <section class="space-y-3">
        <div class="flex flex-col gap-2 sm:flex-row">
            <input type="search" wire:model.live.debounce.400ms="search" class="form-input sm:max-w-xs" placeholder="Cari nama tamu" aria-label="Cari nama tamu">
            @if ($groups->isNotEmpty())
                <select wire:model.live="group" class="form-input sm:max-w-48" aria-label="Filter grup">
                    <option value="">Semua grup</option>
                    @foreach ($groups as $name)
                        <option value="{{ $name }}">{{ $name }}</option>
                    @endforeach
                </select>
            @endif
        </div>

        @forelse ($guests as $guest)
            @php
                $statusKey = $guest->statusKey();
                $statusLabel = ['belum' => 'Belum dikirim', 'dibagikan' => 'Sudah dikirim', 'dibuka' => 'Sudah dibuka', 'rsvp' => $guest->rsvp?->attendance?->label() ?? 'RSVP'][$statusKey];
                $statusClass = ['belum' => 'bg-gray-100 text-gray-600', 'dibagikan' => 'bg-sky-50 text-sky-700', 'dibuka' => 'bg-violet-50 text-violet-700', 'rsvp' => $guest->rsvp?->attendance?->badgeClass() ?? ''][$statusKey];
                $link = $service->personalUrl($invitation, $guest);
            @endphp
            <div wire:key="guest-{{ $guest->id }}" class="card p-4">
                @if ($editingId === $guest->id)
                    <form wire:submit="update" class="space-y-3">
                        <div class="grid gap-3 sm:grid-cols-4">
                            <x-editor.field model="form.name" label="Nama" maxlength="100" />
                            <x-editor.field model="form.phone" label="No. WhatsApp" inputmode="tel" maxlength="20" />
                            <x-editor.field model="form.group_name" label="Grup" maxlength="50" list="guest-groups" />
                            <x-editor.field model="form.table_number" label="Nomor meja" maxlength="20" />
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" wire:click="cancelEdit" class="btn btn-ghost">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                @else
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="font-semibold">{{ $guest->name }}</p>
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $statusClass }}">{{ $statusLabel }}@if ($statusKey === 'rsvp' && $guest->rsvp?->pax) ({{ $guest->rsvp->pax }} orang)@endif</span>
                            </div>
                            <p class="mt-0.5 text-xs text-gray-500">
                                {{ $guest->group_name ?: 'Tanpa grup' }}@if ($guest->table_number) &middot; Meja {{ $guest->table_number }}@endif @if ($guest->phone) &middot; {{ $guest->phone }}@endif
                            </p>
                        </div>
                        <div class="flex shrink-0 flex-wrap gap-1" x-data="{ copied: false }">
                            <button type="button" class="btn btn-outline px-3 py-1.5"
                                    x-on:click="navigator.clipboard.writeText(@js($link)).then(() => { copied = true; setTimeout(() => copied = false, 1500) })">
                                <span x-show="!copied">Salin link</span><span x-show="copied" x-cloak>Tersalin</span>
                            </button>
                            <a href="{{ $service->whatsappUrl($invitation, $guest) }}" target="_blank" rel="noopener noreferrer"
                               wire:click="markShared({{ $guest->id }})" class="btn btn-primary px-3 py-1.5">Kirim WA</a>
                            <button type="button" wire:click="edit({{ $guest->id }})" class="btn btn-ghost px-3 py-1.5">Ubah</button>
                            <button type="button" wire:click="delete({{ $guest->id }})" wire:confirm="Hapus {{ $guest->name }} dari daftar tamu?" class="btn btn-ghost px-3 py-1.5 text-red-600">Hapus</button>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="card py-10 text-center text-sm text-gray-500">
                {{ $search !== '' || $group !== '' ? 'Tidak ada tamu yang cocok.' : 'Belum ada tamu.' }}
            </div>
        @endforelse

        {{ $guests->links() }}
    </section>
</div>
