<div class="space-y-6">
    <section class="card space-y-4">
        <div>
            <h2 class="font-serif text-lg font-semibold">Unggah musik sendiri</h2>
            <p class="text-sm text-gray-500">File MP3, maksimal 10 MB. Jika diisi, musik ini yang diputar. Pastikan Anda berhak memakai lagu tersebut.</p>
        </div>

        @if ($invitation->custom_music_path)
            <div class="flex flex-col gap-3 rounded-xl bg-rose-brand-50 p-4 sm:flex-row sm:items-center">
                <audio controls preload="none" src="{{ $invitation->mediaUrl($invitation->custom_music_path) }}" class="w-full sm:flex-1"></audio>
                <button type="button" wire:click="removeUpload" wire:confirm="Hapus musik ini?" class="btn btn-ghost text-red-600">Hapus</button>
            </div>
        @endif

        <form wire:submit="saveUpload" class="space-y-3">
            <input type="file" wire:model="upload" accept="audio/mpeg,.mp3" class="block w-full text-sm file:mr-3 file:rounded-full file:border-0 file:bg-rose-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-rose-brand-700 hover:file:bg-rose-brand-100">
            <div wire:loading wire:target="upload" class="text-sm text-gray-500">Mengunggah...</div>
            @error('upload') <p class="form-error">{{ $message }}</p> @enderror
            @if ($upload)
                <x-editor.save-bar label="Simpan musik" target="saveUpload" />
            @endif
        </form>
    </section>

    <section class="card space-y-4">
        <div>
            <h2 class="font-serif text-lg font-semibold">Pilih dari perpustakaan</h2>
            <p class="text-sm text-gray-500">Dipakai jika Anda tidak mengunggah musik sendiri.</p>
        </div>

        <form wire:submit="selectTrack" class="space-y-2">
            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-100 p-3 hover:bg-rose-brand-50">
                <input type="radio" wire:model="track" value="none" class="text-rose-brand-500 focus:ring-rose-brand-400">
                <span class="text-sm">Tanpa musik</span>
            </label>
            @forelse ($tracks as $item)
                <label wire:key="track-{{ $item->id }}" class="flex cursor-pointer flex-col gap-2 rounded-xl border border-gray-100 p-3 hover:bg-rose-brand-50 sm:flex-row sm:items-center">
                    <span class="flex items-center gap-3 sm:flex-1">
                        <input type="radio" wire:model="track" value="{{ $item->id }}" class="text-rose-brand-500 focus:ring-rose-brand-400">
                        <span class="text-sm"><span class="font-medium">{{ $item->title }}</span>@if ($item->artist) <span class="text-gray-500">- {{ $item->artist }}</span>@endif</span>
                    </span>
                    <audio controls preload="none" src="{{ $item->url() }}" class="h-9 w-full sm:w-64"></audio>
                </label>
            @empty
                <p class="rounded-xl bg-gray-50 p-4 text-sm text-gray-500">Perpustakaan musik masih kosong. Silakan unggah musik sendiri.</p>
            @endforelse
            @error('track') <p class="form-error">{{ $message }}</p> @enderror
            <x-editor.save-bar label="Simpan pilihan" target="selectTrack" />
        </form>
    </section>
</div>
