{{-- Centered crown ornament above names (viewBox 0 0 240 70). --}}
@php $family = $engine['ornament']; @endphp
<div class="flex justify-center" aria-hidden="true">
    <svg viewBox="0 0 240 70" class="{{ $class ?? 'h-14 w-48' }}">
        <g fill="none" class="stroke-t-accent" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
        @switch($family)
            @case('floral')
                <path d="M120 40 C96 30 70 34 40 50 C30 56 18 56 8 50 M120 40 C144 30 170 34 200 50 C210 56 222 56 232 50"/>
                @foreach ([[60, 38, -20], [86, 32, -10], [154, 32, 10], [180, 38, 20]] as [$x, $y, $r])
                    <path class="fill-t-accent/25" transform="rotate({{ $r }} {{ $x }} {{ $y }})" d="M{{ $x - 10 }} {{ $y }} C{{ $x - 4 }} {{ $y - 9 }} {{ $x + 6 }} {{ $y - 9 }} {{ $x + 10 }} {{ $y }} C{{ $x + 4 }} {{ $y + 6 }} {{ $x - 4 }} {{ $y + 6 }} {{ $x - 10 }} {{ $y }}Z"/>
                @endforeach
                <path class="fill-t-accent/30" d="M120 12 C132 12 140 22 138 30 C146 36 140 50 128 50 C124 58 112 58 108 50 C96 50 92 36 100 30 C98 22 108 12 120 12Z"/>
                <path d="M120 24 C127 24 129 31 124 35 C120 38 114 35 115 30 C116 27 120 27 121 29"/>
                <path d="M108 30 C106 40 112 46 122 46 C130 46 134 40 133 34"/>
                <circle class="fill-t-accent/30" cx="96" cy="44" r="6"/><circle class="fill-t-accent/30" cx="144" cy="44" r="6"/>
                <g class="fill-t-primary/60" stroke="none"><circle cx="40" cy="44" r="1.6"/><circle cx="200" cy="44" r="1.6"/><circle cx="74" cy="48" r="1.4"/><circle cx="166" cy="48" r="1.4"/></g>
                @break
            @case('leaf')
                <path d="M120 58 C120 40 100 22 70 16 C50 12 30 16 12 24 M120 58 C120 40 140 22 170 16 C190 12 210 16 228 24"/>
                @foreach ([[40, 16], [62, 14], [84, 18], [104, 30]] as $i => [$x, $y])
                    <ellipse class="fill-t-accent/30" cx="{{ $x }}" cy="{{ $y - 6 }}" rx="8" ry="4.5" transform="rotate({{ -20 + $i * 12 }} {{ $x }} {{ $y - 6 }})"/>
                    <ellipse class="fill-t-accent/30" cx="{{ 240 - $x }}" cy="{{ $y - 6 }}" rx="8" ry="4.5" transform="rotate({{ 20 - $i * 12 }} {{ 240 - $x }} {{ $y - 6 }})"/>
                    <ellipse class="fill-t-primary/10" cx="{{ $x + 4 }}" cy="{{ $y + 6 }}" rx="7" ry="4" transform="rotate({{ 15 + $i * 8 }} {{ $x + 4 }} {{ $y + 6 }})"/>
                    <ellipse class="fill-t-primary/10" cx="{{ 236 - $x }}" cy="{{ $y + 6 }}" rx="7" ry="4" transform="rotate({{ -15 - $i * 8 }} {{ 236 - $x }} {{ $y + 6 }})"/>
                @endforeach
                <circle class="fill-t-accent" stroke="none" cx="120" cy="60" r="3"/>
                @break
            @case('kawung')
                <path d="M10 35 H82 M158 35 H230" stroke-width="1.4"/><path d="M10 41 H82 M158 41 H230" stroke-width="0.6"/>
                <g transform="translate(120 36)">
                    <g class="fill-t-accent/25"><ellipse cx="0" cy="-15" rx="10" ry="15"/><ellipse cx="0" cy="15" rx="10" ry="15"/><ellipse cx="-15" cy="0" rx="15" ry="10"/><ellipse cx="15" cy="0" rx="15" ry="10"/></g>
                    <g class="fill-t-primary/70" stroke="none"><circle r="3.5"/><circle cy="-17" r="2"/><circle cy="17" r="2"/><circle cx="-17" r="2"/><circle cx="17" r="2"/></g>
                </g>
                @foreach ([94, 146] as $x)
                    <g transform="translate({{ $x }} 38) scale(.4)"><g class="fill-t-accent/30"><ellipse cy="-13" rx="8" ry="12"/><ellipse cy="13" rx="8" ry="12"/><ellipse cx="-13" rx="12" ry="8"/><ellipse cx="13" rx="12" ry="8"/></g></g>
                @endforeach
                @break
            @case('parang')
                <path d="M10 36 H70 M170 36 H230" stroke-width="1.2"/>
                @foreach ([0, 22, 44, 66] as $i => $dx)
                    <path class="{{ $i % 2 ? 'fill-t-primary/25' : 'fill-t-accent/30' }}" d="M{{ 78 + $dx }} 56 C{{ 82 + $dx }} 44 {{ 90 + $dx }} 40 {{ 94 + $dx }} 32 C{{ 98 + $dx }} 24 {{ 98 + $dx }} 18 {{ 100 + $dx }} 12 C{{ 92 + $dx }} 20 {{ 86 + $dx }} 26 {{ 84 + $dx }} 34 C{{ 82 + $dx }} 42 {{ 78 + $dx }} 48 {{ 78 + $dx }} 56Z"/>
                @endforeach
                <path class="fill-t-accent" stroke="none" d="M70 36 l4 -4 l4 4 l-4 4z M162 36 l4 -4 l4 4 l-4 4z"/>
                @break
            @case('cloud')
                <path class="fill-t-primary/15" d="M60 54 C56 40 72 32 82 40 C84 24 104 18 114 30 C122 16 146 18 148 34 C160 28 176 36 172 50 C184 50 186 60 178 62 H62 C52 62 52 54 60 54Z"/>
                <path class="stroke-t-accent/50" stroke-width="4" d="M74 56 C72 46 82 42 88 47 C90 36 104 32 110 40 C116 30 132 32 134 42 C142 38 152 44 150 54"/>
                <path d="M86 58 C86 52 92 50 96 54 C98 46 108 44 112 50 C116 44 126 46 128 52 C132 50 138 52 138 58"/>
                <path d="M10 56 C24 46 40 50 52 56 M230 56 C216 46 200 50 188 56"/>
                @break
            @case('lotus')
                <path class="fill-t-accent/30" d="M120 6 C130 18 130 38 120 54 C110 38 110 18 120 6Z"/>
                <path class="fill-t-accent/25" d="M120 54 C104 50 94 36 96 20 C110 24 118 38 120 54Z M120 54 C136 50 146 36 144 20 C130 24 122 38 120 54Z"/>
                <path class="fill-t-accent/25" d="M120 56 C98 60 82 50 78 36 C96 36 110 44 120 56Z M120 56 C142 60 158 50 162 36 C144 36 130 44 120 56Z"/>
                @foreach ([[60, 1], [180, -1]] as [$x, $d])
                    <path d="M{{ 120 - 46 * $d }} 58 C{{ $x }} 58 {{ $x - 20 * $d }} 48 {{ $x - 24 * $d }} 38 C{{ $x - 28 * $d }} 28 {{ $x - 40 * $d }} 30 {{ $x - 38 * $d }} 40 C{{ $x - 36 * $d }} 46 {{ $x - 28 * $d }} 44 {{ $x - 30 * $d }} 39"/>
                    <path d="M{{ $x - 30 * $d }} 58 C{{ $x - 46 * $d }} 58 {{ $x - 56 * $d }} 50 {{ $x - 50 * $d }} 42"/>
                @endforeach
                <path d="M120 16 V48"/>
                @break
            @case('tenun')
                <path d="M10 22 H230 M10 50 H230" stroke-width="1.4"/>
                @for ($x = 10; $x < 230; $x += 14)
                    <path class="{{ (($x - 10) / 14) % 2 ? 'fill-t-primary/35' : 'fill-t-accent/30' }}" d="M{{ $x }} 22 L{{ $x + 7 }} 36 L{{ $x + 14 }} 22Z"/>
                    <path class="{{ (($x - 10) / 14) % 2 ? 'fill-t-accent/30' : 'fill-t-primary/35' }}" d="M{{ $x }} 50 L{{ $x + 7 }} 36 L{{ $x + 14 }} 50Z"/>
                @endfor
                <rect x="106" y="22" width="28" height="28" class="fill-t-primary/40"/>
                <path d="M120 36 m-8 0 a8 8 0 1 0 16 0 a5 5 0 1 0 -10 0 a2 2 0 1 0 4 0" class="stroke-t-on"/>
                @break
            @case('songket')
                <path d="M10 10 H230" stroke-width="1.4"/>
                @for ($x = 10; $x < 230; $x += 20)
                    @php $big = abs($x + 10 - 120) < 30; @endphp
                    <path class="fill-t-accent/30" d="M{{ $x }} 12 L{{ $x + 10 }} {{ $big ? 62 : 40 }} L{{ $x + 20 }} 12Z"/>
                    <path class="fill-t-primary/30" stroke="none" d="M{{ $x + 5 }} 12 L{{ $x + 10 }} {{ $big ? 40 : 26 }} L{{ $x + 15 }} 12Z"/>
                @endfor
                @break
            @case('deco')
                <path d="M10 60 H230 M30 54 H210" stroke-width="1"/>
                <path class="fill-t-accent/25" d="M80 60 A40 40 0 0 1 160 60Z"/>
                <path d="M80 60 A40 40 0 0 1 160 60 M92 60 A28 28 0 0 1 148 60 M104 60 A16 16 0 0 1 136 60"/>
                @foreach ([-60, -30, 0, 30, 60] as $a)
                    <path d="M120 60 L{{ 120 + 40 * sin(deg2rad($a)) }} {{ 60 - 40 * cos(deg2rad($a)) }}" stroke-width="0.7"/>
                @endforeach
                <path d="M120 4 V16 M114 10 H126" stroke-width="0.8"/><path class="fill-t-accent" stroke="none" d="M120 0 l3 3 l-3 3 l-3 -3z"/>
                <path class="fill-t-accent" stroke="none" d="M30 54 l3 -3 l3 3 l-3 3z M204 54 l3 -3 l3 3 l-3 3z"/>
                @break
            @case('star')
                <path class="fill-t-accent/10" d="M86 66 V40 C86 22 102 8 120 4 C138 8 154 22 154 40 V66"/>
                <path d="M94 66 V42 C94 26 106 14 120 10 C134 14 146 26 146 42 V66"/>
                <g transform="translate(120 38)">
                    <rect x="-11" y="-11" width="22" height="22" class="fill-t-accent/30"/>
                    <rect x="-11" y="-11" width="22" height="22" transform="rotate(45)" class="fill-t-accent/30"/>
                    <circle r="4" class="fill-t-primary/70" stroke="none"/>
                </g>
                <path d="M10 60 C30 50 50 50 70 58 M230 60 C210 50 190 50 170 58"/>
                @foreach ([40, 200] as $x)
                    <path d="M{{ $x }} 52 V60" stroke-width="0.7"/><path class="fill-t-accent/40" d="M{{ $x - 4 }} 60 h8 l-1 8 h-6z"/>
                @endforeach
                @break
            @case('wave')
                @foreach ([96, 120, 144] as $i => $x)
                    <path class="fill-t-accent/30" d="M{{ $x - 24 }} 60 A24 24 0 0 1 {{ $x + 24 }} 60Z"/>
                    <path d="M{{ $x - 24 }} 60 A24 24 0 0 1 {{ $x + 24 }} 60 M{{ $x - 16 }} 60 A16 16 0 0 1 {{ $x + 16 }} 60 M{{ $x - 8 }} 60 A8 8 0 0 1 {{ $x + 8 }} 60"/>
                @endforeach
                <path d="M10 50 C22 42 34 42 46 50 S70 58 72 50 M230 50 C218 42 206 42 194 50 S170 58 168 50"/>
                @break
            @default
                <path d="M20 40 H100 M140 40 H220" stroke-width="0.8"/>
                <path d="M100 40 C108 28 114 28 120 40 C126 28 132 28 140 40"/>
                <path d="M100 40 C108 52 114 52 120 40 C126 52 132 52 140 40" stroke-width="0.7"/>
                <path class="fill-t-accent" stroke="none" d="M120 30 l4 4 l-4 4 l-4 -4z M20 40 l-3 -3 l-3 3 l3 3z M226 40 l-3 -3 l-3 3 l3 3z"/>
        @endswitch
        </g>
    </svg>
</div>
