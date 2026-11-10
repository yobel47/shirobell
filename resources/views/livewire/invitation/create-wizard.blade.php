<div class="mx-auto max-w-2xl">
    <ol class="mb-8 flex items-center gap-3 text-sm">
        @foreach ([1 => 'Nama & alamat', 2 => 'Pilih tema'] as $number => $label)
            <li class="flex items-center gap-2">
                <span @class([
                    'flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold',
                    'bg-rose-brand-500 text-white' => $step >= $number,
                    'bg-gray-100 text-gray-500' => $step < $number,
                ])>{{ $number }}</span>
                <span @class(['font-medium', 'text-ink' => $step >= $number, 'text-gray-400' => $step < $number])>{{ $label }}</span>
            </li>
            @if ($number === 1)
                <li class="h-px flex-1 bg-gray-200" aria-hidden="true"></li>
            @endif
        @endforeach
    </ol>

    @if ($step === 1)
        <form wire:submit="next" class="card space-y-5">
            <div>
                <h2 class="font-serif text-xl font-semibold">Siapa yang menikah?</h2>
                <p class="mt-1 text-sm text-gray-500">Cukup nama panggilan. Data lengkap bisa diisi nanti di editor.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="groom_nickname" class="form-label">Nama panggilan pria</label>
                    <input id="groom_nickname" type="text" wire:model.live.debounce.400ms="groom_nickname" class="form-input" placeholder="Budi" maxlength="50" autofocus>
                    @error('groom_nickname') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="bride_nickname" class="form-label">Nama panggilan wanita</label>
                    <input id="bride_nickname" type="text" wire:model.live.debounce.400ms="bride_nickname" class="form-input" placeholder="Ani" maxlength="50">
                    @error('bride_nickname') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="slug" class="form-label">Alamat undangan</label>
                <div class="flex items-stretch overflow-hidden rounded-xl border border-gray-200 bg-white focus-within:border-rose-brand-400 focus-within:ring-2 focus-within:ring-rose-brand-100">
                    <input id="slug" type="text" wire:model.live.debounce.500ms="slug" class="min-w-0 flex-1 border-0 px-4 py-2.5 text-sm focus:ring-0 focus:outline-none" placeholder="budi-ani" maxlength="40" autocomplete="off" autocapitalize="none" spellcheck="false">
                    <span class="flex items-center bg-gray-50 px-3 text-sm text-gray-500">.{{ config('app.domain') }}</span>
                </div>
                @error('slug')
                    <p class="form-error">{{ $message }}</p>
                @else
                    @if ($slug !== '')
                        <p @class(['mt-1.5 text-sm', 'text-emerald-600' => $this->slugAvailable, 'text-red-600' => ! $this->slugAvailable])>
                            {{ $this->slugAvailable ? 'Alamat tersedia.' : 'Alamat tidak tersedia.' }}
                        </p>
                    @endif
                @enderror
                <p class="mt-1.5 text-xs text-gray-500">Huruf kecil, angka, dan tanda hubung. Bisa diubah selama undangan belum diterbitkan.</p>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">Lanjut pilih tema</button>
            </div>
        </form>
    @else
        <form wire:submit="create" class="card space-y-5">
            <div>
                <h2 class="font-serif text-xl font-semibold">Pilih tema</h2>
                <p class="mt-1 text-sm text-gray-500">Semua tema sudah termasuk dalam paket. Tema bisa diganti kapan saja tanpa kehilangan data.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                @forelse ($this->themes as $theme)
                    <label wire:key="theme-{{ $theme->id }}" @class([
                        'cursor-pointer overflow-hidden rounded-2xl border-2 transition',
                        'border-rose-brand-500 ring-2 ring-rose-brand-100' => $theme_id === $theme->id,
                        'border-gray-100 hover:border-rose-brand-200' => $theme_id !== $theme->id,
                    ])>
                        <input type="radio" wire:model.live="theme_id" value="{{ $theme->id }}" class="sr-only">
                        <x-theme-swatch :theme="$theme" class="h-36" />
                        <div class="p-4">
                            <p class="font-semibold">{{ $theme->name }}</p>
                            <p class="mt-0.5 text-sm text-gray-500">{{ $theme->description }}</p>
                        </div>
                    </label>
                @empty
                    <p class="text-sm text-gray-500">Belum ada tema yang tersedia.</p>
                @endforelse
            </div>
            @error('theme_id') <p class="form-error">{{ $message }}</p> @enderror
            @error('slug') <p class="form-error">{{ $message }}</p> @enderror

            <div class="flex items-center justify-between">
                <button type="button" wire:click="back" class="btn btn-ghost">Kembali</button>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="create">Buat undangan</span>
                    <span wire:loading wire:target="create">Membuat...</span>
                </button>
            </div>
        </form>
    @endif
</div>
