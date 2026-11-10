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
        'business_name' => '',
        'business_address' => '',
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

    public function packagePrice(): int
    {
        return $this->int('package_price');
    }

    public function packageDurationDays(): int
    {
        return $this->int('package_duration_days');
    }
}
