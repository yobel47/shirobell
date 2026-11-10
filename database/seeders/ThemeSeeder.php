<?php

namespace Database\Seeders;

use App\Models\Theme;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    /**
     * Existing rows keep admin edits; only missing themes are inserted.
     * A theme can only be activated when its Blade view exists (ThemeResource).
     */
    public function run(): void
    {
        $themes = [
            ['slug' => 'rose-garden', 'name' => 'Rose Garden', 'description' => 'Nuansa mawar lembut, romantis, dan elegan.', 'badge' => 'new', 'sort_order' => 1, 'is_active' => true],
            ['slug' => 'classic-gold', 'name' => 'Classic Gold', 'description' => 'Gading, hitam, dan emas yang klasik dan mewah.', 'badge' => null, 'sort_order' => 2, 'is_active' => true],
            ['slug' => 'sage-minimalist', 'name' => 'Sage Minimalist', 'description' => 'Hijau sage yang tenang, bersih, dan minimalis.', 'badge' => null, 'sort_order' => 3, 'is_active' => true],
        ];

        // Engine themes are declared in config/themes.php (ADR 009).
        foreach (config('themes.themes') as $slug => $definition) {
            if (! isset($definition['engine'])) {
                continue;
            }
            $themes[] = [
                'slug' => $slug,
                'name' => $definition['name'],
                'description' => $definition['description'] ?? null,
                'badge' => 'new',
                'sort_order' => $definition['sort'] ?? 100,
                'is_active' => true,
            ];
        }

        foreach ($themes as $theme) {
            Theme::query()->firstOrCreate(['slug' => $theme['slug']], $theme);
        }
    }
}
