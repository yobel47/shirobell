<?php

namespace Tests\Feature\Admin;

use App\Enums\InvitationStatus;
use App\Filament\Resources\Invitations\Pages\ViewInvitation;
use App\Livewire\Invitation\Editor\CoupleForm;
use App\Livewire\Invitation\Editor\EventsManager;
use App\Livewire\Invitation\Editor\SettingsForm;
use App\Livewire\Invitation\GuestManager;
use App\Models\AdminActivityLog;
use App\Models\Invitation;
use App\Models\User;
use Database\Seeders\ThemeSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminEditInvitationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $owner;

    private Invitation $invitation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ThemeSeeder::class);
        $this->admin = User::factory()->admin()->create();
        $this->owner = User::factory()->create(['name' => 'Pelanggan Satu', 'email' => 'pelanggan@example.com']);
        $this->invitation = Invitation::factory()->active()->for($this->owner)->create(['slug' => 'budi-ani']);
    }

    public function test_admin_can_open_customer_editor_pages_with_banner_and_audit(): void
    {
        $this->actingAs($this->admin);

        $this->get(route('app.invitations.edit', [$this->invitation, 'acara']))
            ->assertOk()
            ->assertSee('Mode admin.')
            ->assertSee('pelanggan@example.com')
            ->assertSee('Kembali ke panel admin')
            ->assertDontSee('Perpanjang');
        $this->get(route('app.invitations.guests', $this->invitation))->assertOk();
        $this->get(route('app.invitations.wishes', $this->invitation))->assertOk();
        $this->get(route('app.invitations.preview', $this->invitation))->assertOk();

        $pages = AdminActivityLog::where('action', 'invitation.admin_open')->get()->pluck('meta.page')->all();
        $this->assertSame(['editor:acara', 'tamu', 'rsvp', 'preview'], $pages);
        $this->assertSame($this->owner->id, AdminActivityLog::first()->meta['owner_id']);
    }

    public function test_admin_changes_are_saved_and_audited_with_fields(): void
    {
        Livewire::actingAs($this->admin)
            ->test(CoupleForm::class, ['invitation' => $this->invitation])
            ->set('form.groom_fullname', 'Budi Santoso, S.Kom.')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('Budi Santoso, S.Kom.', $this->invitation->refresh()->groom_fullname);

        $log = AdminActivityLog::where('action', 'invitation.admin_edit')->sole();
        $this->assertSame($this->admin->id, $log->admin_id);
        $this->assertSame($this->invitation->id, $log->subject_id);
        $this->assertSame('CoupleForm', $log->meta['source']);
        $this->assertSame('Invitation', $log->meta['model']);
        $this->assertContains('groom_fullname', $log->meta['fields']);
    }

    public function test_child_records_created_and_deleted_by_admin_are_audited(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(EventsManager::class, ['invitation' => $this->invitation])
            ->call('create')
            ->set('form.date', '2027-02-14')->set('form.start_time', '08:00')->set('form.venue_name', 'Masjid Raya')
            ->call('save')
            ->assertHasNoErrors();

        $event = $this->invitation->events()->sole();
        $component->call('delete', $event->id);

        $events = AdminActivityLog::where('action', 'invitation.admin_edit')->get()->map(fn ($l) => $l->meta['model'].':'.$l->meta['event'])->all();
        $this->assertSame(['InvitationEvent:created', 'InvitationEvent:deleted'], $events);
    }

    public function test_admin_can_manage_guests_and_edit_suspended_invitation(): void
    {
        $this->invitation->forceFill(['status' => InvitationStatus::Suspended, 'suspended_reason' => 'Perlu revisi'])->save();

        Livewire::actingAs($this->admin)
            ->test(GuestManager::class, ['invitation' => $this->invitation])
            ->set('bulk', "Pak Joko\nIbu Rina")
            ->call('addBulk')
            ->assertHasNoErrors();

        $this->assertSame(2, $this->invitation->guests()->count());
        $this->assertSame(2, AdminActivityLog::where('action', 'invitation.admin_edit')->count());
    }

    public function test_admin_cannot_delete_customer_invitation_from_editor(): void
    {
        $draft = Invitation::factory()->for($this->owner)->create();

        Livewire::actingAs($this->admin)
            ->test(SettingsForm::class, ['invitation' => $draft])
            ->assertDontSee('Hapus undangan')
            ->call('deleteInvitation')
            ->assertForbidden();

        $this->assertNotSoftDeleted('invitations', ['id' => $draft->id]);
    }

    public function test_admin_still_cannot_use_customer_only_pages(): void
    {
        $this->actingAs($this->admin);

        $this->get(route('app.dashboard'))->assertRedirect('/admin');
        $this->get(route('app.invitations.create'))->assertRedirect('/admin');
        $this->get(route('app.checkout', $this->invitation))->assertRedirect('/admin');
        $this->get(route('app.orders.index'))->assertRedirect('/admin');
    }

    public function test_owner_edits_are_not_audited_and_other_customers_stay_blocked(): void
    {
        Livewire::actingAs($this->owner)
            ->test(CoupleForm::class, ['invitation' => $this->invitation])
            ->set('form.groom_fullname', 'Diubah pemilik')
            ->call('save');

        $this->actingAs($this->owner)->get(route('app.invitations.edit', $this->invitation))->assertOk()->assertDontSee('Mode admin.');
        $this->assertSame(0, AdminActivityLog::count());

        $this->actingAs(User::factory()->create())->get(route('app.invitations.edit', $this->invitation))->assertForbidden();
    }

    public function test_blocked_admin_cannot_edit(): void
    {
        $this->actingAs(User::factory()->admin()->blocked()->create())
            ->get(route('app.invitations.edit', $this->invitation))
            ->assertRedirect(route('login'));
    }

    public function test_panel_has_edit_content_link(): void
    {
        Filament::setCurrentPanel('admin');

        Livewire::actingAs($this->admin)
            ->test(ViewInvitation::class, ['record' => $this->invitation->uuid])
            ->assertActionVisible('editContent')
            ->assertActionHasUrl('editContent', route('app.invitations.edit', $this->invitation));
    }
}
