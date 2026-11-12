{{--
    Canvas cover (ADR 012). Elements are positioned in percentages of the
    390 x 844 artboard and sized with container query units, so the design
    keeps its proportions on every screen. Replaces the recipe cover.
--}}
@php
    $W = \App\Services\CanvasService::WIDTH;
    $H = \App\Services\CanvasService::HEIGHT;
    $bgColor = \App\Services\CanvasService::css($cover['bg']);
@endphp
<section x-show="!opened" @if ($skipCover) x-cloak @endif x-transition:leave="transition duration-700" x-transition:leave-end="-translate-y-full opacity-0"
         class="fixed inset-0 z-40 flex items-center justify-center overflow-hidden" style="background-color: {{ $bgColor }}">
    <div class="relative overflow-hidden [container-type:inline-size]"
         style="width: min(100vw, calc(100dvh * {{ $W }} / {{ $H }})); aspect-ratio: {{ $W }} / {{ $H }}; background-color: {{ $bgColor }}">
        @if (($cover['pattern'] ?? false) && $pattern)
            <div class="{{ $pattern }} absolute inset-0"></div>
        @endif
        <div class="t-grain absolute inset-0"></div>
        @foreach ($cover['elements'] as $e)
            @continue($e['hidden'])
            @include('themes._engine.canvas.element')
        @endforeach
    </div>
</section>
