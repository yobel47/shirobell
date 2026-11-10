@props(['label' => 'Simpan', 'target' => 'save'])
<div class="sticky bottom-0 z-10 -mx-4 border-t border-rose-brand-100 bg-white/95 px-4 py-3 backdrop-blur sm:static sm:mx-0 sm:border-0 sm:bg-transparent sm:p-0">
    <div class="flex justify-end">
        <button type="submit" class="btn btn-primary w-full sm:w-auto" wire:loading.attr="disabled" wire:target="{{ $target }}">
            <span wire:loading.remove wire:target="{{ $target }}">{{ $label }}</span>
            <span wire:loading wire:target="{{ $target }}">Menyimpan...</span>
        </button>
    </div>
</div>
