{{-- Shirobell bell mark (bell with a heart clapper). Inherits the text color. --}}
<svg viewBox="0 0 100 100" fill="currentColor" {{ $attributes->merge(['aria-hidden' => 'true']) }}>
    <circle cx="50" cy="16.5" r="4.5" fill="none" stroke="currentColor" stroke-width="{{ $stroke ?? 3 }}"/>
    <path d="M50 22 C35 22 29 35 29 50 C29 59 26 65 20 70 L80 70 C74 65 71 59 71 50 C71 35 65 22 50 22 Z" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/>
    <path d="M50 91 C43 85.6 40.5 82.6 40.5 79.6 C40.5 76.9 42.6 75 45 75 C47 75 48.8 76.1 50 77.9 C51.2 76.1 53 75 55 75 C57.4 75 59.5 76.9 59.5 79.6 C59.5 82.6 57 85.6 50 91 Z"/>
</svg>
