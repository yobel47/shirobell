{{--
    Flat layered illustrations (viewBox 0 0 400 200), drawn for this project.
    Back layers use soft/accent tints, front layers the primary color, so every
    variant recolors the scene. $scene picks the illustration.
--}}
<svg viewBox="0 0 400 200" preserveAspectRatio="xMidYMax meet" class="{{ $class ?? 'w-full' }}" aria-hidden="true">
@switch($scene)
    @case('bouquet')
        @foreach ([[-1, 0], [1, 400]] as [$d, $o])
            <g transform="translate({{ $o }} 0) scale({{ $d }} 1)">
                <path class="fill-t-primary/25" d="M0 120 C30 90 70 100 90 140 C70 130 40 128 0 150Z"/>
                <path class="fill-t-accent/40" d="M10 200 C20 150 60 120 110 128 C80 140 50 165 40 200Z"/>
                <path class="fill-t-primary/30" d="M60 200 C70 165 110 150 150 160 C120 170 100 185 95 200Z"/>
                <path class="fill-t-accent/30" d="M0 170 C20 160 40 168 50 190 C35 182 15 182 0 188Z"/>
                <circle class="fill-t-accent/50" cx="58" cy="150" r="30"/>
                <circle class="fill-t-soft" cx="58" cy="150" r="22"/>
                <path class="fill-none stroke-t-primary/60" stroke-width="2" d="M58 136 C70 136 72 150 64 156 C56 162 46 156 48 148 C50 142 58 142 60 147 M42 150 C40 164 52 172 64 170 C74 168 80 160 78 150"/>
                <circle class="fill-t-primary/40" cx="105" cy="175" r="22"/>
                <circle class="fill-t-accent/40" cx="105" cy="175" r="15"/>
                <path class="fill-none stroke-t-surface/70" stroke-width="1.6" d="M105 165 C112 165 114 174 109 178 C104 182 98 178 99 173"/>
                <circle class="fill-t-soft" cx="18" cy="128" r="14"/>
                <circle class="fill-t-accent/50" cx="18" cy="128" r="7"/>
                @foreach ([[128, 140], [140, 128], [30, 105], [86, 118]] as [$x, $y])
                    <circle class="fill-t-primary/50" cx="{{ $x }}" cy="{{ $y }}" r="3"/><circle class="fill-t-primary/40" cx="{{ $x + 6 }}" cy="{{ $y - 4 }}" r="2"/>
                @endforeach
            </g>
        @endforeach
        @break
    @case('wildflowers')
        <path class="fill-t-accent/20" d="M0 170 C80 150 140 165 200 160 C270 154 330 150 400 168 V200 H0Z"/>
        @foreach ([20, 46, 70, 98, 128, 160, 240, 272, 300, 330, 356, 382] as $i => $x)
            @php $h = 60 + ($i * 37) % 70; $y = 196 - $h; @endphp
            <path class="fill-none stroke-t-primary/40" stroke-width="1.5" d="M{{ $x }} 200 C{{ $x - 4 }} {{ $y + $h / 2 }} {{ $x + 6 }} {{ $y + 20 }} {{ $x }} {{ $y }}"/>
            <path class="fill-t-accent/40" d="M{{ $x }} {{ $y + 30 }} C{{ $x - 14 }} {{ $y + 24 }} {{ $x - 12 }} {{ $y + 12 }} {{ $x - 2 }} {{ $y + 22 }}Z"/>
            @if ($i % 3 === 0)
                @foreach ([0, 72, 144, 216, 288] as $a)
                    <ellipse class="fill-t-primary/50" cx="{{ $x + 7 * cos(deg2rad($a)) }}" cy="{{ $y + 7 * sin(deg2rad($a)) }}" rx="5" ry="3.4" transform="rotate({{ $a }} {{ $x + 7 * cos(deg2rad($a)) }} {{ $y + 7 * sin(deg2rad($a)) }})"/>
                @endforeach
                <circle class="fill-t-accent" cx="{{ $x }}" cy="{{ $y }}" r="3"/>
            @elseif ($i % 3 === 1)
                <ellipse class="fill-t-accent/60" cx="{{ $x }}" cy="{{ $y }}" rx="5" ry="9"/><ellipse class="fill-t-accent/60" cx="{{ $x }}" cy="{{ $y + 12 }}" rx="4" ry="7"/>
            @else
                <circle class="fill-t-soft stroke-t-primary/40" cx="{{ $x }}" cy="{{ $y }}" r="7"/><circle class="fill-t-primary/50" cx="{{ $x }}" cy="{{ $y }}" r="3"/>
            @endif
        @endforeach
        @break
    @case('eucalyptus')
        @foreach ([[0, 1], [400, -1]] as [$o, $d])
            <g transform="translate({{ $o }} 0) scale({{ $d }} 1)">
                <path class="fill-none stroke-t-primary/40" stroke-width="1.6" d="M0 40 C60 60 100 110 130 200 M0 90 C40 110 60 150 70 200"/>
                @foreach ([[30, 54, 30], [55, 70, 40], [80, 92, 55], [100, 120, 65], [116, 150, 72], [124, 178, 80], [20, 100, 40], [40, 125, 55], [56, 155, 70], [64, 182, 78]] as [$x, $y, $r])
                    <ellipse class="{{ $loop->even ? 'fill-t-accent/40' : 'fill-t-primary/30' }}" cx="{{ $x }}" cy="{{ $y }}" rx="13" ry="9" transform="rotate({{ $r }} {{ $x }} {{ $y }})"/>
                    <ellipse class="fill-t-accent/25" cx="{{ $x + 12 }}" cy="{{ $y - 6 }}" rx="10" ry="7" transform="rotate({{ -$r }} {{ $x + 12 }} {{ $y - 6 }})"/>
                @endforeach
            </g>
        @endforeach
        @break
    @case('frangipani')
        <path class="fill-t-primary/25" d="M0 200 C10 150 50 130 100 140 C70 150 50 170 45 200Z"/>
        <path class="fill-t-primary/30" d="M400 200 C390 150 350 130 300 140 C330 150 350 170 355 200Z"/>
        <path class="fill-t-accent/30" d="M0 150 C30 120 80 118 110 150 C80 140 40 142 0 170Z"/>
        <path class="fill-t-accent/30" d="M400 150 C370 120 320 118 290 150 C320 140 360 142 400 170Z"/>
        @foreach ([[60, 160, 1.2], [110, 182, 0.9], [26, 128, 0.8], [340, 160, 1.2], [290, 182, 0.9], [374, 128, 0.8]] as [$cx, $cy, $s])
            <g transform="translate({{ $cx }} {{ $cy }}) scale({{ $s }})">
                @foreach ([0, 72, 144, 216, 288] as $a)
                    <path class="fill-t-surface stroke-t-accent/50" stroke-width="0.8" transform="rotate({{ $a }})" d="M0 0 C-10 -6 -12 -22 -2 -26 C6 -22 8 -8 0 0Z"/>
                @endforeach
                <circle class="fill-t-accent/80" r="5"/>
            </g>
        @endforeach
        @break
    @case('gunungan')
        <path class="fill-t-accent/20" d="M0 170 L60 120 L110 150 L170 100 L230 150 L290 110 L350 150 L400 125 V200 H0Z"/>
        <g transform="translate(200 0)">
            <path class="fill-t-primary/80" d="M0 20 C14 50 40 80 56 120 C64 140 62 160 48 176 H-48 C-62 160 -64 140 -56 120 C-40 80 -14 50 0 20Z"/>
            <path class="fill-t-accent/70" d="M0 34 C10 58 30 84 42 118 C48 136 46 150 38 162 H-38 C-46 150 -48 136 -42 118 C-30 84 -10 58 0 34Z"/>
            <path class="fill-t-primary/70" d="M0 50 V160 M0 70 C-14 80 -20 92 -16 104 M0 70 C14 80 20 92 16 104 M0 95 C-18 104 -26 118 -22 132 M0 95 C18 104 26 118 22 132"/>
            <path class="fill-none stroke-t-primary/80" stroke-width="2.4" d="M0 50 V160 M0 70 C-14 80 -20 92 -16 104 M0 70 C14 80 20 92 16 104 M0 95 C-18 104 -26 118 -22 132 M0 95 C18 104 26 118 22 132"/>
            <path class="fill-t-primary/90" d="M-22 176 V150 H22 V176Z M-30 150 H30 L24 142 H-24Z"/>
            <path class="fill-t-accent" d="M-8 176 V158 H8 V176Z"/>
            @foreach ([[-30, 120], [30, 120], [-22, 90], [22, 90], [0, 60]] as [$x, $y])
                <circle class="fill-t-surface/80" cx="{{ $x }}" cy="{{ $y }}" r="3"/>
            @endforeach
        </g>
        <path class="fill-t-primary/40" d="M0 200 V186 C80 178 140 182 200 180 C260 182 320 178 400 186 V200Z"/>
        @break
    @case('candi')
        <path class="fill-t-accent/20" d="M0 160 C100 150 300 150 400 160 V200 H0Z"/>
        @foreach ([[-1, 196], [1, 204]] as [$d, $x])
            <g transform="translate({{ $x }} 0) scale({{ $d }} 1)">
                <path class="fill-t-primary/80" d="M0 190 V60 L8 54 V40 L16 34 V22 L24 16 V8 L30 4 V190Z"/>
                <path class="fill-t-primary/80" d="M30 190 H100 V180 H90 V150 L80 146 V120 L70 116 V96 L60 92 V74 L50 70 V54 L40 50 V30 L30 26Z"/>
                <path class="fill-none stroke-t-accent/80" stroke-width="1.5" d="M40 60 H50 M44 90 H64 M48 120 H76 M52 150 H86 M30 40 H40 M30 70 H52 M30 100 H66 M30 130 H80 M30 160 H90"/>
                <path class="fill-t-accent/60" d="M4 70 l6 -6 l6 6 l-6 6z M8 110 l6 -6 l6 6 l-6 6z"/>
            </g>
        @endforeach
        <path class="fill-t-accent/60" d="M150 200 L170 186 H230 L250 200Z M160 190 H240 V184 H160Z"/>
        <circle class="fill-t-accent/50" cx="200" cy="70" r="22"/>
        @break
    @case('masjid')
        <path class="fill-t-accent/20" d="M0 176 C120 168 280 168 400 176 V200 H0Z"/>
        <g class="fill-t-primary/80">
            <path d="M120 200 V130 H280 V200Z"/>
            <path d="M200 40 C232 52 250 84 250 120 H150 C150 84 168 52 200 40Z"/>
            <path d="M200 26 V40 M196 30 H204"/>
            <path d="M140 130 C140 112 152 100 160 98 C168 100 180 112 180 130Z M220 130 C220 112 232 100 240 98 C248 100 260 112 260 130Z"/>
            <path d="M84 200 V70 H96 V200Z M304 200 V70 H316 V200Z"/>
            <path d="M80 72 C80 58 90 50 90 44 C90 50 100 58 100 72Z M300 72 C300 58 310 50 310 44 C310 50 320 58 320 72Z"/>
        </g>
        <path class="fill-none stroke-t-primary/80" stroke-width="2" d="M200 24 V42"/>
        <path class="fill-t-accent" d="M200 14 a6 6 0 1 0 4 10 a5 5 0 1 1 -4 -10Z"/>
        @foreach ([150, 180, 220, 250] as $x)
            <path class="fill-t-accent/70" d="M{{ $x - 6 }} 200 V168 C{{ $x - 6 }} 160 {{ $x + 6 }} 160 {{ $x + 6 }} 168 V200Z"/>
        @endforeach
        <path class="fill-t-accent" d="M190 200 V160 C190 150 210 150 210 160 V200Z"/>
        @foreach ([[30, 30], [60, 60], [350, 40], [372, 70], [120, 20], [280, 24]] as [$x, $y])
            <path class="fill-t-accent/70" d="M{{ $x }} {{ $y - 4 }} L{{ $x + 1.2 }} {{ $y - 1.2 }} L{{ $x + 4 }} {{ $y }} L{{ $x + 1.2 }} {{ $y + 1.2 }} L{{ $x }} {{ $y + 4 }} L{{ $x - 1.2 }} {{ $y + 1.2 }} L{{ $x - 4 }} {{ $y }} L{{ $x - 1.2 }} {{ $y - 1.2 }}Z"/>
        @endforeach
        @foreach ([40, 360] as $x)
            <path class="fill-none stroke-t-accent/60" d="M{{ $x }} 0 V60"/>
            <path class="fill-t-accent/70" d="M{{ $x - 8 }} 60 h16 l-3 18 h-10z M{{ $x - 4 }} 78 h8 l-4 6z"/>
        @endforeach
        @break
    @case('pantai')
        <circle class="fill-t-accent/50" cx="200" cy="120" r="46"/>
        <path class="fill-t-accent/25" d="M0 120 H400 V200 H0Z"/>
        @foreach ([132, 150, 168, 186] as $i => $y)
            <path class="fill-none stroke-t-primary/{{ 30 + $i * 10 }}" stroke-width="2" d="M0 {{ $y }} C40 {{ $y - 6 }} 80 {{ $y + 6 }} 120 {{ $y }} S200 {{ $y - 6 }} 240 {{ $y }} S320 {{ $y + 6 }} 400 {{ $y }}"/>
        @endforeach
        <path class="fill-t-soft" d="M0 200 C60 178 120 176 170 188 C210 198 240 200 260 200Z"/>
        @foreach ([[54, 1], [346, -1]] as [$x, $d])
            <g transform="translate({{ $x }} 0) scale({{ $d }} 1)">
                <path class="fill-none stroke-t-primary/80" stroke-width="5" d="M0 196 C4 150 14 110 30 80"/>
                @foreach ([[-40, 10], [-10, -10], [30, 0], [50, 30], [-30, 40]] as [$ex, $ey])
                    <path class="fill-t-primary/70" d="M30 80 C{{ 30 + $ex / 2 }} {{ 70 + $ey / 2 - 10 }} {{ 30 + $ex }} {{ 76 + $ey }} {{ 30 + $ex * 1.1 }} {{ 90 + $ey }} C{{ 30 + $ex / 2 }} {{ 82 + $ey / 2 }} {{ 34 }} {{ 84 }} 30 80Z"/>
                @endforeach
            </g>
        @endforeach
        <path class="fill-t-primary/70" d="M250 150 H290 L284 158 H256Z M268 150 V128 L284 146Z"/>
        @break
    @case('sawah')
        <path class="fill-t-accent/25" d="M0 110 L80 50 L150 100 L230 40 L320 100 L400 70 V200 H0Z"/>
        <circle class="fill-t-accent/50" cx="300" cy="50" r="20"/>
        @foreach ([[120, 'fill-t-primary/20'], [140, 'fill-t-primary/30'], [160, 'fill-t-primary/40'], [180, 'fill-t-primary/55']] as [$y, $cls])
            <path class="{{ $cls }}" d="M0 {{ $y }} C80 {{ $y - 14 }} 160 {{ $y + 8 }} 240 {{ $y - 6 }} C300 {{ $y - 14 }} 350 {{ $y - 4 }} 400 {{ $y - 10 }} V200 H0Z"/>
            <path class="fill-none stroke-t-accent/60" d="M0 {{ $y + 4 }} C80 {{ $y - 10 }} 160 {{ $y + 12 }} 240 {{ $y - 2 }} C300 {{ $y - 10 }} 350 {{ $y }} 400 {{ $y - 6 }}"/>
        @endforeach
        @foreach ([[50, 1], [360, -1]] as [$x, $d])
            <g transform="translate({{ $x }} 0) scale({{ $d }} 1)">
                <path class="fill-none stroke-t-primary/70" stroke-width="3" d="M0 170 C2 140 6 110 14 90"/>
                <path class="fill-t-primary/60" d="M14 90 C0 80 -20 86 -26 98 C-12 92 0 92 14 90Z M14 90 C24 78 44 80 50 92 C36 88 26 88 14 90Z M14 90 C10 74 16 62 28 58 C22 70 18 80 14 90Z"/>
            </g>
        @endforeach
        @break
    @case('rumah-gadang')
        <path class="fill-t-accent/20" d="M0 170 C120 160 280 160 400 170 V200 H0Z"/>
        <g class="fill-t-primary/80">
            <path d="M60 80 C90 100 110 96 130 70 C150 96 170 100 200 60 C230 100 250 96 270 70 C290 96 310 100 340 80 C330 110 310 120 300 124 H100 C90 120 70 110 60 80Z"/>
            <path d="M110 124 H290 V176 H110Z"/>
        </g>
        <path class="fill-t-accent/70" d="M120 132 H280 V140 H120Z"/>
        @foreach ([136, 170, 204, 238, 266] as $x)
            <path class="fill-t-accent/60" d="M{{ $x }} 148 h14 v20 h-14z"/>
        @endforeach
        <path class="fill-none stroke-t-accent" stroke-width="1.5" d="M60 80 L56 72 M130 70 L130 60 M200 60 L200 48 M270 70 L270 60 M340 80 L344 72"/>
        <path class="fill-t-primary/60" d="M100 176 H300 L310 190 H90Z"/>
        @break
    @case('joglo')
        <path class="fill-t-accent/20" d="M0 172 C120 164 280 164 400 172 V200 H0Z"/>
        <g class="fill-t-primary/80">
            <path d="M170 40 H230 L270 90 H130Z"/>
            <path d="M130 90 H270 L340 124 H60Z"/>
            <path d="M90 124 H310 V132 H90Z"/>
        </g>
        @foreach ([110, 160, 240, 290] as $x)
            <path class="fill-t-primary/70" d="M{{ $x }} 132 h8 v48 h-8z"/>
        @endforeach
        <path class="fill-t-accent/60" d="M170 140 H230 V180 H170Z"/>
        <path class="fill-none stroke-t-accent" stroke-width="1.4" d="M170 40 H230 M150 66 H250 M100 108 H300"/>
        <path class="fill-t-primary/50" d="M80 180 H320 L330 192 H70Z"/>
        @break
    @case('pinisi')
        <path class="fill-t-accent/20" d="M0 150 H400 V200 H0Z"/>
        @foreach ([156, 170, 184] as $y)
            <path class="fill-none stroke-t-primary/40" stroke-width="1.6" d="M0 {{ $y }} C40 {{ $y - 5 }} 80 {{ $y + 5 }} 120 {{ $y }} S200 {{ $y - 5 }} 240 {{ $y }} S320 {{ $y + 5 }} 400 {{ $y }}"/>
        @endforeach
        <path class="fill-t-primary/85" d="M110 140 C140 150 260 150 300 136 L286 156 H126Z"/>
        <path class="fill-none stroke-t-primary/80" stroke-width="2" d="M170 140 V40 M230 140 V50"/>
        <path class="fill-t-soft stroke-t-accent/60" d="M172 44 L220 100 L172 104Z M172 108 L224 134 H172Z M232 54 L276 110 L232 112Z M232 116 L270 134 H232Z"/>
        <path class="fill-t-accent/50" d="M168 46 L120 120 H168Z"/>
        <circle class="fill-t-accent/40" cx="330" cy="50" r="18"/>
        @break
    @case('lampion')
        <path class="fill-none stroke-t-primary/40" stroke-width="1.4" d="M0 20 C100 50 300 50 400 20"/>
        @foreach ([[60, 34, 1], [130, 44, 1.2], [200, 47, 1.35], [270, 44, 1.2], [340, 34, 1]] as [$x, $y, $s])
            <g transform="translate({{ $x }} {{ $y }}) scale({{ $s }})">
                <path class="fill-none stroke-t-primary/50" d="M0 0 V10"/>
                <path class="fill-t-primary/85" d="M-14 14 C-22 30 -22 44 -14 58 H14 C22 44 22 30 14 14Z"/>
                <path class="fill-t-accent" d="M-10 10 H10 V14 H-10Z M-10 58 H10 V62 H-10Z"/>
                <path class="fill-none stroke-t-accent/70" d="M-6 14 C-10 30 -10 44 -6 58 M6 14 C10 30 10 44 6 58 M0 14 V58"/>
                <path class="fill-none stroke-t-accent" stroke-width="1.4" d="M-3 62 V78 M0 62 V82 M3 62 V78"/>
            </g>
        @endforeach
        @foreach ([[20, 140], [380, 150]] as [$x, $y])
            <path class="fill-none stroke-t-primary/60" stroke-width="2.4" d="M{{ $x }} 200 C{{ $x + 10 }} 170 {{ $x + ($x < 200 ? 40 : -40) }} 150 {{ $x + ($x < 200 ? 70 : -70) }} {{ $y }}"/>
            @foreach ([0, 1, 2, 3] as $k)
                @php $px = $x + ($x < 200 ? 1 : -1) * (14 + $k * 16); $py = 186 - $k * 12; @endphp
                @foreach ([0, 72, 144, 216, 288] as $a)
                    <circle class="fill-t-accent/70" cx="{{ $px + 4 * cos(deg2rad($a)) }}" cy="{{ $py + 4 * sin(deg2rad($a)) }}" r="3"/>
                @endforeach
                <circle class="fill-t-primary/70" cx="{{ $px }}" cy="{{ $py }}" r="1.6"/>
            @endforeach
        @endforeach
        @break
    @case('kota')
        @foreach ([[0, 120, 30], [32, 96, 26], [60, 136, 34], [96, 80, 22], [120, 110, 36], [158, 70, 28], [188, 124, 30], [220, 90, 24], [246, 60, 30], [278, 116, 28], [308, 86, 32], [342, 128, 26], [370, 100, 30]] as [$x, $y, $w])
            <path class="{{ $loop->even ? 'fill-t-primary/50' : 'fill-t-primary/70' }}" d="M{{ $x }} 200 V{{ $y }} H{{ $x + $w }} V200Z"/>
            @for ($wy = $y + 8; $wy < 190; $wy += 14)
                <path class="fill-t-accent/{{ ($wy + $x) % 3 ? '60' : '20' }}" d="M{{ $x + 5 }} {{ $wy }} h4 v6 h-4z M{{ $x + $w - 9 }} {{ $wy }} h4 v6 h-4z"/>
            @endfor
        @endforeach
        <circle class="fill-t-accent/60" cx="330" cy="36" r="14"/>
        @foreach ([[40, 30], [90, 50], [160, 24], [220, 44], [280, 20], [370, 56]] as [$x, $y])
            <circle class="fill-t-accent/80" cx="{{ $x }}" cy="{{ $y }}" r="1.4"/>
        @endforeach
        @break
    @case('pegunungan')
        <path class="fill-t-accent/25" d="M0 120 L70 50 L130 100 L200 30 L280 110 L340 60 L400 100 V200 H0Z"/>
        <path class="fill-t-soft" d="M200 30 L220 52 L208 50 L200 60 L192 50 L180 52Z M70 50 L84 64 L70 60 L58 66Z"/>
        <path class="fill-t-primary/30" d="M0 160 C80 140 160 150 240 140 C310 132 360 140 400 150 V200 H0Z"/>
        @foreach ([20, 44, 66, 300, 326, 352, 378, 120, 270] as $i => $x)
            @php $h = 50 + ($i * 13) % 30; @endphp
            <path class="{{ $i % 2 ? 'fill-t-primary/75' : 'fill-t-primary/60' }}" d="M{{ $x }} {{ 196 - $h }} L{{ $x + 14 }} {{ 186 - $h / 2 }} H{{ $x + 6 }} L{{ $x + 18 }} 192 H{{ $x - 18 }} L{{ $x - 6 }} {{ 186 - $h / 2 }} H{{ $x - 14 }}Z"/>
        @endforeach
        <path class="fill-t-primary/50" d="M0 200 V190 C120 184 280 184 400 190 V200Z"/>
        @break
    @case('taman')
        <path class="fill-none stroke-t-primary/40" stroke-width="1.2" d="M0 30 C60 60 140 60 200 30 C260 60 340 60 400 30"/>
        @foreach ([20, 50, 80, 110, 140, 170, 230, 260, 290, 320, 350, 380] as $x)
            @php $y = 30 + 24 * sin(deg2rad(($x % 200) * 0.9)); @endphp
            <circle class="fill-t-accent/80" cx="{{ $x }}" cy="{{ abs($y) + 8 }}" r="3"/>
            <circle class="fill-t-accent/25" cx="{{ $x }}" cy="{{ abs($y) + 8 }}" r="7"/>
        @endforeach
        <path class="fill-t-accent/20" d="M0 176 C120 168 280 168 400 176 V200 H0Z"/>
        <path class="fill-none stroke-t-primary/70" stroke-width="6" d="M140 200 V120 C140 70 260 70 260 120 V200"/>
        <path class="fill-none stroke-t-accent/50" stroke-width="2" d="M140 120 C140 70 260 70 260 120"/>
        @foreach ([[140, 140], [150, 98], [176, 80], [200, 76], [224, 80], [250, 98], [260, 140], [140, 176], [260, 176]] as [$x, $y])
            <circle class="fill-t-accent/60" cx="{{ $x }}" cy="{{ $y }}" r="8"/>
            <circle class="fill-t-primary/40" cx="{{ $x + 6 }}" cy="{{ $y - 4 }}" r="5"/>
            <path class="fill-t-primary/50" d="M{{ $x - 4 }} {{ $y + 6 }} C{{ $x - 14 }} {{ $y + 4 }} {{ $x - 16 }} {{ $y - 6 }} {{ $x - 8 }} {{ $y - 2 }}Z"/>
        @endforeach
        @foreach ([40, 80, 320, 360] as $x)
            <path class="fill-t-primary/50" d="M{{ $x }} 190 C{{ $x - 20 }} 190 {{ $x - 22 }} 160 {{ $x }} 150 C{{ $x + 22 }} 160 {{ $x + 20 }} 190 {{ $x }} 190Z"/>
        @endforeach
        @break
    @case('melati')
        <path class="fill-none stroke-t-primary/30" stroke-width="1.2" d="M0 10 H400"/>
        @foreach ([30, 70, 110, 150, 250, 290, 330, 370] as $i => $x)
            @php $len = 80 + (($i * 29) % 70); @endphp
            <path class="fill-none stroke-t-accent/50" d="M{{ $x }} 10 V{{ 10 + $len }}"/>
            @for ($y = 22; $y < 10 + $len; $y += 13)
                <g transform="translate({{ $x }} {{ $y }})">
                    @foreach ([0, 90, 180, 270] as $a)
                        <ellipse class="fill-t-surface stroke-t-accent/40" stroke-width="0.6" cx="0" cy="-3.6" rx="2.6" ry="3.6" transform="rotate({{ $a + 45 }})"/>
                    @endforeach
                    <circle class="fill-t-accent/70" r="1.4"/>
                </g>
            @endfor
            <path class="fill-t-accent/70" d="M{{ $x - 4 }} {{ 14 + $len }} L{{ $x }} {{ 24 + $len }} L{{ $x + 4 }} {{ 14 + $len }}Z"/>
        @endforeach
        <path class="fill-t-primary/30" d="M150 200 C170 170 230 170 250 200Z"/>
        @break
    @case('bambu')
        <path class="fill-t-accent/20" d="M0 180 C120 172 280 172 400 180 V200 H0Z"/>
        @foreach ([[20, 0, 10], [44, 30, 8], [70, 10, 9], [330, 14, 9], [356, 0, 10], [380, 26, 8]] as [$x, $top, $w])
            <path class="fill-t-primary/70" d="M{{ $x }} 200 V{{ $top }} H{{ $x + $w }} V200Z"/>
            @for ($y = $top + 30; $y < 196; $y += 34)
                <path class="fill-t-accent/80" d="M{{ $x - 1 }} {{ $y }} h{{ $w + 2 }} v2 h-{{ $w + 2 }}z"/>
                <path class="fill-t-primary/50" d="M{{ $x + $w }} {{ $y }} C{{ $x + $w + 14 }} {{ $y - 8 }} {{ $x + $w + 30 }} {{ $y - 6 }} {{ $x + $w + 36 }} {{ $y - 2 }} C{{ $x + $w + 22 }} {{ $y + 2 }} {{ $x + $w + 10 }} {{ $y + 2 }} {{ $x + $w }} {{ $y }}Z"/>
            @endfor
        @endforeach
        @break
    @default
@endswitch
</svg>
{{-- Dynamic classes above, listed for Tailwind: stroke-t-primary/30 stroke-t-primary/40 stroke-t-primary/50 stroke-t-primary/60 fill-t-accent/60 fill-t-accent/20 --}}
