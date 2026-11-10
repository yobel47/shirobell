<?php

namespace Tests\Feature\Invitation;

use App\Models\Invitation;
use App\Models\Theme;
use App\Services\ThemeRegistry;
use Database\Seeders\ThemeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ThemesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ThemeSeeder::class);
    }

    /** Every theme in config/themes.php (read directly: providers run before boot). */
    public static function themes(): array
    {
        $config = require __DIR__.'/../../../config/themes.php';

        return array_map(fn ($slug) => [$slug], array_combine(array_keys($config['themes']), array_keys($config['themes'])));
    }

    #[DataProvider('themes')]
    public function test_every_theme_renders_a_full_invitation(string $slug): void
    {
        $invitation = Invitation::factory()->active()->create([
            'slug' => 'tes-'.$slug,
            'theme_id' => Theme::firstWhere('slug', $slug)->id,
            'quote_text' => 'Kutipan contoh',
        ]);
        $invitation->events()->create(['name' => 'Resepsi', 'date' => now()->addMonth()->toDateString(), 'start_time' => '10:00', 'timezone' => 'WIB', 'venue_name' => 'Gedung Melati']);
        $invitation->stories()->create(['title' => 'Pertama Bertemu', 'body' => 'Cerita']);
        $invitation->giftAccounts()->create(['type' => 'bank', 'provider' => 'BCA', 'account_number' => '123', 'account_name' => 'Budi']);
        $invitation->wishes()->create(['name' => 'Rina', 'message' => 'Selamat berbahagia']);

        $this->get('http://tes-'.$slug.'.'.config('app.domain').'/?to=Pak+Joko')
            ->assertOk()
            ->assertSee('/build/assets/'.(app(ThemeRegistry::class)->isEngine($slug) ? 'engine' : $slug).'-', false)
            ->assertSee(['Pak Joko', 'Gedung Melati', 'Pertama Bertemu', 'Amplop Digital', 'Selamat berbahagia', 'Kutipan contoh']);
    }

    #[DataProvider('themes')]
    public function test_every_theme_has_a_public_demo(string $slug): void
    {
        $this->get(route('themes.demo', $slug))
            ->assertOk()
            ->assertSee(['Contoh tema', 'Raka', 'Nadia', 'Akad Nikah', 'Dimas'], false);
    }

    public function test_inactive_theme_demo_is_not_found(): void
    {
        Theme::where('slug', 'sage-minimalist')->update(['is_active' => false]);

        $this->get(route('themes.demo', 'sage-minimalist'))->assertNotFound();
    }

    public function test_landing_lists_active_themes_with_demo_links(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(['Rose Garden', 'Classic Gold', 'Sage Minimalist'])
            ->assertSee(route('themes.demo', 'classic-gold'), false);
    }

    public function test_main_domain_routes_do_not_answer_on_subdomains(): void
    {
        $base = 'http://budi.'.config('app.domain');

        $this->get($base.'/login')->assertNotFound();
        $this->get($base.'/app')->assertNotFound();
        $this->get($base.'/admin/login')->assertNotFound();
        $this->get($base.'/health')->assertNotFound();
    }
}
