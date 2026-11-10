<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use App\Services\ThemeRegistry;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Public theme catalog with category filter (/tema?kategori=adat). */
class ThemeCatalogController extends Controller
{
    public function __invoke(Request $request, ThemeRegistry $registry): View
    {
        $categories = $registry->categories();
        $category = array_key_exists($request->string('kategori')->toString(), $categories)
            ? $request->string('kategori')->toString()
            : null;

        $all = Theme::query()->available()->get()->filter->hasView()->values();
        $counts = $all->countBy(fn (Theme $theme) => $theme->category());

        return view('catalog', [
            'themes' => $category ? $all->filter(fn (Theme $theme) => $theme->category() === $category)->values() : $all,
            'categories' => $categories,
            'counts' => $counts,
            'total' => $all->count(),
            'category' => $category,
        ]);
    }
}
