<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fortify throttles login only. Registration and password-reset requests are
 * limited per IP here to stop account and email spam.
 */
class ThrottleAuthForms
{
    private const LIMITS = [
        'register' => 5,
        'forgot-password' => 5,
        'reset-password' => 10,
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $path = trim($request->path(), '/');

        if (! $request->isMethod('POST') || ! isset(self::LIMITS[$path])) {
            return $next($request);
        }

        $key = 'auth-form:'.$path.':'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, self::LIMITS[$path])) {
            return back()->withInput($request->except('password', 'password_confirmation'))->withErrors([
                'email' => __('auth.throttle', ['seconds' => RateLimiter::availableIn($key), 'minutes' => ceil(RateLimiter::availableIn($key) / 60)]),
            ]);
        }

        RateLimiter::hit($key, 600);

        return $next($request);
    }
}
