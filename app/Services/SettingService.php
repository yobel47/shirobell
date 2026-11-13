<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * Key-value business settings managed by admin (price, duration, limits).
 * Values are cached as a single array and invalidated on every write.
 */
class SettingService
{
    private const CACHE_KEY = 'settings.all';

    /**
     * Fallback values used when a key has not been stored yet.
     *
     * @var array<string, string|int>
     */
    public const DEFAULTS = [
        'site_name' => 'Shirobell',
        'package_price' => 99000,
        'package_duration_days' => 90,
        'gallery_max_photos' => 20,
        'support_whatsapp' => '',
        'support_email' => '',
        'support_instagram' => '',
        'business_name' => '',
        'business_address' => '',
        // Event-day module (ADR 011): 'included' in the package, or a paid 'addon'.
        'event_day_mode' => 'included',
        'event_day_price' => 49000,
    ];

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default ?? (self::DEFAULTS[$key] ?? null);
    }

    public function int(string $key): int
    {
        return (int) $this->get($key);
    }

    public function set(string $key, mixed $value): void
    {
        Setting::query()->updateOrCreate(['key' => $key], ['value' => (string) $value]);
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return array<string, string|null>
     */
    public function all(): array
    {
        return Cache::rememberForever(
            self::CACHE_KEY,
            fn () => Setting::query()->pluck('value', 'key')->all()
        );
    }

    /**
     * Public support contacts with ready-to-use links (empty values stay null).
     *
     * @return array{whatsapp: ?string, whatsapp_url: ?string, instagram: ?string, instagram_url: ?string, email: ?string}
     */
    public function supportContacts(): array
    {
        $whatsapp = trim((string) $this->get('support_whatsapp')) ?: null;
        $instagram = ltrim(trim((string) $this->get('support_instagram')), '@') ?: null;
        $email = trim((string) $this->get('support_email')) ?: null;

        $digits = $whatsapp ? preg_replace('/\D/', '', $whatsapp) : '';
        $waNumber = str_starts_with($digits, '0') ? '62'.substr($digits, 1) : $digits;

        return [
            'whatsapp' => $whatsapp,
            'whatsapp_url' => $waNumber !== '' ? 'https://wa.me/'.$waNumber : null,
            'instagram' => $instagram,
            'instagram_url' => $instagram ? 'https://instagram.com/'.$instagram : null,
            'email' => $email,
        ];
    }

    public function packagePrice(): int
    {
        return $this->int('package_price');
    }

    public function packageDurationDays(): int
    {
        return $this->int('package_duration_days');
    }
}
