<?php

namespace Database\Factories;

use App\Models\Theme;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Theme> */
class ThemeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'slug' => 'rose-garden',
            'name' => 'Rose Garden',
            'description' => 'Nuansa mawar lembut dan elegan.',
            'sort_order' => 1,
            'is_active' => true,
        ];
    }
}
