<div class="space-y-6">
    <section class="card space-y-4">
        <div>
            <h2 class="font-serif text-lg font-semibold">Foto sampul</h2>
            <p class="text-sm text-gray-500">Tampil di halaman pembuka undangan. Disarankan foto potret (vertikal).</p>
        </div>
        <form wire:submit="saveCover" class="space-y-4">
            <x-editor.image-field label="Foto sampul" model="cover" :upload="$cover" :current="$invitation->mediaUrl($invitation->cover_photo_path)" remove="removeCover" />
            @if ($cover)
                <x-editor.save-bar label="Simpan foto sampul" target="saveCover" />
            @endif
        </form>
    </section>

    <section class="card space-y-4"
             x-data="{ uploading: false, progress: 0 }"
             x-on:livewire-upload-start="uploading = true"
             x-on:livewire-upload-finish="uploading = false"
             x-on:livewire-upload-error="uploading = false"
             x-on:livewire-upload-progress="progress = $event.detail.progress">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-serif text-lg font-semibold">Galeri</h2>
                <p class="text-sm text-gray-500">{{ $photos->count() }} dari {{ $max }} foto. Foto otomatis dikompres.</p>
            </div>
            @if ($photos->count() < $max)
                <div>
                    <label for="gallery-uploads" class="btn btn-primary cursor-pointer">Tambah foto</label>
                    <input id="gallery-uploads" type="file" wire:model="uploads" multiple accept="image/jpeg,image/png,image/webp" class="sr-only">
                </div>
            @endif
        </div>

        <div x-show="uploading" x-cloak class="h-1.5 overflow-hidden rounded-full bg-gray-100">
            <div class="h-full bg-rose-brand-500 transition-all" x-bind:style="'width: ' + progress + '%'"></div>
        </div>
        <div wire:loading wire:target="uploads" class="text-sm text-gray-500">Memproses foto...</div>
        @error('uploads') <p class="form-error">{{ $message }}</p> @enderror
        @error('uploads.*') <p class="form-error">{{ $message }}</p> @enderror

        @if ($photos->isEmpty())
            <p class="rounded-xl bg-rose-brand-50 py-10 text-center text-sm text-gray-500">Belum ada foto di galeri.</p>
        @else
            <ul class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($photos as $photo)
                    <li wire:key="photo-{{ $photo->id }}" class="overflow-hidden rounded-xl border border-gray-100 bg-white">
                        <img src="{{ $photo->url() }}" alt="Foto galeri {{ $loop->iteration }}" class="aspect-square w-full object-cover" loading="lazy">
                        <div class="flex items-center justify-between px-1 py-1">
                            <div class="flex">
                                <x-editor.move-buttons :id="$photo->id" :first="$loop->first" :last="$loop->last" />
                            </div>
                            <button type="button" wire:click="deletePhoto({{ $photo->id }})" wire:confirm="Hapus foto ini?" class="btn btn-ghost px-3 py-1.5 text-xs text-red-600">Hapus</button>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</div>
