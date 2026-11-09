<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class SettingSeeder extends Seeder
{
    /**
     * Insert defaults only for keys that do not exist yet, so re-seeding never
     * overwrites values the admin already changed.
     */
    public function run(): void
    {
        foreach (SettingService::DEFAULTS as $key => $value) {
            Setting::query()->firstOrCreate(['key' => $key], ['value' => (string) $value]);
        }

        Cache::forget('settings.all');
    }
}
