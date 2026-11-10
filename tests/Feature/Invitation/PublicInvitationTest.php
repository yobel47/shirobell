<?php

namespace Tests\Feature\Invitation;

use App\Models\Invitation;
use App\Models\User;
use Database\Seeders\ThemeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicInvitationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ThemeSeeder::class);
    }

    private function subdomain(string $slug, string $query = ''): string
    {
        return 'http://'.$slug.'.'.config('app.domain').'/'.$query;
    }

    public function test_active_invitation_renders_theme_without_watermark(): void
    {
        $invitation = Invitation::factory()->active()->create(['slug' => 'budi-ani']);
        $invitation->events()->create([
            'name' => 'Resepsi', 'date' => now()->addMonth()->toDateString(), 'start_time' => '10:00',
            'timezone' => 'WIB', 'venue_name' => 'Gedung Melati',
        ]);

        $this->get($this->subdomain('budi-ani'))
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
            ->assertSee('Budi')
            ->assertSee('Gedung Melati')
            ->assertSee('Buka Undangan')
            ->assertDontSee('Mode preview');
    }

    public function test_guest_name_is_shown_and_escaped(): void
    {
        Invitation::factory()->active()->create(['slug' => 'budi-ani']);

        $this->get($this->subdomain('budi-ani', '?to='.urlencode('Pak Joko <script>alert(1)</script>')))
            ->assertOk()
            ->assertSee('Pak Joko')
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_draft_invitation_is_not_public(): void
    {
        Invitation::factory()->create(['slug' => 'masih-draft']);

        $this->get($this->subdomain('masih-draft'))
            ->assertNotFound()
            ->assertSee('Undangan belum diterbitkan');
    }

    public function test_expired_invitation_shows_expired_page(): void
    {
        Invitation::factory()->expired()->create(['slug' => 'sudah-lewat']);

        $this->get($this->subdomain('sudah-lewat'))
            ->assertStatus(410)
            ->assertSee('Masa aktif undangan telah berakhir');
    }

    public function test_active_status_past_active_until_is_treated_as_expired(): void
    {
        Invitation::factory()->active()->create(['slug' => 'lupa-expire', 'active_until' => now()->subMinute()]);

        $this->get($this->subdomain('lupa-expire'))->assertStatus(410);
    }

    public function test_unknown_slug_returns_404_page(): void
    {
        $this->get($this->subdomain('tidak-ada'))
            ->assertNotFound()
            ->assertSee('Undangan tidak ditemukan');
    }

    public function test_reserved_subdomain_redirects_to_main_site(): void
    {
        $this->get($this->subdomain('www'))->assertRedirect(config('app.url'));
    }

    public function test_owner_preview_of_draft_has_watermark(): void
    {
        $owner = User::factory()->create();
        $invitation = Invitation::factory()->for($owner)->create(['slug' => 'budi-ani']);

        $this->actingAs($owner)
            ->get(route('app.invitations.preview', $invitation))
            ->assertOk()
            ->assertSee('Mode preview')
            ->assertSee('PREVIEW');
    }

    public function test_owner_preview_of_active_invitation_has_no_watermark(): void
    {
        $owner = User::factory()->create();
        $invitation = Invitation::factory()->active()->for($owner)->create(['slug' => 'budi-ani']);

        $this->actingAs($owner)
            ->get(route('app.invitations.preview', $invitation))
            ->assertOk()
            ->assertDontSee('Mode preview');
    }

    public function test_disabled_gallery_section_is_hidden(): void
    {
        $invitation = Invitation::factory()->active()->create(['slug' => 'budi-ani']);
        $invitation->photos()->create(['path' => 'invitations/x/foto.webp']);
        $invitation->forceFill(['sections' => ['gallery' => false]])->save();

        $this->get($this->subdomain('budi-ani'))
            ->assertOk()
            ->assertDontSee('foto.webp');
    }
}
