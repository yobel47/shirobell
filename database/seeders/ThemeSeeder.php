<?php

namespace Database\Seeders;

use App\Models\Theme;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    /**
     * Themes without a finished Blade view stay inactive (built in M6).
     * Existing rows keep admin edits; only missing themes are inserted.
     */
    public function run(): void
    {
        $themes = [
            ['slug' => 'rose-garden', 'name' => 'Rose Garden', 'description' => 'Nuansa mawar lembut, romantis, dan elegan.', 'badge' => 'new', 'sort_order' => 1, 'is_active' => true],
            ['slug' => 'classic-gold', 'name' => 'Classic Gold', 'description' => 'Hitam dan emas yang mewah dan klasik.', 'badge' => null, 'sort_order' => 2, 'is_active' => false],
            ['slug' => 'sage-minimalist', 'name' => 'Sage Minimalist', 'description' => 'Hijau sage yang tenang dan minimalis.', 'badge' => null, 'sort_order' => 3, 'is_active' => false],
        ];

        foreach ($themes as $theme) {
            Theme::query()->firstOrCreate(['slug' => $theme['slug']], $theme);
        }
    }
}
