<?php

namespace Tests\Feature\Invitation;

use App\Enums\InvitationStatus;
use App\Livewire\Invitation\Editor\CoupleForm;
use App\Livewire\Invitation\Editor\EventsManager;
use App\Livewire\Invitation\Editor\GalleryManager;
use App\Livewire\Invitation\Editor\SettingsForm;
use App\Models\Invitation;
use App\Models\User;
use App\Services\SettingService;
use Database\Seeders\ThemeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class InvitationEditorTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private Invitation $invitation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ThemeSeeder::class);
        Storage::fake('public');

        $this->owner = User::factory()->create();
        $this->invitation = Invitation::factory()->for($this->owner)->create(['slug' => 'budi-ani']);
    }

    public function test_owner_can_open_every_editor_tab(): void
    {
        foreach (['mempelai', 'acara', 'galeri', 'cerita', 'teks', 'musik', 'tema', 'pengaturan'] as $tab) {
            $this->actingAs($this->owner)
                ->get(route('app.invitations.edit', [$this->invitation, $tab]))
                ->assertOk();
        }
    }

    public function test_unknown_tab_returns_404(): void
    {
        $this->actingAs($this->owner)
            ->get(route('app.invitations.edit', [$this->invitation, 'rahasia']))
            ->assertNotFound();
    }

    public function test_other_customer_cannot_open_editor_or_preview(): void
    {
        $intruder = User::factory()->create();

        $this->actingAs($intruder)->get(route('app.invitations.edit', $this->invitation))->assertForbidden();
        $this->actingAs($intruder)->get(route('app.invitations.preview', $this->invitation))->assertForbidden();
    }

    public function test_other_customer_cannot_mount_editor_component(): void
    {
        Livewire::actingAs(User::factory()->create())
            ->test(CoupleForm::class, ['invitation' => $this->invitation])
            ->assertForbidden();
    }

    public function test_couple_form_saves_data_and_normalizes_instagram(): void
    {
        Livewire::actingAs($this->owner)
            ->test(CoupleForm::class, ['invitation' => $this->invitation])
            ->set('form.groom_fullname', 'Budi Santoso, S.T.')
            ->set('form.groom_instagram', 'https://instagram.com/budi.s')
            ->set('form.bride_instagram', '@ani_lestari')
            ->set('groomPhoto', UploadedFile::fake()->image('budi.jpg', 2000, 3000))
            ->call('save')
            ->assertHasNoErrors();

        $this->invitation->refresh();
        $this->assertSame('Budi Santoso, S.T.', $this->invitation->groom_fullname);
        $this->assertSame('budi.s', $this->invitation->groom_instagram);
        $this->assertSame('ani_lestari', $this->invitation->bride_instagram);
        $this->assertStringEndsWith('.webp', $this->invitation->groom_photo_path);
        Storage::disk('public')->assertExists($this->invitation->groom_photo_path);
    }

    public function test_couple_form_cannot_mass_assign_protected_fields(): void
    {
        Livewire::actingAs($this->owner)
            ->test(CoupleForm::class, ['invitation' => $this->invitation])
            ->set('form.status', 'active')
            ->set('form.slug', 'dibajak')
            ->call('save');

        $this->invitation->refresh();
        $this->assertSame(InvitationStatus::Draft, $this->invitation->status);
        $this->assertSame('budi-ani', $this->invitation->slug);
    }

    public function test_non_image_upload_is_rejected(): void
    {
        Livewire::actingAs($this->owner)
            ->test(CoupleForm::class, ['invitation' => $this->invitation])
            ->set('groomPhoto', UploadedFile::fake()->create('shell.php', 10, 'application/x-php'))
            ->call('save')
            ->assertHasErrors('groomPhoto');
    }

    public function test_events_can_be_added_and_foreign_events_are_not_reachable(): void
    {
        $component = Livewire::actingAs($this->owner)
            ->test(EventsManager::class, ['invitation' => $this->invitation])
            ->call('create')
            ->set('form.name', 'Akad Nikah')
            ->set('form.date', '2027-02-14')
            ->set('form.start_time', '08:00')
            ->set('form.venue_name', 'Masjid Raya')
            ->set('form.maps_url', 'https://maps.app.goo.gl/abc')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(1, $this->invitation->events()->count());

        $foreign = Invitation::factory()->create()->events()->create([
            'name' => 'Resepsi', 'date' => '2027-02-14', 'start_time' => '10:00', 'timezone' => 'WIB', 'venue_name' => 'Gedung',
        ]);

        $component->call('delete', $foreign->id)->assertNotFound();
        $this->assertDatabaseHas('invitation_events', ['id' => $foreign->id]);
    }

    public function test_maps_url_must_be_https(): void
    {
        Livewire::actingAs($this->owner)
            ->test(EventsManager::class, ['invitation' => $this->invitation])
            ->call('create')
            ->set('form.date', '2027-02-14')
            ->set('form.start_time', '08:00')
            ->set('form.venue_name', 'Masjid Raya')
            ->set('form.maps_url', 'javascript:alert(1)')
            ->call('save')
            ->assertHasErrors('form.maps_url');
    }

    public function test_gallery_respects_max_photos_setting(): void
    {
        app(SettingService::class)->set('gallery_max_photos', 2);

        Livewire::actingAs($this->owner)
            ->test(GalleryManager::class, ['invitation' => $this->invitation])
            ->set('uploads', [
                UploadedFile::fake()->image('1.jpg'),
                UploadedFile::fake()->image('2.jpg'),
                UploadedFile::fake()->image('3.jpg'),
            ])
            ->assertHasErrors('uploads');

        $this->assertSame(0, $this->invitation->photos()->count());

        Livewire::actingAs($this->owner)
            ->test(GalleryManager::class, ['invitation' => $this->invitation])
            ->set('uploads', [UploadedFile::fake()->image('1.jpg'), UploadedFile::fake()->image('2.jpg')])
            ->assertHasNoErrors();

        $this->assertSame(2, $this->invitation->photos()->count());
    }

    public function test_slug_can_change_while_draft_but_not_after_publish(): void
    {
        Livewire::actingAs($this->owner)
            ->test(SettingsForm::class, ['invitation' => $this->invitation])
            ->set('slug', 'budi-dan-ani')
            ->call('saveSlug')
            ->assertHasNoErrors();

        $this->assertSame('budi-dan-ani', $this->invitation->refresh()->slug);

        $published = Invitation::factory()->active()->for($this->owner)->create(['slug' => 'sudah-terbit']);

        Livewire::actingAs($this->owner)
            ->test(SettingsForm::class, ['invitation' => $published])
            ->set('slug', 'ganti-alamat')
            ->call('saveSlug')
            ->assertForbidden();

        $this->assertSame('sudah-terbit', $published->refresh()->slug);
    }

    public function test_draft_can_be_deleted_and_frees_slug(): void
    {
        Livewire::actingAs($this->owner)
            ->test(SettingsForm::class, ['invitation' => $this->invitation])
            ->call('deleteInvitation')
            ->assertRedirect(route('app.dashboard'));

        $this->assertSoftDeleted('invitations', ['id' => $this->invitation->id]);
        $this->assertDatabaseMissing('invitations', ['slug' => 'budi-ani']);
    }

    public function test_active_invitation_cannot_be_deleted(): void
    {
        $active = Invitation::factory()->active()->for($this->owner)->create();

        Livewire::actingAs($this->owner)
            ->test(SettingsForm::class, ['invitation' => $active])
            ->call('deleteInvitation')
            ->assertForbidden();

        $this->assertNotSoftDeleted('invitations', ['id' => $active->id]);
    }
}
