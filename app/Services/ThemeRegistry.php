<?php

namespace App\Services;

use App\Support\Contrast;
use Illuminate\Support\Str;

/**
 * Reads config/themes.php (ADR 009): categories, color variants and engine
 * recipes. Colors and fonts become CSS custom properties on <body>, which
 * Tailwind 4 utilities read through var(), so variants need no rebuild.
 */
class ThemeRegistry
{
    /**
     * Legacy themes keep their default palette in CSS (@theme), not in config:
     * the values craft.css contrast checks need for their first variant.
     */
    private const LEGACY_DEFAULTS = [
        'rose-garden' => ['bg' => '#fdf7f5', 'accent' => '#c77f86', 'ink' => '#4a3438', 'primary' => '#8a4a52'],
        'classic-gold' => ['bg' => '#faf7f0', 'accent' => '#a8823f', 'ink' => '#2a251e', 'primary' => '#1f1b16'],
        'sage-minimalist' => ['bg' => '#f7f8f4', 'accent' => '#7f9a7a', 'ink' => '#2f3a33', 'primary' => '#44564a'],
    ];

    /** Shared tokens of engine themes, mapped to --color-t-{token}. */
    public const ENGINE_TOKENS = ['bg', 'surface', 'soft', 'primary', 'accent', 'ink', 'on'];

    private const ENGINE_DEFAULTS = [
        'hero' => 'arch', 'shape' => 'arch', 'heading' => 'script', 'sections' => 'cards',
        'ornament' => 'line', 'pattern' => 'none', 'fonts' => ['playfair', 'great-vibes', 'inter'],
        'illustration' => null,
    ];

    /** @return array<string, string> */
    public function categories(): array
    {
        return config('themes.categories', []);
    }

    /** @return array<string, array<string, mixed>> */
    public function all(): array
    {
        return config('themes.themes', []);
    }

    /** @return array<string, mixed> */
    public function definition(string $slug): array
    {
        return $this->all()[$slug] ?? [];
    }

    public function isEngine(string $slug): bool
    {
        return isset($this->definition($slug)['engine']);
    }

    /** @return array<string, mixed>|null normalized engine recipe */
    public function engine(string $slug): ?array
    {
        $engine = $this->definition($slug)['engine'] ?? null;

        return $engine === null ? null : array_merge(self::ENGINE_DEFAULTS, $engine);
    }

    public function category(string $slug): ?string
    {
        return $this->definition($slug)['category'] ?? null;
    }

    public function categoryLabel(string $slug): ?string
    {
        $category = $this->category($slug);

        return $category ? ($this->categories()[$category] ?? null) : null;
    }

    /** @return array<string, array{name: string, colors: array<string, string>}> */
    public function variants(string $slug): array
    {
        return $this->definition($slug)['variants'] ?? [];
    }

    public function hasVariant(string $slug, ?string $key): bool
    {
        return $key !== null && array_key_exists($key, $this->variants($slug));
    }

    /** Falls back to the first (default) variant for unknown keys. */
    public function variantKey(string $slug, ?string $key): ?string
    {
        if ($this->hasVariant($slug, $key)) {
            return $key;
        }

        return array_key_first($this->variants($slug));
    }

    /** @return array<string, string> token => hex */
    public function colors(string $slug, ?string $variant = null): array
    {
        $key = $this->variantKey($slug, $variant);

        return $key === null ? [] : ($this->variants($slug)[$key]['colors'] ?? []);
    }

    /**
     * Three representative colors for swatches: background, primary, accent.
     *
     * @return array{0: string, 1: string, 2: string}
     */
    public function swatch(string $slug, ?string $variant = null): array
    {
        if ($this->isEngine($slug)) {
            $c = $this->colors($slug, $variant);

            return [$c['bg'] ?? '#ffffff', $c['primary'] ?? '#333333', $c['accent'] ?? '#999999'];
        }

        $base = $this->definition($slug)['swatch'] ?? ['#ffffff', '#333333', '#999999'];
        $c = $this->colors($slug, $variant);
        $prefix = $this->tokenPrefix($c);

        return $prefix === null ? $base : [
            $c[$prefix.'-cream'] ?? $base[0],
            $c[$prefix.'-deep'] ?? $base[1],
            $c[$prefix.'-gold'] ?? $base[2],
        ];
    }

    /** Inline CSS custom properties for <body style="...">. */
    public function style(string $slug, ?string $variant = null, ?string $fontPair = null): string
    {
        $vars = [];

        foreach ($this->colors($slug, $variant) as $token => $hex) {
            if (! $this->isColor($hex)) {
                continue;
            }
            $name = $this->isEngine($slug) ? 't-'.$token : $token;
            if (preg_match('/^[a-z0-9-]+$/', $name)) {
                $vars[] = "--color-{$name}: {$hex}";
            }
        }

        $keys = $this->fontKeys($slug, $fontPair);
        if ($keys !== null) {
            $fonts = config('themes.fonts', []);
            [$heading, $script, $body] = $keys;
            foreach (['heading' => $heading, 'script' => $script, 'body' => $body] as $role => $font) {
                if (isset($fonts[$font])) {
                    $vars[] = "--font-{$role}: {$fonts[$font]}";
                }
            }
        }

        return implode('; ', array_merge($vars, $this->accessibleVars($slug, $variant)));
    }

