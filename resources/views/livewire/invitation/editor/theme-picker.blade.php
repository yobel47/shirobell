<div class="space-y-4">
    <div>
        <h2 class="font-serif text-lg font-semibold">Tema</h2>
        <p class="text-sm text-gray-500">Semua tema termasuk dalam paket. Data undangan tetap sama saat tema diganti.</p>
    </div>
    @error('theme_id') <p class="form-error">{{ $message }}</p> @enderror

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($themes as $theme)
            @php $selected = $invitation->theme_id === $theme->id; @endphp
            <div wire:key="theme-{{ $theme->id }}" @class([
                'overflow-hidden rounded-2xl border-2 bg-white',
                'border-rose-brand-500 ring-2 ring-rose-brand-100' => $selected,
                'border-gray-100' => ! $selected,
            ])>
                <x-theme-swatch :theme="$theme" class="h-40" />
                <div class="flex items-center justify-between gap-3 p-4">
                    <div class="min-w-0">
                        <p class="font-semibold">{{ $theme->name }}</p>
                        <p class="truncate text-sm text-gray-500">{{ $theme->description }}</p>
                    </div>
                    @if ($selected)
                        <span class="shrink-0 rounded-full bg-rose-brand-50 px-3 py-1 text-xs font-semibold text-rose-brand-700">Dipakai</span>
                    @else
                        <button type="button" wire:click="choose({{ $theme->id }})" class="btn btn-outline shrink-0 px-4 py-1.5">Pakai</button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    <p class="text-sm text-gray-500">Tema lain segera hadir.</p>
</div>
