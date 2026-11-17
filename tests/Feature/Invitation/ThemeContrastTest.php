<?php

namespace Tests\Feature\Invitation;

use App\Services\ThemeRegistry;
use App\Support\Contrast;
use Tests\TestCase;

/**
 * Every theme and color variant must keep readable text after the craft.css
 * contrast tokens are applied (ThemeRegistry::accessibleVars): small labels,
 * small primary text and button text at WCAG AA 4.5:1.
 */
class ThemeContrastTest extends TestCase
{
    public function test_contrast_helpers(): void
    {
        $this->assertEqualsWithDelta(21.0, Contrast::ratio('#000000', '#ffffff'), 0.01);
        $this->assertSame('#ffffff', Contrast::mix('#ffffff', '#000000', 1.0));
        $this->assertSame('#123456', Contrast::ensure('#123456', '#000000', '#ffffff'), 'already readable colors stay unchanged');
        $this->assertGreaterThanOrEqual(4.5, Contrast::ratio(Contrast::ensure('#eeddaa', '#333333', '#ffffff'), '#ffffff'));
    }

    public function test_every_theme_variant_meets_aa_for_small_text(): void
    {
        $registry = app(ThemeRegistry::class);
        $legacyDefaults = (new \ReflectionClassConstant(ThemeRegistry::class, 'LEGACY_DEFAULTS'))->getValue();
        $failures = [];

        foreach (array_keys($registry->all()) as $slug) {
            foreach (array_keys($registry->variants($slug)) ?: [null] as $variant) {
                $vars = $this->parse($registry->accessibleVars($slug, $variant));
                $c = $registry->colors($slug, $variant);

                if ($registry->isEngine($slug)) {
                    [$bg, $primary, $on] = [$c['bg'], $c['primary'], $c['on'] ?? '#ffffff'];
                } else {
                    $prefix = $c ? explode('-', array_key_first($c))[0] : null;
                    $bg = ($prefix ? ($c[$prefix.'-cream'] ?? null) : null) ?? $legacyDefaults[$slug]['bg'];
                    $primary = ($prefix ? ($c[$prefix.'-deep'] ?? null) : null) ?? $legacyDefaults[$slug]['primary'];
                    $on = '#ffffff';
                }

                $checks = [
                    'label' => Contrast::ratio($vars['--tc-label'], $bg),
                    'strong' => Contrast::ratio($vars['--tc-strong'] ?? $primary, $bg),
                    'button' => Contrast::ratio($on, $vars['--tc-btn'] ?? $primary),
                ];
                foreach ($checks as $name => $ratio) {
                    if ($ratio < 4.5) {
                        $failures[] = sprintf('%s/%s %s %.2f', $slug, $variant, $name, $ratio);
                    }
                }
            }
        }

        $this->assertSame([], $failures);
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
