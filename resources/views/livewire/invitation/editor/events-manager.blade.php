<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-serif text-lg font-semibold">Acara</h2>
            <p class="text-sm text-gray-500">Akad, pemberkatan, resepsi, atau acara lainnya.</p>
        </div>
        @unless ($showForm)
            <button type="button" wire:click="create" class="btn btn-primary">Tambah acara</button>
        @endunless
    </div>
    @error('form') <p class="form-error">{{ $message }}</p> @enderror

    @if ($showForm)
        <form wire:submit="save" class="card space-y-4">
            <h3 class="font-semibold">{{ $editingId ? 'Ubah acara' : 'Acara baru' }}</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <x-editor.field model="form.name" label="Nama acara" placeholder="Akad Nikah" maxlength="100" />
                <x-editor.field model="form.date" label="Tanggal" type="date" />
                <x-editor.field model="form.start_time" label="Jam mulai" type="time" />
                <x-editor.field model="form.end_time" label="Jam selesai" type="time" hint="Kosongkan untuk menampilkan &quot;Selesai&quot;." />
                <div>
                    <label for="form-timezone" class="form-label">Zona waktu</label>
                    <select id="form-timezone" wire:model="form.timezone" class="form-input">
                        @foreach ($timezones as $tz)
                            <option value="{{ $tz }}">{{ $tz }}</option>
                        @endforeach
                    </select>
                    @error('form.timezone') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <x-editor.field model="form.venue_name" label="Nama tempat" placeholder="Gedung Serbaguna Melati" maxlength="150" />
            </div>
            <x-editor.field model="form.address" label="Alamat lengkap" type="textarea" rows="3" maxlength="500" />
            <x-editor.field model="form.maps_url" label="Link Google Maps" type="url" placeholder="https://maps.app.goo.gl/..." hint="Buka Google Maps, pilih lokasi, klik Bagikan, lalu salin link." />
            <div class="flex justify-end gap-2">
                <button type="button" wire:click="cancel" class="btn btn-ghost">Batal</button>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">Simpan acara</button>
            </div>
        </form>
    @endif

    @forelse ($events as $event)
        <div wire:key="event-{{ $event->id }}" class="card flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <p class="font-semibold">{{ $event->name }}</p>
                <p class="text-sm text-gray-600">{{ $event->date->translatedFormat('l, d F Y') }} &middot; {{ $event->timeLabel() }}</p>
                <p class="truncate text-sm text-gray-500">{{ $event->venue_name }}</p>
            </div>
            <div class="flex shrink-0 flex-wrap gap-1">
                <x-editor.move-buttons :id="$event->id" :first="$loop->first" :last="$loop->last" />
                <button type="button" wire:click="edit({{ $event->id }})" class="btn btn-outline px-4 py-1.5">Ubah</button>
                <button type="button" wire:click="delete({{ $event->id }})" wire:confirm="Hapus acara ini?" class="btn btn-ghost px-4 py-1.5 text-red-600">Hapus</button>
            </div>
        </div>
    @empty
        @unless ($showForm)
            <div class="card py-10 text-center text-sm text-gray-500">Belum ada acara. Klik "Tambah acara" untuk memulai.</div>
        @endunless
    @endforelse
</div>
