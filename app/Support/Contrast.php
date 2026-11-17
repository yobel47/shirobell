<?php

namespace App\Support;

/**
 * WCAG contrast helpers with perceptual (OKLab) mixing, matching CSS
 * color-mix(in oklab, ...). Used to derive accessible text and button colors
 * from a theme palette while keeping each color's hue.
 */
final class Contrast
{
    /** WCAG 2.x contrast ratio between two hex colors (1..21). */
    public static function ratio(string $a, string $b): float
    {
        [$la, $lb] = [self::luminance(self::linear($a)), self::luminance(self::linear($b))];

        return (max($la, $lb) + 0.05) / (min($la, $lb) + 0.05);
    }

    /**
     * Mix $color toward $toward in steps of 5% until it reaches $min contrast
     * against $against. Returns $color unchanged when it already passes.
     */
    public static function ensure(string $color, string $toward, string $against, float $min = 4.5): string
    {
        for ($share = 1.0; $share >= 0.0; $share -= 0.05) {
            $candidate = self::mix($color, $toward, $share);
            if (self::ratio($candidate, $against) >= $min) {
                return $candidate;
            }
        }

        return self::mix($color, $toward, 0.0);
    }

    /** color-mix(in oklab, $a $share, $b) as hex. */
    public static function mix(string $a, string $b, float $share): string
    {
        $oa = self::oklab(self::linear($a));
        $ob = self::oklab(self::linear($b));
        $mixed = array_map(fn ($i) => $oa[$i] * $share + $ob[$i] * (1 - $share), [0, 1, 2]);

        return self::hex(self::fromOklab($mixed));
    }

    /** @return array{float, float, float} linear sRGB */
    private static function linear(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        return array_map(function (int $i) use ($hex) {
            $v = hexdec(substr($hex, $i, 2)) / 255;

            return $v <= 0.04045 ? $v / 12.92 : (($v + 0.055) / 1.055) ** 2.4;
        }, [0, 2, 4]);
    }

    /** @param  array{float, float, float}  $c */
    private static function luminance(array $c): float
    {
        return 0.2126 * $c[0] + 0.7152 * $c[1] + 0.0722 * $c[2];
    }

    /** @param  array{float, float, float}  $c  linear sRGB */
    private static function oklab(array $c): array
    {
        [$r, $g, $b] = $c;
        $l = (0.4122214708 * $r + 0.5363325363 * $g + 0.0514459929 * $b) ** (1 / 3);
        $m = (0.2119034982 * $r + 0.6806995451 * $g + 0.1073969566 * $b) ** (1 / 3);
        $s = (0.0883024619 * $r + 0.2817188376 * $g + 0.6299787005 * $b) ** (1 / 3);

        return [
            0.2104542553 * $l + 0.7936177850 * $m - 0.0040720468 * $s,
            1.9779984951 * $l - 2.4285922050 * $m + 0.4505937099 * $s,
            0.0259040371 * $l + 0.7827717662 * $m - 0.8086757660 * $s,
        ];
    }

    /** @param  array{float, float, float}  $o  OKLab -> linear sRGB, clamped */
    private static function fromOklab(array $o): array
    {
        [$L, $a, $b] = $o;
        $l = ($L + 0.3963377774 * $a + 0.2158037573 * $b) ** 3;
        $m = ($L - 0.1055613458 * $a - 0.0638541728 * $b) ** 3;
        $s = ($L - 0.0894841775 * $a - 1.2914855480 * $b) ** 3;

        return array_map(fn ($v) => max(0.0, min(1.0, $v)), [
            4.0767416621 * $l - 3.3077115913 * $m + 0.2309699292 * $s,
            -1.2684380046 * $l + 2.6097574011 * $m - 0.3413193965 * $s,
            -0.0041960863 * $l - 0.7034186147 * $m + 1.7076147010 * $s,
        ]);
    }

    /** @param  array{float, float, float}  $c  linear sRGB */
    private static function hex(array $c): string
    {
        return '#'.implode('', array_map(function (float $v) {
            $s = $v <= 0.0031308 ? 12.92 * $v : 1.055 * ($v ** (1 / 2.4)) - 0.055;

            return str_pad(dechex((int) round(max(0, min(1, $s)) * 255)), 2, '0', STR_PAD_LEFT);
        }, $c));
    }
}
