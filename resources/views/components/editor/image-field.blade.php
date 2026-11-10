@props(['label', 'model', 'upload' => null, 'current' => null, 'remove' => null, 'rounded' => false, 'hint' => 'JPG, PNG, atau WebP. Maks 8 MB.'])
@php
    $id = str_replace('.', '-', $model);
    $preview = null;
    if ($upload && method_exists($upload, 'isPreviewable') && $upload->isPreviewable()) {
        $preview = $upload->temporaryUrl();
    }
    $src = $preview ?? $current;
@endphp
<div x-data="{ uploading: false, progress: 0 }"
     x-on:livewire-upload-start="uploading = true"
     x-on:livewire-upload-finish="uploading = false"
     x-on:livewire-upload-error="uploading = false"
     x-on:livewire-upload-progress="progress = $event.detail.progress">
    <span class="form-label">{{ $label }}</span>
    <div class="flex items-center gap-4">
        <div @class(['flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden bg-rose-brand-50 text-rose-brand-300', 'rounded-full' => $rounded, 'rounded-xl' => ! $rounded])>
            @if ($src)
                <img src="{{ $src }}" alt="" class="h-full w-full object-cover">
            @else
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A1.5 1.5 0 0 0 21.75 19.5V4.5A1.5 1.5 0 0 0 20.25 3H3.75A1.5 1.5 0 0 0 2.25 4.5v15A1.5 1.5 0 0 0 3.75 21Z"/></svg>
            @endif
        </div>
        <div class="min-w-0 space-y-2">
            <div class="flex flex-wrap gap-2">
                <label for="{{ $id }}" class="btn btn-outline cursor-pointer px-4 py-1.5">{{ $src ? 'Ganti foto' : 'Pilih foto' }}</label>
                <input id="{{ $id }}" type="file" wire:model="{{ $model }}" accept="image/jpeg,image/png,image/webp" class="sr-only">
                @if ($current && ! $preview && $remove)
                    <button type="button" wire:click="{{ $remove }}" wire:confirm="Hapus foto ini?" class="btn btn-ghost px-4 py-1.5 text-red-600">Hapus</button>
                @endif
            </div>
            <div x-show="uploading" x-cloak class="h-1.5 w-40 overflow-hidden rounded-full bg-gray-100">
                <div class="h-full bg-rose-brand-500 transition-all" x-bind:style="'width: ' + progress + '%'"></div>
            </div>
            @if ($preview)
                <p class="text-xs text-amber-600">Belum tersimpan. Klik Simpan.</p>
            @elseif ($hint)
                <p class="text-xs text-gray-500">{{ $hint }}</p>
            @endif
        </div>
    </div>
    @error($model) <p class="form-error">{{ $message }}</p> @enderror
</div>
