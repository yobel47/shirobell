<?php

namespace Tests\Feature\Invitation;

use App\Livewire\Invitation\Editor\ThemePicker;
use App\Models\Invitation;
use App\Models\Theme;
use App\Models\User;
use App\Services\ThemeRegistry;
use Database\Seeders\ThemeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ThemeEngineTest extends TestCase
{
    use RefreshDatabase;

    private const OPTIONS = [
        'hero' => ['arch', 'photo', 'editorial', 'frame'],
        'shape' => ['arch', 'circle', 'rounded', 'square'],
        'heading' => ['script', 'serif', 'caps'],
        'sections' => ['cards', 'plain', 'bordered'],
        'ornament' => ['floral', 'leaf', 'kawung', 'parang', 'cloud', 'lotus', 'tenun', 'songket', 'deco', 'star', 'wave', 'line'],
        'pattern' => ['none', 'kawung', 'parang', 'cloud', 'tenun', 'songket', 'star', 'deco', 'dots', 'leaf', 'wave'],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ThemeSeeder::class);
    }

    public function test_theme_config_is_valid(): void
    {
        $registry = app(ThemeRegistry::class);
        $fonts = config('themes.fonts');
        $css = file_get_contents(resource_path('css/themes/patterns.css'));

        $this->assertGreaterThanOrEqual(25, count($registry->all()));

        foreach ($registry->all() as $slug => $definition) {
            $this->assertMatchesRegularExpression('/^[a-z0-9-]+$/', $slug);
            $this->assertArrayHasKey($definition['category'], $registry->categories(), "$slug: unknown category");
            $this->assertNotEmpty($definition['variants'], "$slug: no variants");

            foreach ($definition['variants'] as $key => $variant) {
                $this->assertMatchesRegularExpression('/^[a-z0-9-]+$/', (string) $key);
                $this->assertNotEmpty($variant['name']);
                foreach ($variant['colors'] as $hex) {
                    $this->assertMatchesRegularExpression('/^#[0-9a-f]{6}$/i', $hex, "$slug.$key");
                }
                if (isset($definition['engine'])) {
                    $this->assertSame(ThemeRegistry::ENGINE_TOKENS, array_keys($variant['colors']), "$slug.$key: tokens");
                }
            }

            if ($engine = $registry->engine($slug)) {
                foreach (self::OPTIONS as $option => $allowed) {
                    $this->assertContains($engine[$option], $allowed, "$slug: $option");
                }
                foreach ($engine['fonts'] as $font) {
                    $this->assertArrayHasKey($font, $fonts, "$slug: font $font");
                }
                if ($engine['pattern'] !== 'none') {
                    $this->assertStringContainsString('.t-pattern-'.$engine['pattern'], $css);
                }
            }
        }
    }

    public function test_seeder_registers_every_configured_theme(): void
    {
        $this->assertEqualsCanonicalizing(array_keys(config('themes.themes')), Theme::pluck('slug')->all());
        $this->assertTrue(Theme::firstWhere('slug', 'kawung-ayu')->hasView());
    }

    public function test_public_page_applies_selected_variant_colors_and_fonts(): void
    {
        $invitation = Invitation::factory()->active()->create([
            'slug' => 'budi-ani',
            'theme_id' => Theme::firstWhere('slug', 'noir-gilded')->id,
        ]);
        $invitation->forceFill(['theme_variant' => 'perak'])->save();

        $this->get('http://budi-ani.'.config('app.domain'))
            ->assertOk()
            ->assertSee('--color-t-primary: #c9ced6', false)
            ->assertSee('--font-heading:', false)
            ->assertSee('t-pattern-deco', false);
    }

    public function test_unknown_variant_falls_back_to_default(): void
    {
        $registry = app(ThemeRegistry::class);

        $this->assertSame('sogan', $registry->variantKey('kawung-ayu', 'tidak-ada'));
        $this->assertStringContainsString('--color-t-primary: #6b4423', $registry->style('kawung-ayu', 'tidak-ada'));
    }

    public function test_handcrafted_theme_variant_overrides_its_own_tokens(): void
    {
        $this->get(route('themes.demo', ['theme' => 'rose-garden', 'varian' => 'peach']))
            ->assertOk()
            ->assertSee('--color-rg-deep: #a3563f', false);
    }

    public function test_editor_theme_picker_filters_by_category_and_saves_variant(): void
    {
        $owner = User::factory()->create();
        $invitation = Invitation::factory()->for($owner)->create();
        $songket = Theme::firstWhere('slug', 'songket-minang');

        Livewire::actingAs($owner)
            ->test(ThemePicker::class, ['invitation' => $invitation])
            ->set('category', 'adat')
            ->assertSee('Songket Minang')
            ->assertDontSee('Noir Gilded')
            ->call('choose', $songket->id, 'krem')
            ->assertDispatched('invitation-saved');

        $invitation->refresh();
        $this->assertSame($songket->id, $invitation->theme_id);
        $this->assertSame('krem', $invitation->theme_variant);

        Livewire::actingAs($owner)
            ->test(ThemePicker::class, ['invitation' => $invitation])
            ->call('choose', $songket->id, 'bukan-varian');

        $this->assertNull($invitation->refresh()->theme_variant);
    }

    public function test_catalog_lists_themes_and_filters_by_category(): void
    {
        $total = count(config('themes.themes'));

        $this->get(route('themes.index'))
            ->assertOk()
            ->assertSee("{$total} tema")
            ->assertSee(['Kawung Ayu', 'Noir Gilded', 'Rose Garden'])
            ->assertSee(route('themes.demo', 'kawung-ayu'), false);

        $this->get(route('themes.index', ['kategori' => 'adat']))
            ->assertOk()
            ->assertSee('Kawung Ayu')
            ->assertDontSee('Noir Gilded');

        $this->get(route('themes.index', ['kategori' => 'ngawur']))->assertOk()->assertSee('Noir Gilded');
    }

    public function test_cover_can_be_skipped_for_previews(): void
    {
        $this->get(route('themes.demo', ['theme' => 'kawung-ayu', 'buka' => 1]))
            ->assertOk()
            ->assertSee('opened: true', false);
    }
}
