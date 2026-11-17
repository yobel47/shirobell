<?php

namespace Tests\Feature\Invitation;

use App\Services\ThemeRegistry;
use App\Support\Contrast;
use Tests\TestCase;

/**
 * Theme palette guards: readable text after the craft.css contrast tokens
 * (ThemeRegistry::accessibleVars), legacy defaults in sync with their CSS,
 * and a real catalog thumbnail for every theme variant.
 */
class ThemeContrastTest extends TestCase
{
    public function test_contrast_helpers(): void
    {
        $this->assertEqualsWithDelta(21.0, Contrast::ratio('#000000', '#ffffff'), 0.01);
        $this->assertSame('#ffffff', Contrast::mix('#ffffff', '#000000', 1.0));
        $this->assertSame('#123456', Contrast::ensure('#123456', '#000000', '#ffffff'), 'already readable colors stay unchanged');
        $this->assertGreaterThanOrEqual(4.5, Contrast::ratio(Contrast::ensure('#eeddaa', '#333333', '#ffffff'), '#ffffff'));
        $this->assertSame([170, 187, 204], Contrast::rgb('#abc'));
        $this->assertSame([17, 34, 51], Contrast::rgb('#112233cc'), 'alpha is ignored');
    }

    public function test_every_theme_variant_meets_aa_for_small_text(): void
    {
        $registry = app(ThemeRegistry::class);
        $failures = [];

        foreach ($this->combos($registry) as [$slug, $variant]) {
            $palette = $registry->palette($slug, $variant);
            if ($palette === null) {
                $failures[] = "{$slug}/{$variant} has an incomplete palette";

                continue;
            }
            $vars = $this->parse($registry->accessibleVars($slug, $variant));

            $checks = [
                'label' => Contrast::ratio($vars['--tc-label'], $palette['bg']),
                'strong' => Contrast::ratio($vars['--tc-strong'] ?? $palette['primary'], $palette['bg']),
                'button' => Contrast::ratio($palette['on'], $vars['--tc-btn'] ?? $palette['primary']),
            ];
            foreach ($checks as $name => $ratio) {
                if ($ratio < 4.5) {
                    $failures[] = sprintf('%s/%s %s %.2f', $slug, $variant, $name, $ratio);
                }
            }
        }

        $this->assertSame([], $failures);
    }

    public function test_legacy_defaults_match_their_stylesheets(): void
    {
        $defaults = (new \ReflectionClassConstant(ThemeRegistry::class, 'LEGACY_DEFAULTS'))->getValue();
        $tokens = ['bg' => 'cream', 'accent' => 'rose', 'ink' => 'ink', 'primary' => 'deep'];
        $prefixes = ['rose-garden' => 'rg', 'classic-gold' => 'cg', 'sage-minimalist' => 'sm'];

        foreach ($defaults as $slug => $colors) {
            $css = file_get_contents(resource_path("css/themes/{$slug}.css"));
            foreach ($tokens as $key => $token) {
                $this->assertMatchesRegularExpression(
                    '/--color-'.$prefixes[$slug].'-'.$token.':\s*'.preg_quote($colors[$key], '/').';/i',
                    $css,
                    "{$slug}: LEGACY_DEFAULTS[{$key}] must match --color-{$prefixes[$slug]}-{$token} in {$slug}.css",
                );
            }
        }
    }

    public function test_every_theme_variant_has_a_catalog_thumbnail(): void
    {
        $missing = [];
        foreach ($this->combos(app(ThemeRegistry::class)) as [$slug, $variant]) {
            if (! is_file(public_path("images/themes/{$slug}--{$variant}.webp"))) {
                $missing[] = "{$slug}--{$variant}";
            }
        }

        $this->assertSame([], $missing, 'Run scripts/capture-theme-thumbnails.mjs for new themes or variants.');
    }

    /** @return list<array{0: string, 1: string|null}> */
    private function combos(ThemeRegistry $registry): array
    {
        $combos = [];
        foreach (array_keys($registry->all()) as $slug) {
            foreach (array_keys($registry->variants($slug)) ?: [null] as $variant) {
                $combos[] = [$slug, $variant];
            }
        }

        return $combos;
    }

    /**
     * @param  list<string>  $vars
     * @return array<string, string>
     */
    private function parse(array $vars): array
    {
        $out = [];
        foreach ($vars as $var) {
            [$k, $v] = array_map('trim', explode(':', $var, 2));
            $out[$k] = $v;
        }

        return $out;
    }
}
