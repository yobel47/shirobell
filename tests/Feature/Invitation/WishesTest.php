<?php

namespace Tests\Feature\Invitation;

use App\Enums\Attendance;
use App\Livewire\Invitation\PublicWishes;
use App\Livewire\Invitation\WishesRecap;
use App\Models\Invitation;
use App\Models\User;
use App\Services\GuestService;
use Database\Seeders\ThemeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WishesTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private Invitation $invitation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ThemeSeeder::class);

        $this->owner = User::factory()->create();
        $this->invitation = Invitation::factory()->active()->for($this->owner)->create(['slug' => 'budi-ani', 'rsvp_max_pax' => 3]);
    }

    public function test_guest_can_rsvp_and_send_wish(): void
    {
        Livewire::test(PublicWishes::class, ['invitation' => $this->invitation])
            ->set('name', 'Pak Joko')
            ->set('attendance', 'yes')
            ->set('pax', 2)
            ->set('message', 'Selamat menempuh hidup baru')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('submitted', true);

        $wish = $this->invitation->wishes()->first();
        $this->assertSame(Attendance::Yes, $wish->attendance);
        $this->assertSame(2, $wish->pax);
        $this->assertSame(64, strlen($wish->ip_hash));
        $this->assertNotSame('127.0.0.1', $wish->ip_hash);
    }

    public function test_pax_above_max_is_rejected(): void
    {
        Livewire::test(PublicWishes::class, ['invitation' => $this->invitation])
            ->set('name', 'Pak Joko')
            ->set('attendance', 'yes')
            ->set('pax', 9)
            ->call('submit')
            ->assertHasErrors('pax');
    }

    public function test_empty_submission_requires_attendance_or_message(): void
    {
        Livewire::test(PublicWishes::class, ['invitation' => $this->invitation])
            ->set('name', 'Pak Joko')
            ->call('submit')
            ->assertHasErrors('message');

        $this->assertSame(0, $this->invitation->wishes()->count());
    }

    public function test_honeypot_silently_drops_bots(): void
    {
        Livewire::test(PublicWishes::class, ['invitation' => $this->invitation])
            ->set('name', 'Bot')
            ->set('message', 'spam')
            ->set('website', 'http://spam.example')
            ->call('submit')
            ->assertSet('submitted', true);

        $this->assertSame(0, $this->invitation->wishes()->count());
    }

    public function test_submissions_are_rate_limited(): void
    {
        config(['shirobell.invitation.wish_rate_limit' => 2]);

        foreach ([1, 2] as $i) {
            Livewire::test(PublicWishes::class, ['invitation' => $this->invitation])
                ->set('name', "Tamu {$i}")->set('message', 'Selamat')->call('submit')->assertHasNoErrors();
        }

        Livewire::test(PublicWishes::class, ['invitation' => $this->invitation])
            ->set('name', 'Tamu 3')->set('message', 'Selamat')->call('submit')
            ->assertHasErrors('form');

        $this->assertSame(2, $this->invitation->wishes()->count());
    }

    public function test_draft_invitation_rejects_submissions(): void
    {
        $draft = Invitation::factory()->create(['slug' => 'masih-draft']);

        Livewire::test(PublicWishes::class, ['invitation' => $draft, 'preview' => true])
            ->set('name', 'Pemilik')->set('message', 'Tes')->call('submit')
            ->assertHasErrors('form');

        $this->assertSame(0, $draft->wishes()->count());
    }

    public function test_personal_link_guest_updates_single_rsvp(): void
    {
        $guest = app(GuestService::class)->create($this->invitation, ['name' => 'Pak Joko']);

        Livewire::test(PublicWishes::class, ['invitation' => $this->invitation, 'guestId' => $guest->id])
            ->assertSet('name', 'Pak Joko')
            ->set('attendance', 'maybe')->set('message', 'Insya Allah')->call('submit');

        Livewire::test(PublicWishes::class, ['invitation' => $this->invitation, 'guestId' => $guest->id])
            ->assertSet('attendance', 'maybe')
            ->set('attendance', 'yes')->set('pax', 1)->call('submit');

        $this->assertSame(1, $this->invitation->wishes()->count());
        $wish = $this->invitation->wishes()->first();
        $this->assertSame(Attendance::Yes, $wish->attendance);
        $this->assertSame('Insya Allah', $wish->message);
        $this->assertSame($guest->id, $wish->guest_id);
    }

    public function test_opening_personal_link_marks_guest_opened(): void
    {
        $guest = app(GuestService::class)->create($this->invitation, ['name' => 'Pak Joko']);

        $this->get('http://budi-ani.'.config('app.domain').'/?to=Nama+Palsu&g='.$guest->code)
            ->assertOk()
            ->assertSee('Pak Joko')
            ->assertDontSee('Nama Palsu');

        $this->assertNotNull($guest->refresh()->opened_at);
    }

    public function test_hidden_wishes_are_not_shown_publicly_and_messages_are_escaped(): void
    {
        $visible = $this->invitation->wishes()->create(['name' => 'Rina', 'message' => '<b>Bahagia</b> selalu']);
        $hidden = $this->invitation->wishes()->create(['name' => 'Spammer', 'message' => 'Iklan judi']);
        $hidden->forceFill(['is_hidden' => true])->save();

        Livewire::test(PublicWishes::class, ['invitation' => $this->invitation])
            ->assertSee('&lt;b&gt;Bahagia&lt;/b&gt; selalu', false)
            ->assertDontSee('Iklan judi');
    }

    public function test_owner_can_hide_wish_and_see_stats(): void
    {
        $wish = $this->invitation->wishes()->create(['name' => 'Rina', 'attendance' => 'yes', 'pax' => 2, 'message' => 'Selamat']);
        $this->invitation->wishes()->create(['name' => 'Andi', 'attendance' => 'no']);

        Livewire::actingAs($this->owner)
            ->test(WishesRecap::class, ['invitation' => $this->invitation])
            ->assertViewHas('stats', fn ($s) => $s['yes'] === 1 && $s['yes_pax'] === 2 && $s['no'] === 1 && $s['messages'] === 1)
            ->call('toggleHidden', $wish->id);

        $this->assertTrue($wish->refresh()->is_hidden);
    }

    public function test_recap_and_export_are_owner_only(): void
    {
        $intruder = User::factory()->create();

        $this->actingAs($intruder)->get(route('app.invitations.wishes', $this->invitation))->assertForbidden();
        $this->actingAs($intruder)->get(route('app.invitations.wishes.export', $this->invitation))->assertForbidden();
    }

    public function test_csv_export_neutralises_formulas(): void
    {
        $this->invitation->wishes()->create(['name' => '=HYPERLINK("http://evil")', 'message' => 'Selamat']);

        $response = $this->actingAs($this->owner)->get(route('app.invitations.wishes.export', $this->invitation));

        $response->assertOk();
        $csv = $response->streamedContent();
        $this->assertStringContainsString("'=HYPERLINK", $csv);
        $this->assertStringContainsString('Selamat', $csv);
    }
}
