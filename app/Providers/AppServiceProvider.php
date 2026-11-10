<?php

namespace App\Providers;

use App\Services\Admin\AdminEditAudit;
use App\Services\SettingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingService::class);
        // Request-scoped: state must never leak between requests or queue jobs.
        $this->app->scoped(AdminEditAudit::class);
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

        // Admin edits made through the customer editor (see AdminEditAudit).
        Event::listen(['eloquent.saved: *', 'eloquent.deleted: *'], function (string $event, array $payload): void {
            $model = $payload[0] ?? null;
            if (! $model instanceof Model || ! in_array($model::class, AdminEditAudit::MODELS, true)) {
                return;
            }

            $audit = app(AdminEditAudit::class);
            if ($audit->active()) {
                $audit->record($model, str_starts_with($event, 'eloquent.deleted') ? 'deleted' : 'saved');
            }
        });
    }
}
