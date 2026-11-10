<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-serif text-lg font-semibold">Cerita cinta</h2>
            <p class="text-sm text-gray-500">Perjalanan kalian, dari pertama bertemu sampai hari bahagia.</p>
        </div>
        @unless ($showForm)
            <button type="button" wire:click="create" class="btn btn-primary">Tambah cerita</button>
        @endunless
    </div>
    @error('form') <p class="form-error">{{ $message }}</p> @enderror

    @if ($showForm)
        <form wire:submit="save" class="card space-y-4">
            <h3 class="font-semibold">{{ $editingId ? 'Ubah cerita' : 'Cerita baru' }}</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <x-editor.field model="form.title" label="Judul" placeholder="Pertama Bertemu" maxlength="150" />
                <x-editor.field model="form.story_date" label="Waktu (opsional)" placeholder="Juni 2020" maxlength="50" />
            </div>
            <x-editor.field model="form.body" label="Cerita" type="textarea" rows="5" maxlength="2000" />
            <x-editor.image-field label="Foto (opsional)" model="photo" :upload="$photo" :current="$editing?->photoUrl()" />
            <div class="flex justify-end gap-2">
                <button type="button" wire:click="cancel" class="btn btn-ghost">Batal</button>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">Simpan cerita</button>
            </div>
        </form>
    @endif

    @forelse ($stories as $story)
        <div wire:key="story-{{ $story->id }}" class="card flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex min-w-0 items-center gap-3">
                @if ($story->photoUrl())
                    <img src="{{ $story->photoUrl() }}" alt="" class="h-14 w-14 shrink-0 rounded-xl object-cover">
                @endif
                <div class="min-w-0">
                    <p class="font-semibold">{{ $story->title }}</p>
                    @if ($story->story_date)
                        <p class="text-xs text-rose-brand-600">{{ $story->story_date }}</p>
                    @endif
                    <p class="line-clamp-2 text-sm text-gray-500">{{ $story->body }}</p>
                </div>
            </div>
            <div class="flex shrink-0 flex-wrap gap-1">
                <x-editor.move-buttons :id="$story->id" :first="$loop->first" :last="$loop->last" />
                <button type="button" wire:click="edit({{ $story->id }})" class="btn btn-outline px-4 py-1.5">Ubah</button>
                <button type="button" wire:click="delete({{ $story->id }})" wire:confirm="Hapus cerita ini?" class="btn btn-ghost px-4 py-1.5 text-red-600">Hapus</button>
            </div>
        </div>
    @empty
        @unless ($showForm)
            <div class="card py-10 text-center text-sm text-gray-500">Belum ada cerita. Bagian ini opsional.</div>
        @endunless
    @endforelse
</div>
