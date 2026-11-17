<?php

namespace App\Services;

use App\Models\Invitation;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Storage;

/**
 * Link-preview card (og:image, 1200x630) for an invitation, drawn with GD in the
 * invitation's own theme colors: frame, "The Wedding of", names, ornament, date,
 * and the cover photo on the left when there is one.
 *
 * Cached on the public disk; the file name carries a hash of everything drawn,
 * so editing names, date, theme or cover produces a fresh image (and a fresh URL,
 * which also busts WhatsApp's preview cache).
 */
class OgImageService
{
    private const W = 1200;

    private const H = 630;

    /** Bump when the drawing changes so every card is regenerated. */
    private const VERSION = '1';

    private const FONTS = [
        'italic' => 'cormorant-400-italic.woff',
        'medium' => 'cormorant-500.woff',
        'bold' => 'cormorant-600.woff',
    ];

    public function __construct(private ThemeRegistry $themes) {}

    /** Public URL of the card, rendering it first when it does not exist yet. */
    public function url(Invitation $invitation): string
    {
        $invitation->loadMissing(['theme', 'events']);
        $date = $invitation->events->first()?->date;

        $hash = substr(md5(implode('|', [
            self::VERSION,
            $invitation->groom_nickname,
            $invitation->bride_nickname,
            $date?->toDateString(),
            $invitation->theme->slug,
            $invitation->theme_variant,
            $invitation->cover_photo_path,
        ])), 0, 12);

        $disk = Storage::disk('public');
        $path = "og/{$invitation->uuid}-{$hash}.png";

        if (! $disk->exists($path)) {
            // Drop this invitation's previous cards before writing the new one.
            foreach ($disk->files('og') as $old) {
                if (str_starts_with(basename($old), $invitation->uuid.'-')) {
                    $disk->delete($old);
                }
            }
            $disk->put($path, $this->render($invitation, $date));
        }

        // Link previews need an absolute URL.
        return url($disk->url($path));
    }

    /** PNG bytes of the card. */
    public function render(Invitation $invitation, ?CarbonInterface $date): string
    {
        [$bg, $primary, $accent] = array_map($this->rgb(...), $this->themes->swatch($invitation->theme->slug, $invitation->theme_variant));

        $im = imagecreatetruecolor(self::W, self::H);
        imagealphablending($im, true);
        imagefill($im, 0, 0, $this->color($im, $bg));

        // Text area: the full card, or the right side when a cover photo fills the left.
        $x0 = 0;
        $cover = $this->coverImage($invitation);
        if ($cover) {
            $this->drawCover($im, $cover, 0, 0, 470, self::H);
            $x0 = 470;
        }

        // Double frame in the accent color.
        $line = $this->color($im, $accent, 40);
        $thin = $this->color($im, $accent, 85);
        imagesetthickness($im, 2);
        imagerectangle($im, $x0 + 34, 34, self::W - 35, self::H - 35, $line);
        imagesetthickness($im, 1);
        imagerectangle($im, $x0 + 44, 44, self::W - 45, self::H - 45, $thin);

        $cx = (int) (($x0 + self::W) / 2);
        $maxWidth = self::W - $x0 - 160;
        $groom = $invitation->groom_nickname ?: 'Mempelai Pria';
        $bride = $invitation->bride_nickname ?: 'Mempelai Wanita';
        $ink = $this->color($im, $primary);
        $soft = $this->color($im, $this->mix($primary, $bg, 0.78));
        $gold = $this->color($im, $accent);

        // Names on one line when they fit at a generous size, otherwise stacked.
        $oneLine = "{$groom} & {$bride}";
        $size = $this->fit($oneLine, 'italic', 104, $maxWidth);
        $stacked = $size < 70;

        $this->tracked($im, 'THE WEDDING OF', 'bold', 19, $cx, $stacked ? 150 : 205, $gold, 7);

        if ($stacked) {
            $s = min($this->fit($groom, 'italic', 96, $maxWidth), $this->fit($bride, 'italic', 96, $maxWidth));
            $this->centered($im, $groom, 'italic', $s, $cx, 250, $ink);
            $this->centered($im, '&', 'italic', (int) ($s * 0.55), $cx, 250 + (int) ($s * 0.88), $gold);
            $this->centered($im, $bride, 'italic', $s, $cx, 250 + (int) ($s * 1.9), $ink);
            $ornamentY = 250 + (int) ($s * 1.9) + 50;
        } else {
            $this->centered($im, $oneLine, 'italic', $size, $cx, 315, $ink);
            $ornamentY = 370;
        }

        // Ornament: rule, diamond, rule.
        imagesetthickness($im, 2);
        imageline($im, $cx - 120, $ornamentY, $cx - 18, $ornamentY, $gold);
        imageline($im, $cx + 18, $ornamentY, $cx + 120, $ornamentY, $gold);
        imagefilledpolygon($im, [$cx, $ornamentY - 8, $cx + 8, $ornamentY, $cx, $ornamentY + 8, $cx - 8, $ornamentY], $gold);

        if ($date) {
            $this->centered($im, $date->translatedFormat('l, d F Y'), 'medium', 32, $cx, $ornamentY + 70, $soft);
        }

        ob_start();
        imagepng($im, null, 7);
        $png = (string) ob_get_clean();
        imagedestroy($im);
        if ($cover) {
            imagedestroy($cover);
        }

        return $png;
    }

