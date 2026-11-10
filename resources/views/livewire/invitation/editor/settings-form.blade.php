<div class="space-y-6">
    <form wire:submit="save" class="card space-y-4">
        <div>
            <h2 class="font-serif text-lg font-semibold">Bagian yang ditampilkan</h2>
            <p class="text-sm text-gray-500">Matikan bagian yang tidak ingin ditampilkan di undangan.</p>
        </div>
        <div class="grid gap-2 sm:grid-cols-2">
            @foreach ($sectionLabels as $key => $label)
                <label wire:key="section-{{ $key }}" class="flex cursor-pointer items-center justify-between gap-3 rounded-xl border border-gray-100 px-4 py-3 hover:bg-rose-brand-50">
                    <span class="text-sm">{{ $label }}</span>
                    <input type="checkbox" wire:model="sections.{{ $key }}" class="h-5 w-5 rounded text-rose-brand-500 focus:ring-rose-brand-400">
                </label>
            @endforeach
        </div>
        <p class="text-xs text-gray-500">RSVP, ucapan, dan amplop digital mulai tampil setelah fiturnya tersedia.</p>

        <div class="max-w-xs">
            <x-editor.field model="rsvp_max_pax" label="Maksimal tamu per RSVP" type="number" min="1" max="10" />
        </div>

        <x-editor.save-bar />
    </form>

    <form wire:submit="saveSlug" class="card space-y-4">
        <div>
            <h2 class="font-serif text-lg font-semibold">Alamat undangan</h2>
            <p class="text-sm text-gray-500">
                @if ($canChangeSlug)
                    Bisa diubah selama undangan belum diterbitkan.
                @else
                    Alamat terkunci karena undangan sudah diterbitkan.
                @endif
            </p>
        </div>
        <div class="flex items-stretch overflow-hidden rounded-xl border border-gray-200 bg-white focus-within:border-rose-brand-400 focus-within:ring-2 focus-within:ring-rose-brand-100">
            <input type="text" wire:model="slug" @disabled(! $canChangeSlug) aria-label="Alamat undangan" maxlength="40" autocomplete="off" autocapitalize="none" spellcheck="false" class="min-w-0 flex-1 border-0 px-4 py-2.5 text-sm focus:ring-0 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500">
            <span class="flex items-center bg-gray-50 px-3 text-sm text-gray-500">.{{ config('app.domain') }}</span>
        </div>
        @error('slug') <p class="form-error">{{ $message }}</p> @enderror
        @if ($canChangeSlug)
            <x-editor.save-bar label="Ubah alamat" target="saveSlug" />
        @endif
    </form>

    @if ($canDelete)
        <section class="card space-y-3 border-red-100">
            <h2 class="font-serif text-lg font-semibold text-red-700">Hapus undangan</h2>
            <p class="text-sm text-gray-500">Undangan draft beserta semua foto dan datanya akan dihapus permanen.</p>
            <button type="button" wire:click="deleteInvitation" wire:confirm="Yakin hapus undangan ini? Tindakan ini tidak bisa dibatalkan." class="btn border border-red-200 bg-white text-red-600 hover:bg-red-50">Hapus undangan</button>
        </section>
    @endif
</div>
