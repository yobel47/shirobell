{{-- Section divider. Original line art; color follows the accent token. --}}
<div class="flex justify-center py-4 text-t-accent" aria-hidden="true">
    <svg class="h-8 w-56" viewBox="0 0 224 32" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
        @switch($engine['ornament'])
            @case('floral')
                <path d="M8 16 H84 M140 16 H216"/>
                <path d="M84 16 C 90 8 98 8 100 12 M140 16 C 134 24 126 24 124 20"/>
                <circle cx="112" cy="16" r="3" fill="currentColor"/>
                <path d="M112 9 C 116 3 122 9 112 13 C 102 9 108 3 112 9 Z M112 23 C 116 29 122 23 112 19 C 102 23 108 29 112 23 Z"/>
                <path d="M105 16 C 99 12 99 20 105 16 Z M119 16 C 125 12 125 20 119 16 Z"/>
                <path d="M60 16 C 64 10 70 10 72 14 M164 16 C 160 22 154 22 152 18"/>
                @break
            @case('leaf')
                <path d="M16 16 H208"/>
                @foreach ([52, 76, 148, 172] as $x)
                    <path d="M{{ $x }} 16 C {{ $x + 4 }} 7 {{ $x + 12 }} 6 {{ $x + 14 }} 8 C {{ $x + 12 }} 14 {{ $x + 6 }} 16 {{ $x }} 16 Z"/>
                    <path d="M{{ $x }} 16 C {{ $x + 4 }} 25 {{ $x + 12 }} 26 {{ $x + 14 }} 24 C {{ $x + 12 }} 18 {{ $x + 6 }} 16 {{ $x }} 16 Z"/>
                @endforeach
                <circle cx="112" cy="16" r="3" fill="currentColor"/>
                @break
            @case('kawung')
                <path d="M8 16 H88 M136 16 H216"/>
                <ellipse cx="112" cy="8" rx="4" ry="6"/><ellipse cx="112" cy="24" rx="4" ry="6"/>
                <ellipse cx="104" cy="16" rx="6" ry="4"/><ellipse cx="120" cy="16" rx="6" ry="4"/>
                <circle cx="112" cy="16" r="1.5" fill="currentColor"/>
                <circle cx="80" cy="16" r="2" fill="currentColor"/><circle cx="144" cy="16" r="2" fill="currentColor"/>
                @break
            @case('parang')
                <path d="M8 16 H80 M144 16 H216"/>
                <path d="M84 26 C 92 26 92 16 100 16 S 108 6 116 6"/>
                <path d="M108 26 C 116 26 116 16 124 16 S 132 6 140 6"/>
                <circle cx="96" cy="8" r="1.6" fill="currentColor"/><circle cx="128" cy="24" r="1.6" fill="currentColor"/>
                @break
            @case('cloud')
                <path d="M8 22 H70 M154 22 H216"/>
                <path d="M70 22 C 70 12 84 12 84 19 C 84 8 104 8 104 19 C 104 10 120 10 120 19 C 120 8 140 8 140 19 C 140 12 154 12 154 22"/>
                <path d="M90 22 C 90 16 98 16 98 20 M126 22 C 126 16 134 16 134 20"/>
                @break
            @case('lotus')
                <path d="M8 24 H80 M144 24 H216"/>
                <path d="M112 24 C 104 16 106 8 112 4 C 118 8 120 16 112 24 Z"/>
                <path d="M112 24 C 100 22 94 14 96 8 C 104 10 110 16 112 24 Z M112 24 C 124 22 130 14 128 8 C 120 10 114 16 112 24 Z"/>
                <path d="M112 24 C 96 26 86 20 84 14 M112 24 C 128 26 138 20 140 14"/>
                @break
            @case('tenun')
                <path d="M8 6 H216 M8 26 H216"/>
                <path d="M8 16 L16 10 L24 16 L32 10 L40 16 L48 10 L56 16 L64 10 L72 16 L80 10 L88 16 L96 10 L104 16 L112 10 L120 16 L128 10 L136 16 L144 10 L152 16 L160 10 L168 16 L176 10 L184 16 L192 10 L200 16 L208 10 L216 16"/>
                @break
            @case('songket')
                <path d="M8 16 H64 M160 16 H216"/>
                @foreach ([72, 92, 112, 132, 152] as $x)
                    <path d="M{{ $x }} 8 L{{ $x + 8 }} 16 L{{ $x }} 24 L{{ $x - 8 }} 16 Z" @if ($x === 112) fill="currentColor" @endif/>
                @endforeach
                @break
            @case('deco')
                <path d="M8 16 H84 M140 16 H216 M8 20 H76 M148 20 H216"/>
                <path d="M88 26 A24 24 0 0 1 136 26 M96 26 A16 16 0 0 1 128 26 M104 26 A8 8 0 0 1 120 26"/>
                <path d="M112 2 V26"/>
                @break
            @case('star')
                <path d="M8 16 H88 M136 16 H216"/>
                <rect x="104" y="8" width="16" height="16"/>
                <rect x="104" y="8" width="16" height="16" transform="rotate(45 112 16)"/>
                <circle cx="112" cy="16" r="2.5" fill="currentColor"/>
                <circle cx="80" cy="16" r="1.6" fill="currentColor"/><circle cx="144" cy="16" r="1.6" fill="currentColor"/>
                @break
            @case('wave')
                <path d="M8 14 C 22 6 36 6 50 14 S 78 22 92 14 S 120 6 134 14 S 162 22 176 14 S 204 6 216 14"/>
                <path d="M40 22 C 54 16 68 16 82 22 M142 22 C 156 16 170 16 184 22" stroke-width="0.8"/>
                @break
            @default
                <path d="M24 16 H100 M124 16 H200"/>
                <path d="M112 10 L118 16 L112 22 L106 16 Z" fill="currentColor"/>
        @endswitch
    </svg>
</div>
