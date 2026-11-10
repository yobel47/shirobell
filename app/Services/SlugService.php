<?php

namespace App\Services;

use App\Models\Invitation;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Subdomain rules: 3-40 chars, lowercase letters, digits and single hyphens,
 * not starting or ending with a hyphen, not reserved, unique (soft-deleted included).
 */
class SlugService
{
    public const PATTERN = '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';

    public function normalize(string $value): string
    {
        return Str::limit(Str::slug($value), 40, '');
    }

    /** @return array<int, mixed> */
    public function rules(?Invitation $ignore = null): array
    {
        return [
            'required',
            'string',
            'min:3',
            'max:40',
            'regex:'.self::PATTERN,
            Rule::notIn($this->reserved()),
            Rule::unique('invitations', 'slug')->ignore($ignore?->id),
        ];
    }

    public function isReserved(string $slug): bool
    {
        return in_array($slug, $this->reserved(), true);
    }

    public function isAvailable(string $slug, ?Invitation $ignore = null): bool
    {
        if (strlen($slug) < 3 || ! preg_match(self::PATTERN, $slug) || $this->isReserved($slug)) {
            return false;
        }

        return ! Invitation::withTrashed()
            ->where('slug', $slug)
            ->when($ignore, fn ($q) => $q->whereKeyNot($ignore->id))
            ->exists();
    }

    public function suggest(string $groom, string $bride): string
    {
        $base = trim($this->normalize($groom).'-'.$this->normalize($bride), '-');
        $base = Str::limit($base, 34, '');
        $base = strlen($base) >= 3 ? rtrim($base, '-') : 'undangan';

        if ($this->isAvailable($base)) {
            return $base;
        }

        for ($i = 2; $i <= 20; $i++) {
            if ($this->isAvailable("{$base}-{$i}")) {
                return "{$base}-{$i}";
            }
        }

        return $base.'-'.Str::lower(Str::random(4));
    }

    /** @return array<int, string> */
    private function reserved(): array
    {
        return config('shirobell.reserved_slugs', []);
    }
}
