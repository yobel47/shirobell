<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use App\Services\SettingService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(SettingService $settings): View
    {
        return view('home', [
            'price' => $settings->packagePrice(),
            'durationDays' => $settings->packageDurationDays(),
            'galleryMax' => $settings->int('gallery_max_photos'),
            'domain' => config('app.domain'),
            'themes' => Theme::query()->available()->get()->filter->hasView()->values(),
        ]);
    }
}
