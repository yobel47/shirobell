<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use App\Services\ThemeDemoService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ThemeDemoController extends Controller
{
    public function __invoke(Request $request, Theme $theme, ThemeDemoService $demo): Response
    {
        abort_unless($theme->is_active && $theme->hasView(), 404);

        return response()->view($theme->viewName(), $demo->data($theme, $request->string('varian')->toString() ?: null));
    }
}
