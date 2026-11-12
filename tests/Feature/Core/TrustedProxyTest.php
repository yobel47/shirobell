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