    /** @return \GdImage|null */
    private function coverImage(Invitation $invitation)
    {
        $path = $invitation->cover_photo_path;
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        $image = @imagecreatefromstring((string) Storage::disk('public')->get($path));

        return $image ?: null;
    }

    /** Object-fit: cover into the given box. */
    private function drawCover(\GdImage $im, \GdImage $src, int $x, int $y, int $w, int $h): void
    {
        $sw = imagesx($src);
        $sh = imagesy($src);
        $scale = max($w / $sw, $h / $sh);
        $cw = (int) round($w / $scale);
        $ch = (int) round($h / $scale);
        imagecopyresampled($im, $src, $x, $y, (int) (($sw - $cw) / 2), (int) (($sh - $ch) / 2), $w, $h, $cw, $ch);
    }

    private function font(string $style): string
    {
        return resource_path('fonts/og/'.self::FONTS[$style]);
    }

    private function width(string $text, string $style, int $size): int
    {
        $box = imagettfbbox($size, 0, $this->font($style), $text);

        return (int) abs($box[2] - $box[0]);
    }

    /** Largest size (down to 40) at which the text fits the width. */
    private function fit(string $text, string $style, int $max, int $width): int
    {
        $size = $max;
        while ($size > 40 && $this->width($text, $style, $size) > $width) {
            $size -= 4;
        }

        return $size;
    }

    private function centered(\GdImage $im, string $text, string $style, int $size, int $cx, int $baseline, int $color): void
    {
        imagettftext($im, $size, 0, $cx - (int) ($this->width($text, $style, $size) / 2), $baseline, $color, $this->font($style), $text);
    }

    /** Letter-spaced caps (GD has no tracking, so draw glyph by glyph). */
    private function tracked(\GdImage $im, string $text, string $style, int $size, int $cx, int $baseline, int $color, int $spacing): void
    {
        $chars = mb_str_split($text);
        $total = array_sum(array_map(fn ($c) => $this->width($c, $style, $size) + $spacing, $chars)) - $spacing;
        $x = $cx - (int) ($total / 2);
        foreach ($chars as $c) {
            imagettftext($im, $size, 0, $x, $baseline, $color, $this->font($style), $c);
            $x += $this->width($c, $style, $size) + $spacing;
        }
    }

    /** @return array{int, int, int} */
    private function rgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
    }

    /**
     * @param  array{int, int, int}  $a
     * @param  array{int, int, int}  $b
     * @return array{int, int, int}
     */
    private function mix(array $a, array $b, float $weightA): array
    {
        return [
            (int) round($a[0] * $weightA + $b[0] * (1 - $weightA)),
            (int) round($a[1] * $weightA + $b[1] * (1 - $weightA)),
            (int) round($a[2] * $weightA + $b[2] * (1 - $weightA)),
        ];
    }

    /** @param  array{int, int, int}  $rgb  alpha: 0 opaque .. 127 transparent */
    private function color(\GdImage $im, array $rgb, int $alpha = 0): int
    {
        return imagecolorallocatealpha($im, $rgb[0], $rgb[1], $rgb[2], $alpha);
    }
}
