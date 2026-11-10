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
        // The receptionist QR scanner needs the camera; everything else keeps it off.
        $camera = $request->is('penerima-tamu/*') ? 'camera=(self)' : 'camera=()';
        $response->headers->set('Permissions-Policy', "geolocation=(), microphone=(), {$camera}, payment=()");

        if (config('security.csp_enabled')) {
            $policy = $request->is('admin', 'admin/*') ? $this->adminPolicy() : $this->contentSecurityPolicy();
            $response->headers->set('Content-Security-Policy', $policy);
        }

        if ($request->secure() && app()->isProduction()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }

    private function contentSecurityPolicy(): string
    {
        $nonce = Vite::cspNonce();
        // Browsers reject IPv6 literals in CSP sources, so only named hosts are listed.
        $devServer = app()->isLocal() ? ' http://localhost:5173 http://127.0.0.1:5173' : '';
        $devSocket = app()->isLocal() ? ' ws://localhost:5173 ws://127.0.0.1:5173' : '';
        // Uploaded media is served from the main domain, also on invitation subdomains.
        $appOrigin = ' '.$this->appOrigin();

        // 'unsafe-eval' is required by Alpine (bundled with Livewire) for inline
        // expressions. Tracked in dev-docs/ai/TECHNICAL_DEBT.md (Livewire csp_safe).
        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}' 'unsafe-eval'{$devServer}",
            "style-src 'self' 'unsafe-inline'{$devServer}",
            "img-src 'self' data: blob: https:{$appOrigin}",
            "font-src 'self' data:{$devServer}",
            "media-src 'self' blob:{$appOrigin}",
            "connect-src 'self'{$devSocket}{$devServer}",
            // 'self': the editor's live preview frame.
            "frame-src 'self' https://www.google.com https://maps.google.com",
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "form-action 'self' https://app.midtrans.com https://app.sandbox.midtrans.com",
            "object-src 'none'",
        ]);
    }

    private function appOrigin(): string
    {
        $url = parse_url((string) config('app.url'));
        $port = isset($url['port']) ? ':'.$url['port'] : '';

        return ($url['scheme'] ?? 'https').'://'.($url['host'] ?? 'localhost').$port;
    }

    /**
     * Filament renders inline <script> blocks without a nonce, so the admin
     * panel (admin-only, behind login) allows 'unsafe-inline' scripts.
     * Tracked in dev-docs/ai/TECHNICAL_DEBT.md (TD-005).
     */
    private function adminPolicy(): string
    {
        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'",
            "style-src 'self' 'unsafe-inline'",
            "img-src 'self' data: blob: https:",
            "font-src 'self' data:",
            "media-src 'self' blob:",
            "connect-src 'self'",
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "object-src 'none'",
        ]);
    }
}
