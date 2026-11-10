<?php

namespace App\Providers;

use App\Services\SettingService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingService::class);
    }

    public function boot(): void
    {
        // Generated URLs (payment callbacks, invitation links, emails) are always HTTPS in production.
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        // Composed lazily so artisan commands work before the settings table exists.
        View::composer(['components.layouts.*', 'components.invitation.*', 'themes.*', 'invitation-status'], function ($view): void {
            $view->with('siteName', app(SettingService::class)->get('site_name'));
        });
    }
}
