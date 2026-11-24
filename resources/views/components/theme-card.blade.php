@props(['theme'])
@php
    $registry = app(\App\Services\ThemeRegistry::class);
    $variants = $theme->variants();
    $first = array_key_first($variants) ?? '';
@endphp
{{-- Public theme card: palette preview, variant dots and a demo link per variant. --}}
<div x-data="{ variant: @js($first) }" class="group overflow-hidden rounded-2xl border border-rose-brand-900/8 bg-white transition duration-300 ease-out-expo hover:-translate-y-1 hover:shadow-[0_28px_48px_-28px_rgb(89_52_59/0.4)]">
    <a x-bind:href="@js(route('themes.demo', $theme)) + (variant ? '?varian=' + variant : '')" target="_blank" rel="noopener" class="relative block overflow-hidden">
        @foreach ($variants ?: ['' => null] as $key => $v)
            <div x-show="variant === @js($key)" @if (! $loop->first) x-cloak @endif>
                <x-theme-swatch :theme="$theme" :variant="$key ?: null" class="aspect-[4/5] transition duration-700 ease-out-expo group-hover:scale-[1.03]" />
            </div>
        @endforeach
        @if ($theme->badge === 'new')
            <span class="absolute top-3 left-3 rounded-full bg-white/90 px-2.5 py-1 text-[11px] font-semibold tracking-[0.08em] text-rose-brand-600 uppercase backdrop-blur">Baru</span>
        @endif
    </a>
    <div class="space-y-4 p-5">
        <div>
            <p class="font-serif text-xl leading-tight font-medium">{{ $theme->name }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ $theme->categoryLabel() }}@if (count($variants) > 1) &middot; {{ count($variants) }} warna @endif</p>
        </div>
        <div class="flex items-center justify-between gap-3">
            <div class="flex gap-1.5">
                @if (count($variants) > 1)
                    @foreach ($variants as $key => $v)
                        @php [$bg, $primary] = $registry->swatch($theme->slug, $key); @endphp
                        <button type="button" x-on:click="variant = @js($key)" title="{{ $v['name'] }}" aria-label="Warna {{ $v['name'] }}"
                                class="h-5 w-5 rounded-full ring-offset-2" style="background: linear-gradient(135deg, {{ $bg }} 50%, {{ $primary }} 50%)"
                                x-bind:class="variant === @js($key) ? 'ring-2 ring-rose-brand-500' : 'ring-1 ring-gray-300'"></button>
                    @endforeach
                @endif
            </div>
            <a x-bind:href="@js(route('themes.demo', $theme)) + (variant ? '?varian=' + variant : '')" href="{{ route('themes.demo', $theme) }}" target="_blank" rel="noopener" class="btn btn-outline shrink-0 px-4 py-1.5">Lihat demo</a>
        </div>
    </div>
</div>
