<?php

use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\ThrottleAuthForms;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Global, so unmatched routes (404), the Filament panel and Livewire
        // endpoints all get the same headers.
        $middleware->append(SecurityHeaders::class);

        // Behind a reverse proxy (Docker deploy) the client IP and HTTPS come
        // from X-Forwarded-* headers. Rate limits are per IP, so this matters.
        // TRUSTED_PROXIES: "*" when the app is only reachable through the proxy,
        // or a comma separated list of proxy IPs/CIDRs. Empty = trust none.
        if ($proxies = env('TRUSTED_PROXIES')) {
            $middleware->trustProxies(at: $proxies === '*' ? '*' : array_map('trim', explode(',', $proxies)));
        }

        $middleware->alias([
            'role' => EnsureUserHasRole::class,
        ]);

        $middleware->redirectUsersTo(fn ($request) => $request->user()?->isAdmin() ? '/admin' : route('app.dashboard'));
        $middleware->web(append: [ThrottleAuthForms::class]);
        // Payment notifications carry their own signature instead of a CSRF token.
        $middleware->validateCsrfTokens(except: ['webhooks/*']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
