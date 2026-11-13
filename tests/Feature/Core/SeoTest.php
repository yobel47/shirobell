<?php

namespace Tests\Feature\Core;

use App\Models\Invitation;
use App\Services\SettingService;
use Database\Seeders\ThemeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ThemeSeeder::class);
    }

    private function asProduction(): void
    {
        $this->app['env'] = 'production';
    }

    public function test_home_has_meta_open_graph_canonical_and_structured_data(): void
    {
        $this->asProduction();
        app(SettingService::class)->set('support_instagram', 'shirobell.id');

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('<meta name="robots" content="index, follow">', $html);
        $this->assertStringContainsString('<link rel="canonical" href="'.route('home').'">', $html);
        $this->assertStringContainsString('property="og:image" content="'.asset('og-image.png').'"', $html);
        $this->assertStringContainsString('name="twitter:card" content="summary_large_image"', $html);
        $this->assertStringContainsString('<title>Undangan Pernikahan Online', $html);
        $this->assertStringContainsString('rel="icon" href="'.asset('favicon.svg').'"', $html);

        preg_match('#<script type="application/ld\+json"[^>]*>(.*?)</script>#s', $html, $match);
        $data = json_decode($match[1] ?? '', true);
        $types = array_column($data['@graph'], '@type');
        $this->assertSame(['Organization', 'WebSite', 'Service', 'FAQPage'], $types);
        $this->assertSame(99000, $data['@graph'][2]['offers']['price']);
        $this->assertSame(['https://instagram.com/shirobell.id'], $data['@graph'][0]['sameAs']);
        $this->assertCount(5, $data['@graph'][3]['mainEntity']);
    }

    public function test_non_production_pages_are_never_indexed(): void
    {
        $this->get('/')->assertSee('<meta name="robots" content="noindex, nofollow">', false);
        $this->get('/robots.txt')->assertOk()->assertSee("Disallow: /\n", false)->assertDontSee('Sitemap:');
    }

    public function test_production_robots_blocks_private_areas_and_points_to_sitemap(): void
    {
        $this->asProduction();

        $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Disallow: /app')
            ->assertSee('Disallow: /admin')
            ->assertSee('Disallow: /penerima-tamu')
            ->assertSee('Sitemap: '.route('seo.sitemap'))
            ->assertDontSee("Disallow: /\n", false);
    }

    public function test_sitemap_lists_public_pages_and_theme_demos(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk();

        $this->assertStringStartsWith('application/xml', $response->headers->get('Content-Type'));
        $xml = simplexml_load_string($response->getContent());
        $locs = [];
        foreach ($xml->url as $url) {
            $locs[] = (string) $url->loc;
        }

        $this->assertContains(route('home'), $locs);
        $this->assertContains(route('themes.index'), $locs);
        $this->assertContains(route('themes.index', ['kategori' => 'adat']), $locs);
        $this->assertContains(route('themes.demo', 'kawung-ayu'), $locs);
        $this->assertContains(route('legal.contact'), $locs);
        $this->assertNotContains(route('login'), $locs);
    }

    public function test_catalog_category_pages_have_their_own_title_and_canonical(): void
    {
        $this->get(route('themes.index', ['kategori' => 'adat']))
            ->assertSee('<title>Tema Undangan Pernikahan Adat &amp; Tradisional', false)
            ->assertSee('<link rel="canonical" href="'.route('themes.index', ['kategori' => 'adat']).'">', false);
    }

    public function test_theme_demo_is_indexable_in_production_with_canonical_without_query(): void
    {
        $this->asProduction();

        $this->get(route('themes.demo', ['theme' => 'kawung-ayu', 'varian' => 'hitam', 'buka' => 1]))
            ->assertOk()
            ->assertSee('<meta name="robots" content="index, follow">', false)
            ->assertSee('<title>Tema Undangan Kawung Ayu', false)
            ->assertSee('<link rel="canonical" href="'.route('themes.demo', 'kawung-ayu').'">', false);
    }

    public function test_private_pages_stay_noindex_even_in_production(): void
    {
        $this->asProduction();
        $invitation = Invitation::factory()->active()->create(['slug' => 'budi-ani']);

        $this->get('http://budi-ani.'.config('app.domain'))->assertSee('<meta name="robots" content="noindex, nofollow">', false);
        $this->get(route('login'))->assertSee('<meta name="robots" content="noindex, nofollow">', false);
        $this->actingAs($invitation->user)->get(route('app.dashboard'))->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }
}
