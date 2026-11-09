<?php

namespace Tests\Feature\Core;

use App\Services\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_shows_price_from_settings(): void
    {
        app(SettingService::class)->set('package_price', 149000);
        app(SettingService::class)->set('package_duration_days', 90);

        $this->get('/')
            ->assertOk()
            ->assertSee('Rp 149.000')
            ->assertSee('aktif 90 hari');
    }

    public function test_home_page_falls_back_to_default_settings(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Rp 99.000');
    }

    public function test_security_headers_are_present(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->assertStringContainsString("object-src 'none'", $response->headers->get('Content-Security-Policy'));
        $this->assertStringContainsString("'nonce-", $response->headers->get('Content-Security-Policy'));
    }

    public function test_health_endpoint_reports_ok(): void
    {
        $this->getJson('/health')
            ->assertOk()
            ->assertJson(['status' => 'ok', 'database' => 'ok']);
    }
}
