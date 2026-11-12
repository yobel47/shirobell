{{--
    Canvas cover (ADR 012). Elements are positioned in percentages of the
    390 x 844 artboard and sized with container query units, so the design
    keeps its proportions on every screen. Replaces the recipe cover.
--}}
@php
    $canvas = app(\App\Services\CanvasService::class);
    $cssColor = fn (string $c) => \App\Services\CanvasService::css($c);
    $W = \App\Services\CanvasService::WIDTH;
    $H = \App\Services\CanvasService::HEIGHT;
    $box = fn (array $e) => sprintf('left:%s%%;top:%s%%;width:%s%%;height:%s%%;transform:rotate(%sdeg)',
        round($e['x'] / $W * 100, 4), round($e['y'] / $H * 100, 4), round($e['w'] / $W * 100, 4), round($e['h'] / $H * 100, 4), $e['r']);
    $fontClass = ['heading' => 'font-heading', 'script' => 'font-script', 'body' => 'font-sans'];
@endphp
<section x-show="!opened" @if ($skipCover) x-cloak @endif x-transition:leave="transition duration-700" x-transition:leave-end="-translate-y-full opacity-0"
         class="fixed inset-0 z-40 flex items-center justify-center overflow-hidden" style="background-color: {{ $cssColor($cover['bg']) }}">
    <div class="relative overflow-hidden [container-type:inline-size]"
         style="width: min(100vw, calc(100dvh * {{ $W }} / {{ $H }})); aspect-ratio: {{ $W }} / {{ $H }}; background-color: {{ $cssColor($cover['bg']) }}">
        @if (($cover['pattern'] ?? false) && $pattern)
            <div class="{{ $pattern }} absolute inset-0"></div>
        @endif
        <div class="t-grain absolute inset-0"></div>
        @foreach ($cover['elements'] as $e)
            @continue($e['hidden'])
            @php $p = $e['props']; @endphp
            <div class="absolute" style="{{ $box($e) }}">
                @switch($e['type'])
                    @case('text')
                        <p @class(['h-full w-full leading-[1.15] whitespace-pre-line', $fontClass[$p['font']], 'italic' => $p['italic'] ?? false])
                           style="color: {{ $cssColor($p['color']) }}; font-size: {{ round($p['size'] / $W * 100, 4) }}cqw; text-align: {{ $p['align'] }}; font-weight: {{ $p['weight'] }}; letter-spacing: {{ $p['spacing'] }}em">{{ $canvas->resolve($p['text'], $invitation, $dateLabel, $guestName) }}</p>
                        @break
                    @case('image')
                        <img src="{{ $canvas->imageUrl($p['path']) }}" alt="" class="h-full w-full"
                             style="object-fit: {{ $p['fit'] }}; border-radius: {{ round($p['radius'] / $W * 100, 4) }}cqw; opacity: {{ $p['opacity'] }}; border: {{ round($p['border'] / $W * 100, 4) }}cqw solid {{ $cssColor($p['borderColor']) }}">
                        @break
                    @case('sticker')
                        <div class="h-full w-full" style="opacity: {{ $p['opacity'] }}; transform: scale({{ $p['flipX'] ? -1 : 1 }}, {{ $p['flipY'] ? -1 : 1 }})">
                            @include('themes._engine.canvas.sticker', ['set' => $p['set'], 'key' => $p['key']])
                        </div>
                        @break
                    @case('shape')
                        <div class="h-full w-full" style="background-color: {{ $cssColor($p['fill']) }}; opacity: {{ $p['opacity'] }};
                            border: {{ round($p['strokeWidth'] / $W * 100, 4) }}cqw solid {{ $cssColor($p['stroke']) }};
                            border-radius: {{ match ($p['shape']) { 'circle' => '9999px', 'arch' => '999px 999px 0 0', default => round($p['radius'] / $W * 100, 4).'cqw' } }}
                            @if ($p['shape'] === 'line'); background-color: {{ $cssColor($p['stroke']) }}; border: 0 @endif"></div>
                        @break
                    @case('button')
                        <button type="button" x-on:click="open()" class="flex h-full w-full items-center justify-center font-semibold shadow-sm transition hover:opacity-90"
                                style="background-color: {{ $cssColor($p['color']) }}; color: {{ $cssColor($p['textColor']) }}; font-size: {{ round($p['size'] / $W * 100, 4) }}cqw; border-radius: {{ round($p['radius'] / $W * 100, 4) }}cqw">{{ $p['text'] }}</button>
                        @break
                @endswitch
            </div>
        @endforeach
    </div>
</section>
