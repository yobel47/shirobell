{{-- Free canvas page placed between sections (ADR 012). Full width of the invitation column. --}}
@php
    $W = \App\Services\CanvasService::WIDTH;
    $H = $page['height'];
@endphp
<section class="relative w-full overflow-hidden [container-type:inline-size]" aria-label="{{ $page['name'] }}"
         style="aspect-ratio: {{ $W }} / {{ $H }}; background-color: {{ \App\Services\CanvasService::css($page['bg']) }}">
    @if ($page['pattern'] && $pattern)
        <div class="{{ $pattern }} absolute inset-0"></div>
    @endif
    @foreach ($page['elements'] as $e)
        @continue($e['hidden'])
        @include('themes._engine.canvas.element')
    @endforeach
</section>
