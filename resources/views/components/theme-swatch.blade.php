@props(['theme'])
@php
    $palettes = [
        'rose-garden' => ['bg' => 'bg-gradient-to-br from-rose-50 via-rose-100 to-rose-200', 'text' => 'text-rose-800', 'line' => 'bg-rose-300'],
        'classic-gold' => ['bg' => 'bg-gradient-to-br from-stone-50 via-amber-50 to-stone-100', 'text' => 'text-stone-900', 'line' => 'bg-amber-600'],
        'sage-minimalist' => ['bg' => 'bg-gradient-to-br from-emerald-50 via-stone-50 to-emerald-100', 'text' => 'text-emerald-900', 'line' => 'bg-emerald-300'],
    ];
    $palette = $palettes[$theme->slug] ?? ['bg' => 'bg-rose-brand-50', 'text' => 'text-rose-brand-700', 'line' => 'bg-rose-brand-300'];
@endphp
@if ($theme->thumbnail_path)
    <img src="{{ Storage::disk('public')->url($theme->thumbnail_path) }}" alt="Tema {{ $theme->name }}" {{ $attributes->merge(['class' => 'w-full object-cover']) }}>
@else
    <div {{ $attributes->merge(['class' => 'flex w-full flex-col items-center justify-center gap-2 '.$palette['bg']]) }} aria-hidden="true">
        <span class="text-[10px] tracking-[0.3em] uppercase {{ $palette['text'] }} opacity-70">The Wedding of</span>
        <span class="font-serif text-2xl italic {{ $palette['text'] }}">Budi &amp; Ani</span>
        <span class="h-px w-16 {{ $palette['line'] }}"></span>
    </div>
@endif
