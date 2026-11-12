<?php

namespace App\Services;

use App\Models\Invitation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Free-form canvas designs (ADR 012). Stage 1: the cover page.
 *
 * Elements live on a 390 x 844 artboard (phone size) in artboard units and
 * are rendered with percentages and container query units, so the design
 * scales to any screen. Everything coming from the editor is untrusted and
 * normalized by sanitize() before it is stored.
 */
class CanvasService
{
    public const WIDTH = 390;

    public const HEIGHT = 844;

    public const MAX_ELEMENTS = 60;

    public const MAX_IMAGES = 20;

    public const TYPES = ['text', 'image', 'sticker', 'shape', 'button'];

    public const FONTS = ['heading', 'script', 'body'];

    public const TOKENS = ['primary', 'accent', 'ink', 'on', 'surface', 'soft', 'bg'];

    public const PLACEHOLDERS = ['{groom}', '{bride}', '{date}', '{guest}'];

    /** Built-in sections of engine themes, in default order (themes/_engine/sections/*). */
    public const SECTIONS = [
        'hero' => 'Hero (foto & nama)',
        'opening' => 'Kutipan & pembuka',
        'couple' => 'Profil mempelai',
        'countdown' => 'Hitung mundur',
        'events' => 'Waktu & tempat',
        'story' => 'Cerita cinta',
        'gallery' => 'Galeri',
        'gift' => 'Amplop digital',
        'rsvp' => 'Tiket, RSVP & ucapan',
        'closing' => 'Penutup',
    ];

    public const MAX_PAGES = 10;

    public function __construct(private readonly ThemeRegistry $themes) {}

    /** @return array{enabled: bool, bg: string, elements: list<array<string, mixed>>}|null */
    public function cover(Invitation $invitation): ?array
    {
        $cover = $invitation->canvas['cover'] ?? null;

        return is_array($cover) ? $cover : null;
    }

    public function coverEnabled(Invitation $invitation): bool
    {
        return (bool) ($this->cover($invitation)['enabled'] ?? false)
            && $this->themes->isEngine($invitation->theme->slug);
    }

    /** Starting design built from the current theme recipe. */
    public function template(Invitation $invitation): array
    {
        $engine = $this->themes->engine($invitation->theme->slug) ?? [];
        $scene = $engine['illustration'] ?? null;
        $hanging = in_array($scene, ['lampion', 'melati', 'taman'], true);
        $el = fn (string $type, float $x, float $y, float $w, float $h, array $props, float $r = 0) => [
            'id' => Str::random(8), 'type' => $type, 'x' => $x, 'y' => $y, 'w' => $w, 'h' => $h, 'r' => $r, 'hidden' => false, 'locked' => false, 'props' => $props,
        ];

        $elements = [];
        if ($scene) {
            $elements[] = $el('sticker', 0, $hanging ? 0 : 640, 390, 204, ['set' => 'scene', 'key' => $scene, 'opacity' => 1]);
        }
        $family = $engine['ornament'] ?? 'line';
        $elements[] = $el('sticker', 0, 0, 130, 130, ['set' => 'corner', 'key' => $family, 'opacity' => 1]);
        $elements[] = $el('sticker', 260, 0, 130, 130, ['set' => 'corner', 'key' => $family, 'opacity' => 1, 'flipX' => true]);
        $base = $hanging ? 170 : 110;
        $elements[] = $el('sticker', 85, $base, 220, 64, ['set' => 'crown', 'key' => $family, 'opacity' => 1]);
        $elements[] = $el('text', 45, $base + 80, 300, 24, ['text' => 'THE WEDDING OF', 'font' => 'body', 'size' => 12, 'color' => 'accent', 'align' => 'center', 'weight' => 500, 'spacing' => 0.35]);
        $elements[] = $el('text', 20, $base + 108, 350, 150, ['text' => "{groom}\n&\n{bride}", 'font' => 'script', 'size' => 38, 'color' => 'primary', 'align' => 'center', 'weight' => 400, 'spacing' => 0]);
        $elements[] = $el('text', 45, $base + 272, 300, 24, ['text' => '{date}', 'font' => 'body', 'size' => 14, 'color' => 'ink', 'align' => 'center', 'weight' => 400, 'spacing' => 0.1]);
        $elements[] = $el('text', 45, $base + 320, 300, 20, ['text' => 'Kepada Yth. Bapak/Ibu/Saudara/i', 'font' => 'body', 'size' => 12, 'color' => 'ink', 'align' => 'center', 'weight' => 400, 'spacing' => 0]);
        $elements[] = $el('text', 30, $base + 344, 330, 34, ['text' => '{guest}', 'font' => 'heading', 'size' => 22, 'color' => 'ink', 'align' => 'center', 'weight' => 600, 'spacing' => 0]);
        $elements[] = $el('button', 110, $base + 404, 170, 48, ['text' => 'Buka Undangan', 'color' => 'primary', 'textColor' => 'on', 'size' => 14, 'radius' => 999]);

        return ['enabled' => true, 'bg' => 'bg', 'pattern' => true, 'elements' => $elements];
    }

