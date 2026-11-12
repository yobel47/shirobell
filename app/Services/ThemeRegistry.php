<?php

namespace App\Services;

/**
 * Reads config/themes.php (ADR 009): categories, color variants and engine
 * recipes. Colors and fonts become CSS custom properties on <body>, which
 * Tailwind 4 utilities read through var(), so variants need no rebuild.
 */
class ThemeRegistry
{
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

        $engine = $this->engine($slug);
        if ($engine !== null) {
            $fonts = config('themes.fonts', []);
            $pair = $fontPair ? (config("themes.font_pairs.{$fontPair}.fonts") ?? null) : null;
            [$heading, $script, $body] = array_pad($pair ?? $engine['fonts'], 3, 'inter');
            foreach (['heading' => $heading, 'script' => $script, 'body' => $body] as $role => $font) {
                if (isset($fonts[$font])) {
                    $vars[] = "--font-{$role}: {$fonts[$font]}";
                }
            }
        }

        return implode('; ', $vars);
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
