<?php

namespace Tests\Feature\EventDay;

use App\Enums\OrderStatus;
use App\Livewire\Invitation\EventDayManager;
use App\Livewire\Payment\Checkout;
use App\Livewire\Reception\CheckinApp;
use App\Livewire\Reception\WelcomeScreen;
use App\Models\Guest;
use App\Models\Invitation;
use App\Models\Order;
use App\Models\User;
use App\Services\EventDayService;
use App\Services\GuestService;
use App\Services\Payment\PaymentService;
use App\Services\ReceptionAccess;
use App\Services\SettingService;
use Database\Seeders\ThemeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class EventDayTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private Invitation $invitation;

    private Guest $guest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ThemeSeeder::class);
        $this->owner = User::factory()->create();
        $this->invitation = Invitation::factory()->active()->for($this->owner)->create(['slug' => 'budi-ani']);
        $this->guest = app(GuestService::class)->create($this->invitation, ['name' => 'Pak Joko', 'group_name' => 'Keluarga']);
    }

    private function enable(): void
    {
        app(EventDayService::class)->enable($this->invitation);
        $this->invitation->refresh();
    }

    private function loginReception(): void
    {
        $this->post(route('reception.login', $this->invitation->checkin_token), ['pin' => $this->invitation->checkin_pin])
            ->assertRedirect(route('reception.show', $this->invitation->checkin_token));
    }

    public function test_owner_enables_receptionist_link_and_sees_pin(): void
    {
        Livewire::actingAs($this->owner)
            ->test(EventDayManager::class, ['invitation' => $this->invitation])
            ->assertSee('Aktifkan penerima tamu')
            ->call('enable')
            ->assertSee('Akses petugas penerima tamu');

        $this->invitation->refresh();
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9]{40}$/', $this->invitation->checkin_token);
        $this->assertMatchesRegularExpression('/^\d{6}$/', (string) $this->invitation->checkin_pin);
        // Stored encrypted, never in plain text.
        $this->assertNotSame($this->invitation->checkin_pin, $this->invitation->getRawOriginal('checkin_pin'));
        $this->assertArrayNotHasKey('checkin_pin', $this->invitation->toArray());

        $this->actingAs($this->owner)->get(route('app.invitations.event-day', $this->invitation))->assertOk()->assertSee((string) $this->invitation->checkin_pin);
    }

    public function test_draft_invitation_cannot_enable(): void
    {
        $draft = Invitation::factory()->for($this->owner)->create();

        Livewire::actingAs($this->owner)
            ->test(EventDayManager::class, ['invitation' => $draft])
            ->assertSee('tersedia setelah undangan aktif');

        $this->expectException(ValidationException::class);
        app(EventDayService::class)->enable($draft);
    }

    public function test_receptionist_needs_correct_pin_and_is_rate_limited(): void
    {
        $this->enable();
        $token = $this->invitation->checkin_token;

        $this->get(route('reception.show', $token))->assertOk()->assertSee('Masukkan PIN');
        $this->get(route('reception.screen', $token))->assertRedirect(route('reception.show', $token));

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('reception.login', $token), ['pin' => '000000'])->assertSessionHasErrors('pin');
        }
        $this->post(route('reception.login', $token), ['pin' => $this->invitation->checkin_pin])
            ->assertSessionHasErrors(['pin' => 'Terlalu banyak percobaan. Coba lagi dalam 60 detik.']);
    }

    public function test_unknown_or_disabled_links_are_not_found(): void
    {
        $this->get(route('reception.show', str_repeat('a', 40)))->assertNotFound();

        $this->enable();
        $token = $this->invitation->checkin_token;
        app(EventDayService::class)->disable($this->invitation);
        $this->get(route('reception.show', $token))->assertNotFound();
    }

    public function test_reception_page_allows_camera_and_loads_scanner(): void
    {
        $this->enable();
        $this->loginReception();

        $response = $this->get(route('reception.show', $this->invitation->checkin_token))->assertOk()->assertSee('Pindai QR');
        $this->assertStringContainsString('camera=(self)', $response->headers->get('Permissions-Policy'));
        $this->assertStringContainsString('camera=()', $this->get('/')->headers->get('Permissions-Policy'));
    }

    public function test_scan_checks_in_once_and_reports_repeat_and_unknown_codes(): void
    {
        $this->enable();
        app(ReceptionAccess::class)->grant($this->invitation);

        $app = Livewire::test(CheckinApp::class, ['invitation' => $this->invitation]);

        $app->call('scan', $this->guest->code)->assertSet('result.status', 'ok')->assertSee('Pak Joko');
        $first = $this->guest->refresh()->checked_in_at;
        $this->assertNotNull($first);

        $this->travel(5)->minutes();
        $app->call('scan', strtoupper($this->guest->code))->assertSet('result.status', 'already');
        $this->assertEquals($first, $this->guest->refresh()->checked_in_at);

        $app->call('scan', 'tidakada1')->assertSet('result.status', 'unknown');

        // A personal link QR works too.
        $other = app(GuestService::class)->create($this->invitation, ['name' => 'Bu Rina']);
        $app->call('scan', app(GuestService::class)->personalUrl($this->invitation, $other))->assertSet('result.name', 'Bu Rina');
    }

    public function test_scan_uses_rsvp_pax_and_receptionist_can_adjust_or_undo(): void
    {
        $this->enable();
        app(ReceptionAccess::class)->grant($this->invitation);
        $this->invitation->wishes()->make(['name' => 'Pak Joko', 'attendance' => 'yes', 'pax' => 3])->forceFill(['guest_id' => $this->guest->id])->save();

        $app = Livewire::test(CheckinApp::class, ['invitation' => $this->invitation])
            ->call('scan', $this->guest->code)
            ->assertSet('result.pax', 3)
            ->call('setPax', $this->guest->id, 4)
            ->assertSet('result.pax', 4);
        $this->assertSame(4, $this->guest->refresh()->checked_in_pax);

        $app->call('undo', $this->guest->id)->assertSet('result', null);
        $this->assertNull($this->guest->refresh()->checked_in_at);
    }

    public function test_search_and_walk_in(): void
    {
        $this->enable();
        app(ReceptionAccess::class)->grant($this->invitation);

        Livewire::test(CheckinApp::class, ['invitation' => $this->invitation])
            ->set('mode', 'search')
            ->set('search', 'jok')
            ->assertSee('Pak Joko')
            ->call('checkIn', $this->guest->id, 2)
            ->assertSet('result.pax', 2)
            ->set('mode', 'walkin')
            ->set('walkInName', 'Tamu Teman Kantor')
            ->set('walkInPax', 2)
            ->call('addWalkIn')
            ->assertHasNoErrors()
            ->assertSet('result.walk_in', true);

        $this->assertSame(['guests' => 2, 'checked_in' => 2, 'pax' => 4, 'walk_in' => 1], app(EventDayService::class)->stats($this->invitation));
    }

    public function test_reception_actions_require_session_and_stay_inside_the_invitation(): void
    {
        $this->enable();

        Livewire::test(CheckinApp::class, ['invitation' => $this->invitation])->assertForbidden();

        app(ReceptionAccess::class)->grant($this->invitation);
        $foreign = app(GuestService::class)->create(Invitation::factory()->active()->create(), ['name' => 'Orang Lain']);

        Livewire::test(CheckinApp::class, ['invitation' => $this->invitation])
            ->call('checkIn', $foreign->id, 1)
            ->assertNotFound();
        $this->assertNull($foreign->refresh()->checked_in_at);
    }

    public function test_resetting_access_logs_out_existing_devices(): void
    {
        $this->enable();
        $this->loginReception();
        $oldToken = $this->invitation->checkin_token;

        app(EventDayService::class)->resetAccess($this->invitation);
        $this->invitation->refresh();

        $this->get(route('reception.show', $oldToken))->assertNotFound();
        $this->get(route('reception.show', $this->invitation->checkin_token))->assertSee('Masukkan PIN');
    }

    public function test_welcome_screen_greets_latest_arrival(): void
    {
        $this->enable();
        app(ReceptionAccess::class)->grant($this->invitation);

        Livewire::test(WelcomeScreen::class, ['invitation' => $this->invitation])->assertSee('Silakan check-in');

        app(EventDayService::class)->checkIn($this->invitation, $this->guest, 2);
        Livewire::test(WelcomeScreen::class, ['invitation' => $this->invitation])
            ->assertSee('Terima kasih telah hadir')
            ->assertSee('Pak Joko');

        $this->travel(WelcomeScreen::GREETING_SECONDS + 1)->seconds();
        Livewire::test(WelcomeScreen::class, ['invitation' => $this->invitation])
            ->assertDontSee('Terima kasih telah hadir')
            ->assertSee('Baru tiba');
    }

    public function test_guest_personal_link_shows_ticket_only_when_open(): void
    {
        $url = 'http://budi-ani.'.config('app.domain').'/?g='.$this->guest->code;

        $this->get($url)->assertOk()->assertDontSee('Tiket Masuk');

        $this->enable();
        $this->get($url)->assertOk()->assertSee('Tiket Masuk')->assertSee('<svg', false)->assertSee(strtoupper($this->guest->code));
        $this->get('http://budi-ani.'.config('app.domain').'/')->assertDontSee('Tiket Masuk');
    }

    public function test_owner_prints_qr_cards(): void
    {
        $this->actingAs($this->owner)->get(route('app.invitations.event-day.cards', $this->invitation))->assertNotFound();

        $this->enable();
        $this->actingAs($this->owner)
            ->get(route('app.invitations.event-day.cards', $this->invitation))
            ->assertOk()
            ->assertSee('Pak Joko')
            ->assertSee('<svg', false);

        $this->actingAs(User::factory()->create())->get(route('app.invitations.event-day.cards', $this->invitation))->assertForbidden();
    }

    public function test_addon_mode_requires_payment_before_use(): void
    {
        app(SettingService::class)->set('event_day_mode', 'addon');
        app(SettingService::class)->set('event_day_price', 75000);

        Livewire::actingAs($this->owner)
            ->test(EventDayManager::class, ['invitation' => $this->invitation])
            ->assertSee('Rp 75.000')
            ->assertSee('Beli add-on');
        $this->assertFalse(app(EventDayService::class)->isAvailable($this->invitation));

        $this->actingAs($this->owner)->get(route('app.checkout', [$this->invitation, 'produk' => 'hari-h']))->assertOk()->assertSee('Add-on Hari-H');

        Livewire::actingAs($this->owner)
            ->test(Checkout::class, ['invitation' => $this->invitation, 'product' => 'event_day'])
            ->assertSee('Rp 75.000');

        $order = new Order;
        $order->forceFill([
            'uuid' => (string) Str::uuid(), 'number' => 'INV-TEST-EVENT', 'user_id' => $this->owner->id,
            'invitation_id' => $this->invitation->id, 'type' => 'event_day', 'base_amount' => 75000, 'discount_amount' => 0,
            'total_amount' => 75000, 'duration_days' => 0, 'status' => OrderStatus::Pending, 'expires_at' => now()->addDay(),
        ])->save();
        $activeUntil = $this->invitation->active_until;

        app(PaymentService::class)->markPaid($order, ['payment_type' => 'qris']);

        $this->invitation->refresh();
        $this->assertNotNull($this->invitation->event_day_unlocked_at);
        $this->assertEquals($activeUntil, $this->invitation->active_until);
        $this->assertTrue(app(EventDayService::class)->isAvailable($this->invitation));

        // Already bought: the add-on checkout sends the owner back to the Hari-H page.
        $this->actingAs($this->owner)->get(route('app.checkout', [$this->invitation, 'produk' => 'hari-h']))
            ->assertRedirect(route('app.invitations.event-day', $this->invitation));
    }

    public function test_included_mode_needs_no_payment(): void
    {
        $this->assertTrue(app(EventDayService::class)->isAvailable($this->invitation));
        $this->actingAs($this->owner)->get(route('app.checkout', [$this->invitation, 'produk' => 'hari-h']))
            ->assertRedirect(route('app.invitations.event-day', $this->invitation));
    }

    public function test_admin_can_open_event_day_page_with_audit(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('app.invitations.event-day', $this->invitation))->assertOk()->assertSee('Mode admin.');
        $this->assertDatabaseHas('admin_activity_logs', ['action' => 'invitation.admin_open']);
    }
}