    /**
     * Normalizes an editor payload. Unknown keys and element types are
     * dropped; numbers are clamped; texts are limited; images must belong to
     * the invitation's own media folder; a cover always keeps one open button.
     */
    public function sanitize(Invitation $invitation, mixed $cover): array
    {
        $cover = is_array($cover) ? $cover : [];
        $elements = $this->sanitizeElements($invitation, $cover['elements'] ?? [], self::HEIGHT, true);

        // Guests must always be able to open the invitation.
        $buttons = array_values(array_filter($elements, fn ($e) => $e['type'] === 'button'));
        if ($buttons === []) {
            $elements[] = ['id' => Str::random(8), 'type' => 'button', 'x' => 110, 'y' => 700, 'w' => 170, 'h' => 48, 'r' => 0, 'hidden' => false, 'locked' => false,
                'props' => ['text' => 'Buka Undangan', 'color' => 'primary', 'textColor' => 'on', 'size' => 14, 'radius' => 999]];
        } else {
            foreach ($elements as &$e) {
                if ($e['type'] === 'button') {
                    $e['hidden'] = false;
                }
            }
            unset($e);
        }

        return [
            'enabled' => (bool) ($cover['enabled'] ?? true),
            'bg' => $this->color($cover['bg'] ?? 'bg'),
            'pattern' => (bool) ($cover['pattern'] ?? true),
            'elements' => $elements,
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function sanitizeElements(Invitation $invitation, mixed $items, float $height, bool $allowButton): array
    {
        $stickers = $this->stickerKeys();
        $mediaPrefix = $invitation->mediaDirectory().'/canvas/';
        $types = $allowButton ? self::TYPES : array_values(array_diff(self::TYPES, ['button']));
        $elements = [];
        $images = 0;

        foreach (array_slice(array_values(is_array($items) ? $items : []), 0, self::MAX_ELEMENTS) as $raw) {
            if (! is_array($raw) || ! in_array($raw['type'] ?? null, $types, true)) {
                continue;
            }
            $element = $this->sanitizeElement($raw, $height, $stickers, $mediaPrefix);
            if ($element['type'] === 'image' && ($element['props']['path'] === null || ++$images > self::MAX_IMAGES)) {
                continue;
            }
            $elements[] = $element;
        }

        return $elements;
    }

    /**
     * Whole design document: cover, free pages, section order and hidden sections.
     *
     * @return array{cover: array<string, mixed>, pages: list<array<string, mixed>>, order: list<string>, hidden: list<string>}
     */
    public function sanitizeDocument(Invitation $invitation, mixed $doc): array
    {
        $doc = is_array($doc) ? $doc : [];
        $pages = [];
        foreach (array_slice(array_values(is_array($doc['pages'] ?? null) ? $doc['pages'] : []), 0, self::MAX_PAGES) as $raw) {
            if (! is_array($raw)) {
                continue;
            }
            $height = $this->num($raw['height'] ?? 600, 200, 3000);
            $id = preg_match('/^[A-Za-z0-9]{1,16}$/', (string) ($raw['id'] ?? '')) ? (string) $raw['id'] : Str::random(8);
            $pages[] = [
                'id' => $id,
                'name' => mb_substr(strip_tags((string) ($raw['name'] ?? 'Halaman')), 0, 40) ?: 'Halaman',
                'height' => $height,
                'bg' => $this->color($raw['bg'] ?? 'bg'),
                'pattern' => (bool) ($raw['pattern'] ?? false),
                'elements' => $this->sanitizeElements($invitation, $raw['elements'] ?? [], $height, false),
            ];
        }
        $pageIds = array_column($pages, 'id');

        $order = [];
        foreach (is_array($doc['order'] ?? null) ? $doc['order'] : [] as $entry) {
            $entry = (string) $entry;
            $valid = array_key_exists($entry, self::SECTIONS)
                || (str_starts_with($entry, 'page:') && in_array(substr($entry, 5), $pageIds, true));
            if ($valid && ! in_array($entry, $order, true)) {
                $order[] = $entry;
            }
        }
        // Anything missing (new sections or pages) goes to its natural place at the end.
        foreach ([...array_keys(self::SECTIONS), ...array_map(fn ($id) => 'page:'.$id, $pageIds)] as $entry) {
            if (! in_array($entry, $order, true)) {
                $order[] = $entry;
            }
        }

        $hidden = array_values(array_intersect(array_keys(self::SECTIONS), is_array($doc['hidden'] ?? null) ? $doc['hidden'] : []));

        return [
            'cover' => $this->sanitize($invitation, $doc['cover'] ?? []),
            'pages' => $pages,
            'order' => $order,
            'hidden' => $hidden,
        ];
    }

    public function saveDocument(Invitation $invitation, mixed $doc): void
    {
        $invitation->forceFill(['canvas' => $this->sanitizeDocument($invitation, $doc)])->save();
    }

    /** Document for the editor, with defaults for invitations that never saved one. */
    public function document(Invitation $invitation): array
    {
        $canvas = $invitation->canvas ?? [];
        $doc = $this->sanitizeDocument($invitation, [
            'cover' => $canvas['cover'] ?? $this->template($invitation),
            'pages' => $canvas['pages'] ?? [],
            'order' => $canvas['order'] ?? [],
            'hidden' => $canvas['hidden'] ?? [],
        ]);
        if (! isset($canvas['cover'])) {
            $doc['cover']['enabled'] = false;
        }

        return $doc;
    }

    /** Sections and free pages to render, in order (engine themes only). */
    public function sectionOrder(Invitation $invitation): array
    {
        $canvas = $invitation->canvas ?? [];
        if (! isset($canvas['order'])) {
            return array_keys(self::SECTIONS);
        }

        return array_values(array_diff($canvas['order'], $canvas['hidden'] ?? []));
    }

    /** @return array<string, array<string, mixed>> free pages by id */
    public function pages(Invitation $invitation): array
    {
        return collect($invitation->canvas['pages'] ?? [])->keyBy('id')->all();
    }

    public function save(Invitation $invitation, mixed $cover): void
    {
        $canvas = $invitation->canvas ?? [];
        $canvas['cover'] = $this->sanitize($invitation, $cover);
        $invitation->forceFill(['canvas' => $canvas])->save();
    }

    public function disable(Invitation $invitation): void
    {
        $canvas = $invitation->canvas ?? [];
        if (isset($canvas['cover'])) {
            $canvas['cover']['enabled'] = false;
            $invitation->forceFill(['canvas' => $canvas])->save();
        }
    }

    /** Placeholder values for texts on the public page. */
    public function resolve(string $text, Invitation $invitation, ?string $date, ?string $guest): string
    {
        return strtr($text, [
            '{groom}' => $invitation->groom_nickname ?: 'Mempelai Pria',
            '{bride}' => $invitation->bride_nickname ?: 'Mempelai Wanita',
            '{date}' => (string) $date,
            '{guest}' => $guest ?: 'Tamu Undangan',
        ]);
    }

    /** CSS color for a token name or hex value. */
    public static function css(string $color): string
    {
        return in_array($color, self::TOKENS, true) ? "var(--color-t-{$color})" : ($color === 'none' ? 'transparent' : $color);
    }

    public function imageUrl(?string $path): ?string
    {
        return $path ? Storage::disk(MediaService::DISK)->url($path) : null;
    }

    /** @return array<string, list<string>> */
    public function stickerKeys(): array
    {
        $ornaments = ['floral', 'leaf', 'kawung', 'parang', 'cloud', 'lotus', 'tenun', 'songket', 'deco', 'star', 'wave', 'line'];

        return [
            'corner' => $ornaments,
            'crown' => $ornaments,
            'divider' => $ornaments,
            'scene' => ['bouquet', 'wildflowers', 'eucalyptus', 'frangipani', 'gunungan', 'candi', 'masjid', 'pantai', 'sawah',
                'rumah-gadang', 'joglo', 'pinisi', 'lampion', 'kota', 'pegunungan', 'taman', 'melati', 'bambu'],
        ];
    }

    /** @param array<string, list<string>> $stickers */
    private function sanitizeElement(array $raw, float $height, array $stickers, string $mediaPrefix): array
    {
        $props = is_array($raw['props'] ?? null) ? $raw['props'] : [];
        $element = [
            'id' => preg_match('/^[A-Za-z0-9]{1,16}$/', (string) ($raw['id'] ?? '')) ? (string) $raw['id'] : Str::random(8),
            'type' => $raw['type'],
            'x' => $this->num($raw['x'] ?? 0, -self::WIDTH, self::WIDTH * 2),
            'y' => $this->num($raw['y'] ?? 0, -$height, $height * 2),
            'w' => $this->num($raw['w'] ?? 100, 4, self::WIDTH * 3),
            'h' => $this->num($raw['h'] ?? 100, 4, $height * 3),
            'r' => $this->num($raw['r'] ?? 0, -360, 360),
            'hidden' => (bool) ($raw['hidden'] ?? false),
            'locked' => (bool) ($raw['locked'] ?? false),
        ];

        $element['props'] = match ($element['type']) {
            'text' => [
                'text' => mb_substr(strip_tags((string) ($props['text'] ?? '')), 0, 300),
                'font' => in_array($props['font'] ?? null, self::FONTS, true) ? $props['font'] : 'body',
                'size' => $this->num($props['size'] ?? 16, 6, 160),
                'color' => $this->color($props['color'] ?? 'ink'),
                'align' => in_array($props['align'] ?? null, ['left', 'center', 'right'], true) ? $props['align'] : 'center',
                'weight' => in_array($weight = (int) ($props['weight'] ?? 400), [300, 400, 500, 600, 700], true) ? $weight : 400,
                'spacing' => $this->num($props['spacing'] ?? 0, -0.1, 1),
                'italic' => (bool) ($props['italic'] ?? false),
            ],
            'image' => [
                'path' => is_string($props['path'] ?? null) && str_starts_with($props['path'], $mediaPrefix) && ! str_contains($props['path'], '..') ? $props['path'] : null,
                'fit' => ($props['fit'] ?? 'cover') === 'contain' ? 'contain' : 'cover',
                'radius' => $this->num($props['radius'] ?? 0, 0, 999),
                'opacity' => $this->num($props['opacity'] ?? 1, 0, 1),
                'border' => $this->num($props['border'] ?? 0, 0, 20),
                'borderColor' => $this->color($props['borderColor'] ?? 'surface'),
            ],
            'sticker' => [
                'set' => $set = (in_array($props['set'] ?? null, ['corner', 'crown', 'divider', 'scene'], true) ? $props['set'] : 'corner'),
                'key' => in_array($props['key'] ?? null, $stickers[$set], true) ? $props['key'] : $stickers[$set][0],
                'opacity' => $this->num($props['opacity'] ?? 1, 0, 1),
                'flipX' => (bool) ($props['flipX'] ?? false),
                'flipY' => (bool) ($props['flipY'] ?? false),
            ],
            'shape' => [
                'shape' => in_array($props['shape'] ?? null, ['rect', 'circle', 'arch', 'line'], true) ? $props['shape'] : 'rect',
                'fill' => $this->color($props['fill'] ?? 'soft', true),
                'stroke' => $this->color($props['stroke'] ?? 'accent', true),
                'strokeWidth' => $this->num($props['strokeWidth'] ?? 0, 0, 20),
                'radius' => $this->num($props['radius'] ?? 0, 0, 999),
                'opacity' => $this->num($props['opacity'] ?? 1, 0, 1),
            ],
            'button' => [
                'text' => mb_substr(strip_tags((string) ($props['text'] ?? 'Buka Undangan')), 0, 40) ?: 'Buka Undangan',
                'color' => $this->color($props['color'] ?? 'primary'),
                'textColor' => $this->color($props['textColor'] ?? 'on'),
                'size' => $this->num($props['size'] ?? 14, 8, 40),
                'radius' => $this->num($props['radius'] ?? 999, 0, 999),
            ],
        };

        return $element;
    }

    private function num(mixed $value, float $min, float $max): float
    {
        return round(max($min, min($max, is_numeric($value) ? (float) $value : $min)), 2);
    }

    private function color(mixed $value, bool $allowNone = false): string
    {
        if (in_array($value, self::TOKENS, true) || ($allowNone && $value === 'none')) {
            return $value;
        }

        return is_string($value) && preg_match('/^#[0-9a-fA-F]{6}$/', $value) ? strtolower($value) : 'ink';
    }
}
