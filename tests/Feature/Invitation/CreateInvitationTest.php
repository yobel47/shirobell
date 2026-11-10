<?php

namespace Tests\Feature\Invitation;

use App\Enums\InvitationStatus;
use App\Livewire\Invitation\CreateWizard;
use App\Models\Invitation;
use App\Models\Theme;
use App\Models\User;
use Database\Seeders\ThemeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CreateInvitationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ThemeSeeder::class);
    }

    public function test_guest_is_redirected_from_create_page(): void
    {
        $this->get(route('app.invitations.create'))->assertRedirect(route('login'));
    }

    public function test_customer_sees_create_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('app.invitations.create'))
            ->assertOk()
            ->assertSee('Siapa yang menikah?');
    }

    public function test_wizard_suggests_slug_from_nicknames(): void
    {
        Livewire::actingAs(User::factory()->create())
            ->test(CreateWizard::class)
            ->set('groom_nickname', 'Budi')
            ->set('bride_nickname', 'Ani Lestari')
            ->assertSet('slug', 'budi-ani-lestari');
    }

    public function test_wizard_creates_draft_invitation_with_defaults(): void
    {
        $user = User::factory()->create();
        $theme = Theme::firstWhere('slug', 'rose-garden');

        $component = Livewire::actingAs($user)
            ->test(CreateWizard::class)
            ->set('groom_nickname', 'Budi')
            ->set('bride_nickname', 'Ani')
            ->set('slug', 'budi-ani')
            ->call('next')
            ->assertSet('step', 2)
            ->set('theme_id', $theme->id)
            ->call('create')
            ->assertHasNoErrors();

        $invitation = Invitation::firstWhere('slug', 'budi-ani');
        $component->assertRedirect(route('app.invitations.edit', $invitation));

        $this->assertSame($user->id, $invitation->user_id);
        $this->assertSame(InvitationStatus::Draft, $invitation->status);
        $this->assertNull($invitation->active_until);
        $this->assertTrue($invitation->sectionEnabled('gallery'));
        $this->assertNotEmpty($invitation->opening_text);
    }

    public function test_reserved_invalid_and_taken_slugs_are_rejected(): void
    {
        Invitation::factory()->create(['slug' => 'sudah-ada']);
        $user = User::factory()->create();

        foreach (['www', 'admin', 'ab', '-budi', 'budi--ani', 'Budi Ani', 'sudah-ada'] as $slug) {
            Livewire::actingAs($user)
                ->test(CreateWizard::class)
                ->set('groom_nickname', 'Budi')
                ->set('bride_nickname', 'Ani')
                ->set('slug', $slug)
                ->call('next')
                ->assertHasErrors('slug')
                ->assertSet('step', 1);
        }
    }

    public function test_inactive_theme_cannot_be_chosen(): void
    {
        $inactive = Theme::factory()->create(['slug' => 'belum-siap', 'name' => 'Belum Siap', 'is_active' => false]);

        Livewire::actingAs(User::factory()->create())
            ->test(CreateWizard::class)
            ->set('groom_nickname', 'Budi')
            ->set('bride_nickname', 'Ani')
            ->set('slug', 'budi-ani')
            ->set('theme_id', $inactive->id)
            ->call('create')
            ->assertHasErrors('theme_id');

        $this->assertDatabaseMissing('invitations', ['slug' => 'budi-ani']);
    }

    public function test_draft_limit_is_enforced(): void
    {
        config(['shirobell.invitation.max_drafts_per_user' => 2]);
        $user = User::factory()->create();
        Invitation::factory()->count(2)->for($user)->create();

        Livewire::actingAs($user)
            ->test(CreateWizard::class)
            ->set('groom_nickname', 'Budi')
            ->set('bride_nickname', 'Ani')
            ->set('slug', 'budi-ani-baru')
            ->set('theme_id', Theme::firstWhere('slug', 'rose-garden')->id)
            ->call('create')
            ->assertHasErrors('slug');

        $this->assertSame(2, $user->invitations()->count());
    }

    public function test_dashboard_lists_only_own_invitations(): void
    {
        $user = User::factory()->create();
        Invitation::factory()->for($user)->create(['slug' => 'punya-saya', 'groom_nickname' => 'Rama']);
        Invitation::factory()->create(['slug' => 'punya-orang', 'groom_nickname' => 'Joko']);

        $this->actingAs($user)
            ->get(route('app.dashboard'))
            ->assertOk()
            ->assertSee('punya-saya')
            ->assertDontSee('punya-orang');
    }
}
