<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security headers per ai-rules/security Part B.
 * Scripts are nonce-based (Vite + Livewire read Vite::cspNonce()).
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        Vite::useCspNonce();

        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=()');

        if (config('security.csp_enabled')) {
            $response->headers->set('Content-Security-Policy', $this->contentSecurityPolicy());
        }

        if ($request->secure() && app()->isProduction()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }

    private function contentSecurityPolicy(): string
    {
        $nonce = Vite::cspNonce();
        $devServer = app()->isLocal() ? ' http://localhost:5173 http://127.0.0.1:5173 http://[::1]:5173' : '';
        $devSocket = app()->isLocal() ? ' ws://localhost:5173 ws://127.0.0.1:5173 ws://[::1]:5173' : '';

        // 'unsafe-eval' is required by Alpine (bundled with Livewire) for inline
        // expressions. Tracked in dev-docs/ai/TECHNICAL_DEBT.md (Livewire csp_safe).
        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}' 'unsafe-eval'{$devServer}",
            "style-src 'self' 'unsafe-inline'{$devServer}",
            "img-src 'self' data: blob: https:",
            "font-src 'self' data:{$devServer}",
            "media-src 'self' blob:",
            "connect-src 'self'{$devSocket}{$devServer}",
            'frame-src https://www.google.com https://maps.google.com',
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "form-action 'self' https://checkout.xendit.co https://checkout-staging.xendit.co",
            "object-src 'none'",
        ]);
    }
}
