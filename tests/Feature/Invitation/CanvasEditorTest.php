<?php

namespace Tests\Feature\Invitation;

use App\Livewire\Invitation\Editor\CanvasEditor;
use App\Models\Invitation;
use App\Models\Theme;
use App\Models\User;
use App\Services\CanvasService;
use App\Services\InvitationVersionService;
use App\Services\MediaService;
use Database\Seeders\ThemeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class CanvasEditorTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private Invitation $invitation;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed(ThemeSeeder::class);
        $this->owner = User::factory()->create();
        $this->invitation = Invitation::factory()->active()->for($this->owner)->create([
            'slug' => 'budi-ani', 'groom_nickname' => 'Budi', 'bride_nickname' => 'Ani',
            'theme_id' => Theme::firstWhere('slug', 'kayon-sakral')->id,
        ]);
    }

    private function text(string $text, array $extra = []): array
    {
        return array_merge(['id' => 'abc123', 'type' => 'text', 'x' => 20, 'y' => 100, 'w' => 350, 'h' => 80, 'r' => 0,
            'props' => ['text' => $text, 'font' => 'script', 'size' => 40, 'color' => 'primary', 'align' => 'center', 'weight' => 400, 'spacing' => 0]], $extra);
    }

    public function test_sanitize_drops_unknown_types_clamps_values_and_keeps_an_open_button(): void
    {
        $clean = app(CanvasService::class)->sanitize($this->invitation, [
            'enabled' => true,
            'bg' => 'url(javascript:alert(1))',
            'elements' => [
                ['type' => 'script', 'props' => ['code' => 'alert(1)']],
                $this->text('<b>Halo</b> {groom}', ['x' => 99999, 'w' => -5, 'r' => 900, 'props' => ['text' => '<b>Halo</b> {groom}', 'font' => 'comic', 'color' => 'red; background:url(x)', 'size' => 9999]]),
                ['type' => 'image', 'props' => ['path' => 'invitations/orang-lain/canvas/x.webp']],
                ['type' => 'sticker', 'props' => ['set' => 'corner', 'key' => '../../etc/passwd']],
            ],
        ]);

        $this->assertSame('ink', $clean['bg']);
        $types = array_column($clean['elements'], 'type');
        $this->assertSame(['text', 'sticker', 'button'], $types);

        $text = $clean['elements'][0];
        $this->assertSame('Halo {groom}', $text['props']['text']);
        $this->assertSame('body', $text['props']['font']);
        $this->assertSame('ink', $text['props']['color']);
        $this->assertSame(160.0, $text['props']['size']);
        $this->assertSame(780.0, $text['x']);
        $this->assertSame(4.0, $text['w']);
        $this->assertSame(360.0, $text['r']);
        $this->assertSame('floral', $clean['elements'][1]['props']['key']);
    }

    public function test_hidden_open_button_is_forced_visible_and_element_count_is_capped(): void
    {
        $button = ['type' => 'button', 'hidden' => true, 'x' => 0, 'y' => 0, 'w' => 100, 'h' => 40, 'props' => []];
        $many = array_fill(0, 80, $this->text('x'));

        $clean = app(CanvasService::class)->sanitize($this->invitation, ['elements' => [$button, ...$many]]);

        $this->assertCount(CanvasService::MAX_ELEMENTS, $clean['elements']);
        $this->assertFalse($clean['elements'][0]['hidden']);
    }

    public function test_owner_saves_design_from_editor_and_public_cover_uses_it(): void
    {
        $template = Livewire::actingAs($this->owner)
            ->test(CanvasEditor::class, ['invitation' => $this->invitation])
            ->assertSee('Desain bawaan tema')
            ->instance()->template(app(CanvasService::class));
        $this->assertContains('button', array_column($template['elements'], 'type'));

        $design = ['enabled' => true, 'bg' => '#123456', 'pattern' => false, 'elements' => [
            $this->text("{groom}\n&\n{bride}"),
            ['type' => 'sticker', 'x' => 0, 'y' => 640, 'w' => 390, 'h' => 200, 'props' => ['set' => 'scene', 'key' => 'gunungan']],
            ['type' => 'button', 'x' => 110, 'y' => 700, 'w' => 170, 'h' => 48, 'props' => ['text' => 'Masuk', 'color' => 'primary', 'textColor' => 'on']],
        ]];

        Livewire::actingAs($this->owner)
            ->test(CanvasEditor::class, ['invitation' => $this->invitation])
            ->call('save', $design)
            ->assertDispatched('notify');

        $this->assertSame('#123456', $this->invitation->refresh()->canvas['cover']['bg']);

        $this->get('http://budi-ani.'.config('app.domain').'/?to=Pak+Joko')
            ->assertOk()
            ->assertSee("Budi\n&amp;\nAni", false)
            ->assertSee('cqw', false)
            ->assertSee('Masuk')
            ->assertSee('background-color: #123456', false);
    }

    public function test_disabled_design_and_handcrafted_themes_keep_the_recipe_cover(): void
    {
        app(CanvasService::class)->save($this->invitation, ['enabled' => false, 'elements' => [$this->text('Desain Saya')]]);
        $this->get('http://budi-ani.'.config('app.domain'))->assertOk()->assertDontSee('Desain Saya');

        app(CanvasService::class)->save($this->invitation, ['enabled' => true, 'elements' => [$this->text('Desain Saya')]]);
        $this->invitation->forceFill(['theme_id' => Theme::firstWhere('slug', 'rose-garden')->id])->save();
        $this->get('http://budi-ani.'.config('app.domain'))->assertOk()->assertDontSee('Desain Saya');

        Livewire::actingAs($this->owner)
            ->test(CanvasEditor::class, ['invitation' => $this->invitation->refresh()])
            ->assertSee('Desain bebas tersedia untuk tema selain');
    }

    public function test_photo_upload_is_stored_in_the_invitation_canvas_folder(): void
    {
        $result = Livewire::actingAs($this->owner)
            ->test(CanvasEditor::class, ['invitation' => $this->invitation])
            ->set('photo', UploadedFile::fake()->image('foto.jpg', 2400, 1600))
            ->instance()->storePhoto(app(MediaService::class), app(CanvasService::class));

        $this->assertStringStartsWith($this->invitation->mediaDirectory().'/canvas/', $result['path']);
        Storage::disk('public')->assertExists($result['path']);

        $clean = app(CanvasService::class)->sanitize($this->invitation, ['elements' => [['type' => 'image', 'x' => 0, 'y' => 0, 'w' => 100, 'h' => 100, 'props' => ['path' => $result['path']]]]]);
        $this->assertSame($result['path'], $clean['elements'][0]['props']['path']);

        Livewire::actingAs($this->owner)
            ->test(CanvasEditor::class, ['invitation' => $this->invitation])
            ->set('photo', UploadedFile::fake()->create('x.svg', 5, 'image/svg+xml'))
            ->call('storePhoto')
            ->assertHasErrors('photo');
    }

    public function test_other_customers_cannot_edit_and_design_is_versioned(): void
    {
        Livewire::actingAs(User::factory()->create())
            ->test(CanvasEditor::class, ['invitation' => $this->invitation])
            ->assertForbidden();

        app(CanvasService::class)->save($this->invitation, ['enabled' => true, 'elements' => [$this->text('Versi Satu')]]);
        $version = app(InvitationVersionService::class)->snapshot($this->invitation, 'manual');
        app(CanvasService::class)->save($this->invitation, ['enabled' => true, 'elements' => [$this->text('Versi Dua')]]);

        app(InvitationVersionService::class)->restore($version);
        $this->assertSame('Versi Satu', $this->invitation->refresh()->canvas['cover']['elements'][0]['props']['text']);
    }

    public function test_editor_tab_is_available_without_preview_column(): void
    {
        $this->actingAs($this->owner)
            ->get(route('app.invitations.edit', [$this->invitation, 'desain']))
            ->assertOk()
            ->assertSee('Desain Sampul')
            ->assertSee('canvasEditor(', false)
            ->assertDontSee('Preview undangan');
    }
}
