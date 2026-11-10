<div class="space-y-4">
    <div>
        <h2 class="font-serif text-lg font-semibold">Tema</h2>
        <p class="text-sm text-gray-500">{{ $themes->count() }} tema, semua termasuk dalam paket. Data undangan tetap sama saat tema atau warna diganti.</p>
    </div>
    @error('theme_id') <p class="form-error">{{ $message }}</p> @enderror

    <div class="-mx-1 flex gap-2 overflow-x-auto px-1 pb-1" role="tablist" aria-label="Kategori tema">
        <button type="button" wire:click="$set('category', '')" @class([
            'shrink-0 rounded-full px-4 py-1.5 text-sm font-medium',
            'bg-rose-brand-500 text-white' => $category === '',
            'bg-white text-gray-600 ring-1 ring-gray-200 hover:bg-rose-brand-50' => $category !== '',
        ])>Semua</button>
        @foreach ($categories as $key => $label)
            <button type="button" wire:click="$set('category', '{{ $key }}')" @class([
                'shrink-0 rounded-full px-4 py-1.5 text-sm font-medium',
                'bg-rose-brand-500 text-white' => $category === $key,
                'bg-white text-gray-600 ring-1 ring-gray-200 hover:bg-rose-brand-50' => $category !== $key,
            ])>{{ $label }}</button>
        @endforeach
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($themes as $theme)
            @php
                $selected = $invitation->theme_id === $theme->id;
                $variants = $theme->variants();
                $initial = ($selected ? $currentVariant : array_key_first($variants)) ?? '';
            @endphp
            <div wire:key="theme-{{ $theme->id }}" x-data="{ variant: @js($initial) }" @class([
                'overflow-hidden rounded-2xl border-2 bg-white',
                'border-rose-brand-500 ring-2 ring-rose-brand-100' => $selected,
                'border-gray-100' => ! $selected,
            ])>
                <div class="relative">
                    @foreach ($variants ?: ['' => null] as $key => $v)
                        <div x-show="variant === @js($key)" @if (! $loop->first) x-cloak @endif>
                            <x-theme-swatch :theme="$theme" :variant="$key" class="h-40" />
                        </div>
                    @endforeach
                    @if ($theme->badge === 'new')
                        <span class="absolute top-2 left-2 rounded-full bg-white/90 px-2 py-0.5 text-[11px] font-semibold text-rose-brand-600">Baru</span>
                    @endif
                </div>
                <div class="space-y-3 p-4">
                    <div class="min-w-0">
                        <p class="font-semibold">{{ $theme->name }}</p>
                        <p class="text-xs text-gray-500">{{ $theme->categoryLabel() }}</p>
                    </div>
                    @if (count($variants) > 1)
                        <div class="flex items-center gap-2" role="radiogroup" aria-label="Warna {{ $theme->name }}">
                            @foreach ($variants as $key => $v)
                                @php [$bg, $primary] = app(\App\Services\ThemeRegistry::class)->swatch($theme->slug, $key); @endphp
                                <button type="button" x-on:click="variant = @js($key)" title="{{ $v['name'] }}" aria-label="{{ $v['name'] }}"
                                        class="h-6 w-6 rounded-full ring-offset-2" style="background: linear-gradient(135deg, {{ $bg }} 50%, {{ $primary }} 50%)"
                                        x-bind:class="variant === @js($key) ? 'ring-2 ring-rose-brand-500' : 'ring-1 ring-gray-300'"></button>
                            @endforeach
                            <span class="text-xs text-gray-500" x-text="@js(collect($variants)->map->name)[variant] ?? ''"></span>
                        </div>
                    @endif
                    <div class="flex gap-2">
                        <a x-bind:href="@js(route('themes.demo', $theme)) + '?varian=' + variant" target="_blank" class="btn btn-ghost flex-1 px-3 py-1.5">Lihat demo</a>
                        @if ($selected)
                            <button type="button" x-show="variant !== @js($currentVariant ?? '')" x-cloak x-on:click="$wire.choose({{ $theme->id }}, variant)" class="btn btn-primary flex-1 px-3 py-1.5">Pakai warna ini</button>
                            <span x-show="variant === @js($currentVariant ?? '')" class="flex flex-1 items-center justify-center rounded-full bg-rose-brand-50 px-3 py-1.5 text-xs font-semibold text-rose-brand-700">Dipakai</span>
                        @else
                            <button type="button" x-on:click="$wire.choose({{ $theme->id }}, variant)" class="btn btn-outline flex-1 px-3 py-1.5">Pakai</button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-500">Belum ada tema di kategori ini.</p>
        @endforelse
    </div>
</div>
