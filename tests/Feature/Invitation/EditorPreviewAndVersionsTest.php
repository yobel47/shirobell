<?php

namespace Tests\Feature\Invitation;

use App\Livewire\Invitation\Editor\CoupleForm;
use App\Livewire\Invitation\Editor\ThemePicker;
use App\Livewire\Invitation\Editor\VersionHistory;
use App\Models\AdminActivityLog;
use App\Models\Invitation;
use App\Models\InvitationVersion;
use App\Models\Theme;
use App\Models\User;
use App\Services\InvitationVersionService;
use Database\Seeders\ThemeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class EditorPreviewAndVersionsTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private Invitation $invitation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ThemeSeeder::class);
        $this->owner = User::factory()->create();
        $this->invitation = Invitation::factory()->for($this->owner)->create(['groom_nickname' => 'Budi', 'bride_nickname' => 'Ani']);
        $this->invitation->events()->create(['name' => 'Akad', 'date' => '2027-02-14', 'start_time' => '08:00', 'timezone' => 'WIB', 'venue_name' => 'Masjid Raya']);
    }

    public function test_editor_page_shows_live_preview_frame(): void
    {
        $response = $this->actingAs($this->owner)->get(route('app.invitations.edit', $this->invitation));
        // The preview is a same-origin frame; CSP must allow it (regression found in browser QA).
        $this->assertStringContainsString("frame-src 'self'", $response->headers->get('Content-Security-Policy'));

        $response
            ->assertOk()
            ->assertSee('Preview undangan')
            ->assertSee('preview?buka=1\u0026frame=1', false)
            ->assertSee('Riwayat');
    }

    public function test_preview_frame_hides_banner_but_keeps_watermark_and_skips_cover(): void
    {
        $this->actingAs($this->owner)
            ->get(route('app.invitations.preview', [$this->invitation, 'buka' => 1, 'frame' => 1]))
            ->assertOk()
            ->assertDontSee('Kembali ke editor')
            ->assertSee('PREVIEW')
            ->assertSee('opened: true', false);
    }

    public function test_admin_preview_frame_reloads_are_not_audited(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('app.invitations.preview', [$this->invitation, 'frame' => 1]))->assertOk();
        $this->assertSame(0, AdminActivityLog::where('action', 'invitation.admin_open')->count());
    }

    public function test_editor_save_creates_one_auto_snapshot_per_interval(): void
    {
        $original = $this->invitation->groom_fullname;
        $component = Livewire::actingAs($this->owner)->test(CoupleForm::class, ['invitation' => $this->invitation]);

        $component->set('form.groom_fullname', 'Budi Satu')->call('save');
        $component->set('form.groom_fullname', 'Budi Dua')->call('save');

        $versions = $this->invitation->versions()->get();
        $this->assertCount(1, $versions);
        $this->assertSame('auto', $versions->first()->reason);
        // The snapshot holds the state before the first change.
        $this->assertSame($original, $versions->first()->snapshot['invitation']['groom_fullname']);

        $this->travel(InvitationVersionService::AUTO_INTERVAL_MINUTES + 1)->minutes();
        $component->set('form.groom_fullname', 'Budi Tiga')->call('save');
        $this->assertSame(2, $this->invitation->versions()->count());
    }

    public function test_restore_brings_back_content_and_saves_current_state_first(): void
    {
        Storage::fake('public');
        $service = app(InvitationVersionService::class);
        Storage::disk('public')->put('kept.webp', 'x');
        $this->invitation->stories()->create(['title' => 'Bertemu', 'body' => 'Cerita', 'photo_path' => 'kept.webp']);
        $this->invitation->stories()->create(['title' => 'Lamaran', 'body' => 'Cerita', 'photo_path' => 'gone.webp']);
        $gift = $this->invitation->giftAccounts()->make(['type' => 'bank', 'provider' => 'BCA', 'account_number' => '123', 'account_name' => 'Budi']);
        $gift->save();
        $version = $service->snapshot($this->invitation->fresh(), 'manual', $this->owner);

        $this->invitation->forceFill(['groom_nickname' => 'Diganti'])->save();
        $this->invitation->events()->delete();
        $this->invitation->stories()->delete();
        $this->invitation->giftAccounts()->delete();

        Livewire::actingAs($this->owner)
            ->test(VersionHistory::class, ['invitation' => $this->invitation])
            ->call('restore', $version->id)
            ->assertDispatched('invitation-saved');

        $invitation = $this->invitation->fresh(['events', 'stories', 'giftAccounts']);
        $this->assertSame('Budi', $invitation->groom_nickname);
        $this->assertSame(['Masjid Raya'], $invitation->events->pluck('venue_name')->all());
        $this->assertSame(['kept.webp', null], $invitation->stories->sortBy('title')->pluck('photo_path')->all());
        $this->assertSame('123', $invitation->giftAccounts->first()->account_number);

        $before = $invitation->versions()->where('reason', 'before_restore')->sole();
        $this->assertSame('Diganti', $before->snapshot['invitation']['groom_nickname']);
        $this->assertSame([], $before->snapshot['events']);
    }

    public function test_manual_save_and_pruning(): void
    {
        $service = app(InvitationVersionService::class);
        for ($i = 0; $i < InvitationVersionService::MAX_VERSIONS + 3; $i++) {
            $service->snapshot($this->invitation, 'manual');
        }
        $this->assertSame(InvitationVersionService::MAX_VERSIONS, $this->invitation->versions()->count());

        Livewire::actingAs($this->owner)
            ->test(VersionHistory::class, ['invitation' => $this->invitation])
            ->call('saveNow')
            ->assertSee('Disimpan manual');

        $this->assertSame('manual', $this->invitation->versions()->first()->reason);
    }

    public function test_other_customers_cannot_restore_and_versions_of_other_invitations_are_not_found(): void
    {
        $other = Invitation::factory()->create();
        $foreign = app(InvitationVersionService::class)->snapshot($other, 'manual');

        Livewire::actingAs(User::factory()->create())
            ->test(VersionHistory::class, ['invitation' => $this->invitation])
            ->assertForbidden();

        Livewire::actingAs($this->owner)
            ->test(VersionHistory::class, ['invitation' => $this->invitation])
            ->call('restore', $foreign->id)
            ->assertNotFound();
        $this->assertSame(1, InvitationVersion::count());
    }

    public function test_admin_restore_is_audited(): void
    {
        $version = app(InvitationVersionService::class)->snapshot($this->invitation, 'manual');

        Livewire::actingAs(User::factory()->admin()->create())
            ->test(VersionHistory::class, ['invitation' => $this->invitation])
            ->call('restore', $version->id);

        $this->assertTrue(AdminActivityLog::where('action', 'invitation.admin_edit')->where('meta->source', 'VersionHistory')->exists());
        $this->assertSame(2, InvitationVersion::count());
    }

    public function test_font_pair_is_saved_and_applied_to_engine_themes(): void
    {
        $this->invitation->forceFill(['theme_id' => Theme::firstWhere('slug', 'kawung-ayu')->id])->save();

        Livewire::actingAs($this->owner)
            ->test(ThemePicker::class, ['invitation' => $this->invitation])
            ->assertSee('Bawaan tema')
            ->call('setFontPair', 'modern')
            ->assertDispatched('invitation-saved');

        $this->assertSame('modern', $this->invitation->refresh()->customization['font_pair']);
        $this->actingAs($this->owner)
            ->get(route('app.invitations.preview', $this->invitation))
            ->assertSee('--font-heading: &#039;DM Serif Display&#039;', false);

        Livewire::actingAs($this->owner)
            ->test(ThemePicker::class, ['invitation' => $this->invitation])
            ->call('setFontPair', 'tidak-ada');
        $this->assertNull($this->invitation->refresh()->customization);
    }
}