    /**
     * Contrast-safe variants of decorative colors for craft.css, computed per
     * theme and variant so each palette changes only as much as needed:
     *  --tc-label  accent pulled toward ink until small labels reach 4.5:1
     *  --tc-strong primary pulled toward ink for small text (only when needed)
     *  --tc-btn    primary pushed away from the button text color (only when
     *              the original pair is below 4.5:1)
     *
     * @return list<string>
     */
    public function accessibleVars(string $slug, ?string $variant = null): array
    {
        $palette = $this->palette($slug, $variant);
        if ($palette === null) {
            return [];
        }
        ['bg' => $bg, 'accent' => $accent, 'ink' => $ink, 'primary' => $primary, 'on' => $on] = $palette;

        $vars = ['--tc-label: '.Contrast::ensure($accent, $ink, $bg)];
        // Primary as small text (form labels, outline buttons); large headings keep the original.
        if (Contrast::ratio($primary, $bg) < 4.5) {
            $vars[] = '--tc-strong: '.Contrast::ensure($primary, $ink, $bg);
        }
        if (Contrast::ratio($on, $primary) < 4.5) {
            $away = Contrast::ratio($on, '#000000') > Contrast::ratio($on, '#ffffff') ? '#000000' : '#ffffff';
            $vars[] = '--tc-btn: '.Contrast::ensure($primary, $away, $on);
        }

        return $vars;
    }

    /**
     * Text-relevant colors of a theme variant as craft.css sees them (--tc-*):
     * background, accent (labels), ink (body), primary (headings, buttons) and
     * the text color on primary. Legacy themes read their prefixed tokens, with
     * the CSS defaults for the first variant. Null when a color is missing.
     *
     * @return array{bg: string, accent: string, ink: string, primary: string, on: string}|null
     */
    public function palette(string $slug, ?string $variant = null): ?array
    {
        $c = $this->colors($slug, $variant);
        if ($this->isEngine($slug)) {
            $palette = ['bg' => $c['bg'] ?? null, 'accent' => $c['accent'] ?? null, 'ink' => $c['ink'] ?? null, 'primary' => $c['primary'] ?? null, 'on' => $c['on'] ?? '#ffffff'];
        } else {
            $defaults = self::LEGACY_DEFAULTS[$slug] ?? [];
            $prefix = $this->tokenPrefix($c);
            $token = fn (string $name, string $key) => ($prefix !== null ? ($c[$prefix.'-'.$name] ?? null) : null) ?? $defaults[$key] ?? null;
            $palette = ['bg' => $token('cream', 'bg'), 'accent' => $token('rose', 'accent'), 'ink' => $token('ink', 'ink'), 'primary' => $token('deep', 'primary'), 'on' => '#ffffff'];
        }

        foreach ($palette as $hex) {
            if (! is_string($hex) || ! $this->isColor($hex)) {
                return null;
            }
        }

        return $palette;
    }

    /**
     * Font families (Vite font aliases, e.g. "playfair-display") a theme renders
     * with, so invitation pages declare and preload only those (@fonts($aliases)).
     * Resolves the customer's font pair the same way style() does.
     *
     * @return list<string>
     */
    public function fontAliases(string $slug, ?string $fontPair = null): array
    {
        $keys = $this->fontKeys($slug, $fontPair);
        if ($keys === null) {
            // Legacy themes (rose-garden, classic-gold, sage-minimalist) set these in their CSS.
            return ['inter', 'playfair-display', 'great-vibes'];
        }

        $fonts = config('themes.fonts', []);
        $aliases = [];
        foreach ($keys as $key) {
            // "'Playfair Display', Georgia, serif" -> "playfair-display"
            if (isset($fonts[$key]) && preg_match("/'([^']+)'/", $fonts[$key], $m)) {
                $aliases[] = Str::slug($m[1]);
            }
        }

        return array_values(array_unique($aliases));
    }

    /**
     * [heading, script, body] font keys of an engine theme, the customer's font
     * pair taking precedence. Null for legacy themes (fonts live in their CSS).
     *
     * @return array{0: string, 1: string, 2: string}|null
     */
    private function fontKeys(string $slug, ?string $fontPair): ?array
    {
        $engine = $this->engine($slug);
        if ($engine === null) {
            return null;
        }

        $pair = $fontPair ? (config("themes.font_pairs.{$fontPair}.fonts") ?? null) : null;

        return array_slice(array_pad($pair ?? $engine['fonts'], 3, 'inter'), 0, 3);
    }

    private function isColor(string $value): bool
    {
        return (bool) preg_match('/^#[0-9a-fA-F]{3,8}$/', $value);
    }

    /** @param array<string, string> $colors */
    private function tokenPrefix(array $colors): ?string
    {
        $first = array_key_first($colors);

        return $first ? explode('-', $first)[0] : null;
    }
}
