<div class="space-y-4">
    <div>
        <h2 class="font-serif text-lg font-semibold">Amplop digital</h2>
        <p class="text-sm text-gray-500">Tamu yang berhalangan hadir bisa mengirim hadiah langsung ke rekening, e-wallet, atau QRIS Anda. Dana tidak melewati {{ $siteName ?? config('app.name') }}.</p>
    </div>

    @unless ($showForm)
        <div class="flex flex-wrap gap-2">
            <button type="button" wire:click="create('bank')" class="btn btn-primary">Tambah rekening</button>
            <button type="button" wire:click="create('ewallet')" class="btn btn-outline">Tambah e-wallet</button>
            <button type="button" wire:click="create('qris')" class="btn btn-outline">Tambah QRIS</button>
        </div>
    @endunless
    @error('form') <p class="form-error">{{ $message }}</p> @enderror

    @if ($showForm)
        <form wire:submit="save" class="card space-y-4">
            <h3 class="font-semibold">{{ $editingId ? 'Ubah' : 'Tambah' }} {{ strtolower($types[$form['type']] ?? '') }}</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="form-type" class="form-label">Jenis</label>
                    <select id="form-type" wire:model.live="form.type" class="form-input">
                        @foreach ($types as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <x-editor.field model="form.provider" :label="$form['type'] === 'bank' ? 'Nama bank' : ($form['type'] === 'ewallet' ? 'Nama e-wallet' : 'Label')" :placeholder="$form['type'] === 'bank' ? 'BCA' : ($form['type'] === 'ewallet' ? 'DANA' : 'QRIS')" maxlength="50" />
                @if ($form['type'] !== 'qris')
                    <x-editor.field model="form.account_number" :label="$form['type'] === 'bank' ? 'Nomor rekening' : 'Nomor HP / akun'" inputmode="numeric" maxlength="50" />
                @endif
                <x-editor.field model="form.account_name" label="Atas nama" maxlength="100" />
            </div>
            @if ($form['type'] === 'qris')
                <x-editor.image-field label="Gambar QRIS" model="qris" :upload="$qris" :current="$editing?->qrisUrl()" hint="Unggah gambar QRIS asli dari aplikasi bank / e-wallet Anda. Pastikan jelas dan tidak terpotong." />
            @endif
            <div class="flex justify-end gap-2">
                <button type="button" wire:click="cancel" class="btn btn-ghost">Batal</button>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">Simpan</button>
            </div>
        </form>
    @endif

    @forelse ($accounts as $account)
        <div wire:key="gift-{{ $account->id }}" class="card flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex min-w-0 items-center gap-3">
                @if ($account->qrisUrl())
                    <img src="{{ $account->qrisUrl() }}" alt="QRIS" class="h-14 w-14 shrink-0 rounded-lg border border-gray-100 object-contain">
                @endif
                <div class="min-w-0">
                    <p class="text-xs font-semibold tracking-wide text-rose-brand-600 uppercase">{{ $types[$account->type] ?? $account->type }}</p>
                    <p class="font-semibold">{{ $account->provider }} @if ($account->account_number) <span class="font-normal text-gray-600">{{ $account->account_number }}</span> @endif</p>
                    <p class="truncate text-sm text-gray-500">a.n. {{ $account->account_name }}</p>
                </div>
            </div>
            <div class="flex shrink-0 flex-wrap gap-1">
                <x-editor.move-buttons :id="$account->id" :first="$loop->first" :last="$loop->last" />
                <button type="button" wire:click="edit({{ $account->id }})" class="btn btn-outline px-4 py-1.5">Ubah</button>
                <button type="button" wire:click="delete({{ $account->id }})" wire:confirm="Hapus akun ini?" class="btn btn-ghost px-4 py-1.5 text-red-600">Hapus</button>
            </div>
        </div>
    @empty
        @unless ($showForm)
            <div class="card py-10 text-center text-sm text-gray-500">Belum ada rekening atau QRIS. Bagian ini opsional.</div>
        @endunless
    @endforelse
</div>
