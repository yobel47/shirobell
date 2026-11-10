<?php

namespace Tests\Feature\EventDay;

use App\Livewire\Invitation\EventDayManager;
use App\Livewire\Reception\CheckinApp;
use App\Livewire\Reception\WelcomeScreen;
use App\Models\Guest;
use App\Models\Invitation;
use App\Models\User;
use App\Services\EventDayService;
use App\Services\GuestService;
use App\Services\ReceptionAccess;
use Database\Seeders\ThemeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class SelfieCheckinTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private Invitation $invitation;

    private Guest $guest;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed(ThemeSeeder::class);
        $this->owner = User::factory()->create();
        $this->invitation = Invitation::factory()->active()->for($this->owner)->create();
        $this->guest = app(GuestService::class)->create($this->invitation, ['name' => 'Pak Joko']);
        app(EventDayService::class)->enable($this->invitation);
        $this->invitation->refresh();
        app(ReceptionAccess::class)->grant($this->invitation);
    }

    private function enableSelfie(): void
    {
        Livewire::actingAs($this->owner)
            ->test(EventDayManager::class, ['invitation' => $this->invitation])
            ->call('toggleSelfie');
        $this->invitation->refresh();
        auth()->logout();
    }

    public function test_selfie_is_off_by_default_and_cannot_be_saved(): void
    {
        $this->assertFalse($this->invitation->checkin_selfie);

        Livewire::test(CheckinApp::class, ['invitation' => $this->invitation])
            ->call('scan', $this->guest->code)
            ->assertDontSee('Ambil foto tamu')
            ->set('selfie', UploadedFile::fake()->image('selfie.jpg', 800, 600))
            ->call('saveSelfie')
            ->assertNotFound();
    }

    public function test_receptionist_takes_and_replaces_a_selfie(): void
    {
        $this->enableSelfie();

        $app = Livewire::test(CheckinApp::class, ['invitation' => $this->invitation])
            ->call('scan', $this->guest->code)
            ->assertSee('Ambil foto tamu')
            ->set('selfie', UploadedFile::fake()->image('selfie.jpg', 2000, 1500))
            ->call('saveSelfie')
            ->assertHasNoErrors();

        $first = $this->guest->refresh()->selfie_path;
        $this->assertStringStartsWith($this->invitation->mediaDirectory().'/selfies/', $first);
        $this->assertStringEndsWith('.webp', $first);
        Storage::disk('public')->assertExists($first);
        $this->assertLessThanOrEqual(EventDayService::SELFIE_MAX_PX, getimagesizefromstring(Storage::disk('public')->get($first))[0]);

        $app->set('selfie', UploadedFile::fake()->image('again.jpg', 800, 600))->call('saveSelfie');
        Storage::disk('public')->assertMissing($first);
        Storage::disk('public')->assertExists($this->guest->refresh()->selfie_path);
    }

    public function test_non_image_upload_is_rejected(): void
    {
        $this->enableSelfie();

        Livewire::test(CheckinApp::class, ['invitation' => $this->invitation])
            ->call('scan', $this->guest->code)
            ->set('selfie', UploadedFile::fake()->create('virus.php', 10, 'application/x-php'))
            ->call('saveSelfie')
            ->assertHasErrors('selfie');

        $this->assertNull($this->guest->refresh()->selfie_path);
    }

    public function test_selfie_shows_on_welcome_screen_and_owner_gallery_and_can_be_deleted(): void
    {
        $this->enableSelfie();
        $service = app(EventDayService::class);
        $service->checkIn($this->invitation, $this->guest, 1);
        $service->setSelfie($this->invitation, $this->guest, UploadedFile::fake()->image('s.jpg', 600, 600));
        $path = $this->guest->refresh()->selfie_path;

        Livewire::test(WelcomeScreen::class, ['invitation' => $this->invitation])->assertSee($this->guest->selfieUrl());

        Livewire::actingAs($this->owner)
            ->test(EventDayManager::class, ['invitation' => $this->invitation])
            ->assertSee('Selfie check-in')
            ->assertSee($this->guest->selfieUrl())
            ->call('deleteSelfie', $this->guest->id);

        $this->assertNull($this->guest->refresh()->selfie_path);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_privacy_policy_mentions_event_photos(): void
    {
        $this->get(route('legal.privacy'))->assertSee('Foto tamu di hari acara');
    }
}
