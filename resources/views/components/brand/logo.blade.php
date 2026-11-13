@props(['name' => null, 'size' => 'md'])
{{--
    Shirobell logo: bell mark plus wordmark. "Shirobell" is split into
    Shiro + bell (accent); any other site name renders as plain text.
--}}
@php
    $name = $name ?: config('app.name');
    [$markClass, $textClass] = match ($size) {
        'sm' => ['h-6 w-6', 'text-xl'],
        'lg' => ['h-10 w-10', 'text-4xl'],
        default => ['h-8 w-8', 'text-3xl'],
    };
    $split = strcasecmp($name, 'Shirobell') === 0;
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2']) }}>
    <x-brand.mark class="{{ $markClass }} shrink-0 text-rose-brand-500"/>
    <span class="font-logo {{ $textClass }} leading-none font-semibold tracking-wide text-[#3d2a2e]">@if ($split){{ substr($name, 0, 5) }}<span class="text-rose-brand-500">{{ substr($name, 5) }}</span>@else{{ $name }}@endif</span>
</span>
