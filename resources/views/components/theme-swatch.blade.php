@props(['theme', 'variant' => null])
@php
    $registry = app(\App\Services\ThemeRegistry::class);
    [$bg, $primary, $accent] = $registry->swatch($theme->slug, $variant);
    $engine = $registry->engine($theme->slug);
    $fonts = config('themes.fonts');
    $heading = $engine['heading'] ?? 'script';
    $font = $heading === 'script'
        ? ($fonts[$engine['fonts'][1] ?? 'great-vibes'] ?? $fonts['great-vibes'])
        : ($fonts[$engine['fonts'][0] ?? 'playfair'] ?? $fonts['playfair']);
    $pattern = ($engine['pattern'] ?? 'none') !== 'none' ? 't-pattern t-pattern-'.$engine['pattern'] : null;
    $shape = match ($engine['shape'] ?? 'arch') {
        'circle' => 'rounded-full',
        'rounded' => 'rounded-2xl',
        'square' => 'rounded-sm',
        default => 'rounded-t-full rounded-b-xl',
    };
@endphp
@if ($theme->thumbnail_path && $variant === null)
    <img src="{{ Storage::disk('public')->url($theme->thumbnail_path) }}" alt="Tema {{ $theme->name }}" {{ $attributes->merge(['class' => 'w-full object-cover']) }}>
@else
    {{-- Generated preview from the theme recipe and variant palette (no image needed). --}}
    <div {{ $attributes->merge(['class' => 'relative flex w-full flex-col items-center justify-center gap-2 overflow-hidden']) }}
         style="background-color: {{ $bg }}; --color-t-accent: {{ $accent }}" aria-hidden="true">
        @if ($pattern)
            <span class="{{ $pattern }} absolute inset-0"></span>
        @endif
        <span class="absolute inset-3 rounded-xl border" style="border-color: {{ $accent }}; opacity: .55"></span>
        <span class="relative h-16 w-12 {{ $shape }}" style="background-color: {{ $accent }}; opacity: .35"></span>
        <span class="relative text-[9px] tracking-[0.3em] uppercase" style="color: {{ $accent }}">The Wedding of</span>
        <span @class([
            'relative leading-tight',
            'text-3xl' => $heading === 'script',
            'text-xl font-semibold' => $heading === 'serif',
            'text-base font-semibold tracking-[0.2em] uppercase' => $heading === 'caps',
        ]) style="color: {{ $primary }}; font-family: {{ $font }}">Raka &amp; Nadia</span>
        <span class="relative h-px w-16" style="background-color: {{ $accent }}"></span>
        <span class="relative mt-1 rounded-full px-3 py-1 text-[10px] font-semibold" style="background-color: {{ $primary }}; color: {{ $bg }}">Buka Undangan</span>
    </div>
@endif
