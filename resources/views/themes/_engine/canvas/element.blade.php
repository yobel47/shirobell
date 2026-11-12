{{-- One canvas element on an artboard of width $W and height $H (ADR 012). Texts are escaped. --}}
@php
    $canvasService = app(\App\Services\CanvasService::class);
    $c = fn (string $color) => \App\Services\CanvasService::css($color);
    $u = fn (float $n) => round($n / $W * 100, 4).'cqw';
    $p = $e['props'];
    $box = sprintf('left:%s%%;top:%s%%;width:%s%%;height:%s%%;transform:rotate(%sdeg)',
        round($e['x'] / $W * 100, 4), round($e['y'] / $H * 100, 4), round($e['w'] / $W * 100, 4), round($e['h'] / $H * 100, 4), $e['r']);
@endphp
<div class="absolute" style="{{ $box }}">
    @switch($e['type'])
        @case('text')
            <p @class(['h-full w-full leading-[1.15] whitespace-pre-line', ['heading' => 'font-heading', 'script' => 'font-script', 'body' => 'font-sans'][$p['font']], 'italic' => $p['italic'] ?? false])
               style="color: {{ $c($p['color']) }}; font-size: {{ $u($p['size']) }}; text-align: {{ $p['align'] }}; font-weight: {{ $p['weight'] }}; letter-spacing: {{ $p['spacing'] }}em">{{ $canvasService->resolve($p['text'], $invitation, $dateLabel, $guestName) }}</p>
            @break
        @case('image')
            <img src="{{ $canvasService->imageUrl($p['path']) }}" alt="" class="h-full w-full" loading="lazy"
                 style="object-fit: {{ $p['fit'] }}; border-radius: {{ $u($p['radius']) }}; opacity: {{ $p['opacity'] }}; border: {{ $u($p['border']) }} solid {{ $c($p['borderColor']) }}">
            @break
        @case('sticker')
            <div class="h-full w-full" style="opacity: {{ $p['opacity'] }}; transform: scale({{ $p['flipX'] ? -1 : 1 }}, {{ $p['flipY'] ? -1 : 1 }})">
                @include('themes._engine.canvas.sticker', ['set' => $p['set'], 'key' => $p['key']])
            </div>
            @break
        @case('shape')
            @php
                $radius = match ($p['shape']) { 'circle' => '9999px', 'arch' => '999px 999px 0 0', default => $u($p['radius']) };
                $shapeStyle = $p['shape'] === 'line'
                    ? "background-color: {$c($p['stroke'])}; opacity: {$p['opacity']}"
                    : "background-color: {$c($p['fill'])}; opacity: {$p['opacity']}; border: {$u($p['strokeWidth'])} solid {$c($p['stroke'])}; border-radius: {$radius}";
            @endphp
            <div class="h-full w-full" style="{{ $shapeStyle }}"></div>
            @break
        @case('button')
            <button type="button" x-on:click="open()" class="flex h-full w-full items-center justify-center font-semibold shadow-sm transition hover:opacity-90"
                    style="background-color: {{ $c($p['color']) }}; color: {{ $c($p['textColor']) }}; font-size: {{ $u($p['size']) }}; border-radius: {{ $u($p['radius']) }}">{{ $p['text'] }}</button>
            @break
    @endswitch
</div>
