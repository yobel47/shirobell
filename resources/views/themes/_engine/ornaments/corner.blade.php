{{--
    Top-left corner ornament (viewBox 0 0 120 120), drawn for each ornament
    family. corners.blade.php mirrors it into the other three corners.
    Colors come from the theme tokens, so every variant recolors it.
--}}
<g fill="none" class="stroke-t-accent" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
@switch($family)
    @case('floral')
        <path d="M44 19 C68 13 94 9 118 7"/>
        <path d="M19 44 C13 68 9 94 7 118"/>
        <path class="fill-t-accent/25" d="M60 14 C67 4 80 3 87 6 C80 13 70 16 60 14Z"/>
        <path class="fill-t-accent/25" d="M88 10 C96 17 105 17 112 13 C105 6 95 5 88 10Z"/>
        <path class="fill-t-accent/25" d="M74 12 C79 20 88 23 95 21 C90 14 82 11 74 12Z"/>
        <path class="fill-t-accent/25" d="M14 60 C4 67 3 80 6 87 C13 80 16 70 14 60Z"/>
        <path class="fill-t-accent/25" d="M10 88 C17 96 17 105 13 112 C6 105 5 95 10 88Z"/>
        <path class="fill-t-accent/25" d="M12 74 C20 79 23 88 21 95 C14 90 11 82 12 74Z"/>
        <path class="fill-t-accent/30" d="M27 6 C40 4 51 14 49 25 C58 31 52 47 39 47 C33 56 16 52 14 39 C3 35 5 18 17 15 C19 9 23 6 27 6Z"/>
        <path d="M30 18 C38 17 41 25 36 30 C32 34 25 31 26 25 C27 21 31 21 32 24"/>
        <path d="M21 21 C17 30 21 41 32 42 C41 43 46 36 45 29"/>
        <path d="M25 13 C34 11 43 15 45 22"/>
        <path d="M16 27 C14 34 16 41 21 44"/>
        <circle class="fill-t-accent/30" cx="57" cy="23" r="7.5"/>
        <path d="M54 22 C56 18 61 19 61 23 C61 26 56 27 55 24"/>
        <circle class="fill-t-accent/30" cx="23" cy="57" r="7.5"/>
        <path d="M22 54 C18 56 19 61 23 61 C26 61 27 56 24 55"/>
        <g class="fill-t-primary/60" stroke="none">
            <circle cx="100" cy="20" r="1.8"/><circle cx="105" cy="23" r="1.3"/><circle cx="96" cy="24" r="1.2"/>
            <circle cx="20" cy="100" r="1.8"/><circle cx="23" cy="105" r="1.3"/><circle cx="24" cy="96" r="1.2"/>
            <circle cx="47" cy="47" r="1.6"/>
        </g>
        @break
    @case('leaf')
        <path d="M4 4 C34 9 70 8 117 4"/>
        <path d="M4 4 C9 34 8 70 4 117"/>
        @foreach ([[26, 11, -24], [44, 1, 22], [62, 11, -18], [80, 2, 20], [98, 9, -14]] as [$x, $y, $r])
            <ellipse class="fill-t-accent/30" cx="{{ $x }}" cy="{{ $y + 2 }}" rx="8" ry="4.6" transform="rotate({{ $r }} {{ $x }} {{ $y + 2 }})"/>
            <ellipse class="fill-t-accent/30" cx="{{ $y + 2 }}" cy="{{ $x }}" rx="4.6" ry="8" transform="rotate({{ -$r }} {{ $y + 2 }} {{ $x }})"/>
        @endforeach
        <path class="fill-t-primary/15" d="M6 6 C27 10 40 23 42 42 C23 40 10 27 6 6Z"/>
        <path d="M6 6 L42 42 M16 16 L26 12 M24 24 L36 22 M16 16 L12 26 M24 24 L22 36"/>
        <path class="fill-t-accent/30" d="M9 6 C26 2 43 6 53 17 C38 19 21 15 9 6Z"/>
        <path class="fill-t-accent/30" d="M6 9 C2 26 6 43 17 53 C19 38 15 21 6 9Z"/>
        @break
    @case('kawung')
        <path d="M2 2 H118 M2 2 V118" stroke-width="1.6"/>
        <path d="M8 8 H118 M8 8 V118" stroke-width="0.6"/>
        @foreach ([[32, 32, 1], [72, 18, 0.45], [100, 18, 0.45], [18, 72, 0.45], [18, 100, 0.45]] as [$cx, $cy, $s])
            <g transform="translate({{ $cx }} {{ $cy }}) scale({{ $s }})">
                <g class="fill-t-accent/25">
                    <ellipse cx="0" cy="-13" rx="8" ry="12"/><ellipse cx="0" cy="13" rx="8" ry="12"/>
                    <ellipse cx="-13" cy="0" rx="12" ry="8"/><ellipse cx="13" cy="0" rx="12" ry="8"/>
                </g>
                <g class="fill-t-primary/70" stroke="none">
                    <circle cx="0" cy="0" r="3"/><circle cx="0" cy="-14" r="1.8"/><circle cx="0" cy="14" r="1.8"/><circle cx="-14" cy="0" r="1.8"/><circle cx="14" cy="0" r="1.8"/>
                </g>
            </g>
        @endforeach
        <path d="M50 50 l5 -5 l5 5 l-5 5z" class="fill-t-accent/40"/>
        @break
    @case('parang')
        <path d="M2 2 H118 M2 2 V118" stroke-width="1.6"/>
        <path d="M2 74 L74 2 M2 44 L44 2"/>
        @foreach ([[0, 0], [16, -16], [32, -32]] as [$dx, $dy])
            <g transform="translate({{ $dx }} {{ $dy }})">
                <path class="fill-t-accent/30" d="M8 64 C16 50 30 50 36 38 C27 43 17 43 8 64Z"/>
                <path class="fill-t-primary/20" d="M14 70 C22 58 34 58 40 46"/>
                <circle class="fill-t-primary/70" stroke="none" cx="26" cy="62" r="2"/>
            </g>
        @endforeach
        <path class="fill-t-accent/30" d="M56 56 l4 -4 l4 4 l-4 4z M86 26 l3 -3 l3 3 l-3 3z M26 86 l3 -3 l3 3 l-3 3z"/>
        <path d="M84 10 C92 4 104 6 116 4 M10 84 C4 92 6 104 4 116" stroke-width="0.7"/>
        @break
    @case('cloud')
        <path class="fill-t-primary/10" stroke="none" d="M0 0 H70 C66 12 56 14 50 20 C44 28 34 26 28 32 C22 40 14 38 8 46 C4 54 0 58 0 70Z"/>
        @foreach ([[62, 'stroke-t-primary/25', 7], [50, 'stroke-t-accent/40', 5], [38, 'stroke-t-primary/20', 4]] as [$r, $cls, $w])
            @php $k = $r / 62; @endphp
            <path class="{{ $cls }}" stroke-width="{{ $w }}" d="M2 {{ 62 * $k }} C6 {{ 50 * $k }} {{ 18 * $k }} {{ 50 * $k }} {{ 20 * $k }} {{ 57 * $k }} C20 {{ 42 * $k }} {{ 34 * $k }} {{ 38 * $k }} {{ 38 * $k }} {{ 46 * $k }} C38 {{ 32 * $k }} {{ 52 * $k }} {{ 26 * $k }} {{ 56 * $k }} {{ 36 * $k }} C56 {{ 20 * $k }} {{ 64 * $k }} {{ 14 * $k }} {{ 64 * $k }} 2"/>
        @endforeach
        <path d="M2 62 C6 50 18 50 20 57 C20 42 34 38 38 46 C38 32 52 26 56 36 C56 20 64 14 64 2"/>
        <path d="M2 38 C5 31 11 31 12 35 C12 26 21 23 23 28 C23 20 32 16 34 22 C35 12 39 9 40 2"/>
        <path class="fill-t-accent/30" d="M78 14 C74 6 84 2 90 8 C96 2 106 6 102 14 C108 18 102 26 96 22 C90 26 80 24 82 18 C76 18 74 16 78 14Z"/>
        <path d="M84 14 C84 10 90 10 90 14 C90 17 86 17 86 15"/>
        <path class="fill-t-accent/30" d="M14 78 C6 74 2 84 8 90 C2 96 6 106 14 102 C18 108 26 102 22 96 C26 90 24 80 18 82 C18 76 16 74 14 78Z"/>
        <path d="M14 84 C10 84 10 90 14 90 C17 90 17 86 15 86"/>
        @break
    @case('lotus')
        <path d="M2 2 H118 M2 2 V118" stroke-width="0.8"/>
        <g transform="rotate(45 30 30)">
            <path class="fill-t-accent/30" d="M30 6 C38 16 38 32 30 44 C22 32 22 16 30 6Z"/>
            <path class="fill-t-accent/25" d="M30 44 C18 40 10 28 12 16 C22 20 28 32 30 44Z M30 44 C42 40 50 28 48 16 C38 20 32 32 30 44Z"/>
            <path class="fill-t-accent/25" d="M30 46 C14 48 4 40 2 30 C14 30 24 36 30 46Z M30 46 C46 48 56 40 58 30 C46 30 36 36 30 46Z"/>
            <path d="M30 14 V40"/>
        </g>
        @foreach ([56, 84] as $x)
            <path d="M{{ $x }} 8 C{{ $x + 10 }} 0 {{ $x + 22 }} 6 {{ $x + 20 }} 14 C{{ $x + 18 }} 20 {{ $x + 10 }} 18 {{ $x + 12 }} 13"/>
            <path d="M8 {{ $x }} C0 {{ $x + 10 }} 6 {{ $x + 22 }} 14 {{ $x + 20 }} C20 {{ $x + 18 }} 18 {{ $x + 10 }} 13 {{ $x + 12 }}"/>
            <circle class="fill-t-primary/60" stroke="none" cx="{{ $x + 15 }}" cy="14" r="1.6"/>
            <circle class="fill-t-primary/60" stroke="none" cx="14" cy="{{ $x + 15 }}" r="1.6"/>
        @endforeach
        @break
    @case('tenun')
        <rect x="2" y="2" width="22" height="22" class="fill-t-primary/30"/>
        <path d="M13 13 m-6 0 a6 6 0 1 0 12 0 a4 4 0 1 0 -8 0 a2 2 0 1 0 4 0" class="stroke-t-on"/>
        @for ($x = 24; $x < 118; $x += 12)
            <path class="{{ ($x / 12) % 2 ? 'fill-t-primary/35' : 'fill-t-accent/30' }}" d="M{{ $x }} 2 L{{ $x + 6 }} 13 L{{ $x + 12 }} 2Z"/>
            <path class="{{ ($x / 12) % 2 ? 'fill-t-primary/35' : 'fill-t-accent/30' }}" d="M2 {{ $x }} L13 {{ $x + 6 }} L2 {{ $x + 12 }}Z"/>
        @endfor
        <path d="M24 13 H118 M13 24 V118" stroke-width="0.7"/>
        @for ($x = 30; $x < 118; $x += 8)
            <circle class="fill-t-accent/70" stroke="none" cx="{{ $x }}" cy="19" r="1"/>
            <circle class="fill-t-accent/70" stroke="none" cx="19" cy="{{ $x }}" r="1"/>
        @endfor
        <path d="M2 24 H24 V2" stroke-width="1.6"/>
        @break
    @case('songket')
        <path d="M2 2 H118 M2 2 V118" stroke-width="1.6"/>
        @for ($x = 28; $x < 112; $x += 16)
            <path class="fill-t-accent/30" d="M{{ $x }} 4 L{{ $x + 8 }} 26 L{{ $x + 16 }} 4Z"/>
            <path class="fill-t-primary/30" stroke="none" d="M{{ $x + 4 }} 4 L{{ $x + 8 }} 15 L{{ $x + 12 }} 4Z"/>
            <path class="fill-t-accent/30" d="M4 {{ $x }} L26 {{ $x + 8 }} L4 {{ $x + 16 }}Z"/>
            <path class="fill-t-primary/30" stroke="none" d="M4 {{ $x + 4 }} L15 {{ $x + 8 }} L4 {{ $x + 12 }}Z"/>
        @endfor
        <path class="fill-t-accent/30" d="M16 2 L30 16 L16 30 L2 16Z"/>
        <path class="fill-t-primary/50" stroke="none" d="M16 9 L23 16 L16 23 L9 16Z"/>
        <path d="M28 28 L40 40 M40 40 l4 -4 l4 4 l-4 4z" class="fill-t-accent/40"/>
        @break
    @case('deco')
        <path d="M2 2 H118 M2 2 V118" stroke-width="1.6"/>
        <path d="M10 10 H96 M10 10 V96"/>
        <path d="M18 18 H64 M18 18 V64" stroke-width="0.7"/>
        <path d="M2 40 A38 38 0 0 0 40 2"/>
        <path d="M2 30 A28 28 0 0 0 30 2" class="fill-t-accent/25"/>
        <path d="M2 2 L28 28 M2 2 L14 30 M2 2 L30 14" stroke-width="0.7"/>
        <path d="M64 18 V26 H72 V34 M18 64 H26 V72 H34" stroke-width="0.8"/>
        <path class="fill-t-accent" stroke="none" d="M96 10 l3 -3 l3 3 l-3 3z M10 96 l-3 3 l3 3 l3 -3z"/>
        <path class="fill-t-accent/60" stroke="none" d="M72 34 l2 -2 l2 2 l-2 2z M34 72 l-2 2 l2 2 l2 -2z"/>
        @break
    @case('star')
        <path d="M2 2 H118 M2 2 V118" stroke-width="1.4"/>
        <path d="M8 8 H118 M8 8 V118" stroke-width="0.6"/>
        <g transform="translate(30 30)">
            <rect x="-14" y="-14" width="28" height="28" class="fill-t-accent/30"/>
            <rect x="-14" y="-14" width="28" height="28" transform="rotate(45)" class="fill-t-accent/30"/>
            <circle r="7" class="fill-t-accent/30"/>
            <circle r="2.5" class="fill-t-primary/70" stroke="none"/>
        </g>
        @foreach ([60, 90] as $x)
            <path d="M{{ $x - 12 }} 8 C{{ $x - 12 }} 18 {{ $x - 6 }} 22 {{ $x }} 22 C{{ $x + 6 }} 22 {{ $x + 12 }} 18 {{ $x + 12 }} 8"/>
            <path d="M8 {{ $x - 12 }} C18 {{ $x - 12 }} 22 {{ $x - 6 }} 22 {{ $x }} C22 {{ $x + 6 }} 18 {{ $x + 12 }} 8 {{ $x + 12 }}"/>
            <g transform="translate({{ $x }} 14) scale(.25)"><rect x="-12" y="-12" width="24" height="24" class="fill-t-accent/40"/><rect x="-12" y="-12" width="24" height="24" transform="rotate(45)" class="fill-t-accent/40"/></g>
            <g transform="translate(14 {{ $x }}) scale(.25)"><rect x="-12" y="-12" width="24" height="24" class="fill-t-accent/40"/><rect x="-12" y="-12" width="24" height="24" transform="rotate(45)" class="fill-t-accent/40"/></g>
        @endforeach
        @break
    @case('wave')
        @foreach ([[0, 0], [24, 0], [0, 24]] as [$dx, $dy])
            <g transform="translate({{ $dx }} {{ $dy }})">
                <path class="fill-t-accent/30" d="M0 24 A24 24 0 0 1 48 24 Z"/>
                <path d="M0 24 A24 24 0 0 1 48 24 M6 24 A18 18 0 0 1 42 24 M12 24 A12 12 0 0 1 36 24 M18 24 A6 6 0 0 1 30 24"/>
            </g>
        @endforeach
        <path d="M72 8 C80 2 88 2 96 8 S112 14 118 8 M8 72 C2 80 2 88 8 96 S14 112 8 118"/>
        <path d="M76 16 C82 12 88 12 94 16 M16 76 C12 82 12 88 16 94" stroke-width="0.7"/>
        <circle class="fill-t-primary/50" stroke="none" cx="62" cy="40" r="2"/><circle class="fill-t-primary/50" stroke="none" cx="40" cy="62" r="2"/>
        @break
    @default
        <path d="M2 40 V2 H40" stroke-width="1.4"/>
        <path d="M9 56 V9 H56" stroke-width="0.6"/>
        <path d="M9 9 C22 22 34 16 30 9 C26 3 18 8 22 14"/>
        <path d="M56 9 C70 9 78 4 92 6 M9 56 C9 70 4 78 6 92" stroke-width="0.7"/>
        <path class="fill-t-accent" stroke="none" d="M40 2 l3 -2 l3 2 l-3 2z M2 40 l-2 3 l2 3 l2 -3z"/>
@endswitch
</g>
