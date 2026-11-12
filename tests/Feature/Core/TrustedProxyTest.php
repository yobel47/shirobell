<?php

namespace Tests\Feature\Core;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Behind the Docker reverse proxy, rate limits must see the real client IP
 * and Laravel must know the request was HTTPS (bootstrap/app.php).
 */
class TrustedProxyTest extends TestCase
{
    private function probe(): void
    {
        Route::get('/_test-ip', fn (Request $request) => $request->ip().'|'.($request->secure() ? 'https' : 'http'));
    }

    public function test_forwarded_headers_are_ignored_without_trusted_proxies(): void
    {
        $this->probe();

        $this->get('/_test-ip', ['X-Forwarded-For' => '203.0.113.9', 'X-Forwarded-Proto' => 'https'])
            ->assertSee('127.0.0.1|http');
    }

    /**
     * Production chain: client -> Cloudflare -> NPM (Docker) -> app. Only Docker
     * and Cloudflare ranges are trusted, so a forged left-most X-Forwarded-For
     * entry cannot replace the real client IP added by Cloudflare.
     */
    public function test_forged_forwarded_for_cannot_hide_the_real_client(): void
    {
        $proxies = '172.16.0.0/12,173.245.48.0/20,162.158.0.0/15';
        putenv("TRUSTED_PROXIES={$proxies}");
        $_ENV['TRUSTED_PROXIES'] = $_SERVER['TRUSTED_PROXIES'] = $proxies;
        $this->refreshApplication();
        $this->probe();

        try {
            $this->withServerVariables(['REMOTE_ADDR' => '172.19.0.2'])
                ->get('/_test-ip', ['X-Forwarded-For' => '1.2.3.4, 203.0.113.9, 162.158.10.20', 'X-Forwarded-Proto' => 'https'])
                ->assertSee('203.0.113.9|https');

            // Staging has no Cloudflare: NPM appends the real client after any forged value.
            $this->withServerVariables(['REMOTE_ADDR' => '172.19.0.2'])
                ->get('/_test-ip', ['X-Forwarded-For' => '1.2.3.4, 198.51.100.7'])
                ->assertSee('198.51.100.7|http');
        } finally {
            putenv('TRUSTED_PROXIES');
            unset($_ENV['TRUSTED_PROXIES'], $_SERVER['TRUSTED_PROXIES']);
        }
    }

    public function test_client_ip_and_https_come_from_the_proxy_when_trusted(): void
    {
        putenv('TRUSTED_PROXIES=*');
        $_ENV['TRUSTED_PROXIES'] = $_SERVER['TRUSTED_PROXIES'] = '*';
        $this->refreshApplication();
        $this->probe();

        try {
            $this->get('/_test-ip', ['X-Forwarded-For' => '203.0.113.9, 172.18.0.5', 'X-Forwarded-Proto' => 'https'])
                ->assertSee('203.0.113.9|https');
        } finally {
            putenv('TRUSTED_PROXIES');
            unset($_ENV['TRUSTED_PROXIES'], $_SERVER['TRUSTED_PROXIES']);
        }
    }
}
