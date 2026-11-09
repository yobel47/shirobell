<?php

namespace Tests\Feature\Core;

use App\Services\SettingService;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_defaults_when_not_stored(): void
    {
        $settings = app(SettingService::class);

        $this->assertSame(99000, $settings->packagePrice());
        $this->assertSame(90, $settings->packageDurationDays());
        $this->assertSame(20, $settings->int('gallery_max_photos'));
    }

    public function test_set_invalidates_cache(): void
    {
        $settings = app(SettingService::class);
        $this->assertSame(99000, $settings->packagePrice());

        $settings->set('package_price', 120000);

        $this->assertSame(120000, $settings->packagePrice());
    }

    public function test_seeder_does_not_overwrite_admin_changes(): void
    {
        $settings = app(SettingService::class);
        $settings->set('package_price', 75000);

        $this->seed(SettingSeeder::class);

        $this->assertSame(75000, $settings->packagePrice());
    }
}
