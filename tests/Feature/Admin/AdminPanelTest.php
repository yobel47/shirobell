<?php

namespace Tests\Feature\Admin;

use App\Enums\InvitationStatus;
use App\Enums\OrderStatus;
use App\Filament\Pages\ManageSettings;
use App\Filament\Resources\Invitations\Pages\ViewInvitation;
use App\Filament\Resources\Orders\Pages\ViewOrder;
use App\Filament\Resources\PromoCodes\Pages\CreatePromoCode;
use App\Filament\Resources\Themes\Pages\EditTheme;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Models\AdminActivityLog;
use App\Models\Invitation;
use App\Models\Order;
use App\Models\PromoCode;
use App\Models\Theme;
use App\Models\User;
use App\Services\SettingService;
use Database\Seeders\ThemeSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ThemeSeeder::class);
        Mail::fake();
        $this->admin = User::factory()->admin()->create();
    }

    private function order(Invitation $invitation, array $attrs = []): Order
    {
        $order = new Order;
        $order->forceFill(array_merge([
            'uuid' => (string) Str::uuid(), 'number' => 'INV-TEST-'.Str::upper(Str::random(5)),
            'user_id' => $invitation->user_id, 'invitation_id' => $invitation->id, 'type' => 'new',
            'base_amount' => 99000, 'discount_amount' => 0, 'total_amount' => 99000, 'duration_days' => 90,
            'status' => OrderStatus::Pending, 'expires_at' => now()->addDay(),
        ], $attrs))->save();

        return $order;
    }

    public function test_admin_can_open_dashboard_and_every_resource(): void
    {
        $invitation = Invitation::factory()->create();
        $order = $this->order($invitation, ['status' => OrderStatus::Paid, 'paid_at' => now()]);
        $invitation->wishes()->create(['name' => 'Rina', 'message' => 'Selamat']);

        $this->actingAs($this->admin);
        foreach (['', '/orders', '/promo-codes', '/promo-codes/create', '/users', '/invitations', '/wishes', '/themes', '/music-tracks', '/admin-activity-logs', '/pengaturan',
            '/invitations/'.$invitation->uuid, '/orders/'.$order->uuid, '/users/'.$invitation->user_id] as $path) {
            $this->assertSame(200, $this->get('/admin'.$path)->status(), 'Failed: /admin'.$path);
        }
    }

    public function test_customer_and_blocked_admin_cannot_access_panel(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
        $this->actingAs(User::factory()->admin()->blocked()->create())->get('/admin')->assertForbidden();
    }

    public function test_admin_visiting_customer_area_is_sent_to_panel(): void
    {
        $this->actingAs($this->admin)->get('/app')->assertRedirect('/admin');
    }

    public function test_admin_panel_has_security_headers(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin');

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $this->assertStringContainsString("object-src 'none'", $response->headers->get('Content-Security-Policy'));
    }

    public function test_suspend_and_unsuspend_are_audited(): void
    {
        Filament::setCurrentPanel('admin');
        $invitation = Invitation::factory()->active()->create();

        Livewire::actingAs($this->admin)
            ->test(ViewInvitation::class, ['record' => $invitation->uuid])
            ->callAction('suspend', ['reason' => 'Konten tidak pantas'])
            ->assertHasNoActionErrors();

        $invitation->refresh();
        $this->assertSame(InvitationStatus::Suspended, $invitation->status);
        $this->assertSame('Konten tidak pantas', $invitation->suspended_reason);
        $this->get('http://'.$invitation->slug.'.'.config('app.domain').'/')->assertForbidden();

        Livewire::actingAs($this->admin)
            ->test(ViewInvitation::class, ['record' => $invitation->uuid])
            ->callAction('unsuspend');

        $this->assertSame(InvitationStatus::Active, $invitation->refresh()->status);
        $this->assertSame(['invitation.suspend', 'invitation.unsuspend'], AdminActivityLog::orderBy('id')->pluck('action')->all());
        $this->assertSame($this->admin->id, AdminActivityLog::first()->admin_id);
    }

    public function test_manual_extend_activates_draft(): void
    {
        Filament::setCurrentPanel('admin');
        $invitation = Invitation::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(ViewInvitation::class, ['record' => $invitation->uuid])
            ->callAction('extend', ['days' => 30, 'reason' => 'Kompensasi'])
            ->assertHasNoActionErrors();

        $invitation->refresh();
        $this->assertSame(InvitationStatus::Active, $invitation->status);
        $this->assertTrue($invitation->active_until->between(now()->addDays(29), now()->addDays(31)));
        $this->assertDatabaseHas('admin_activity_logs', ['action' => 'invitation.extend', 'subject_id' => $invitation->id]);
    }

    public function test_admin_can_change_published_slug_with_validation(): void
    {
        Filament::setCurrentPanel('admin');
        $invitation = Invitation::factory()->active()->create(['slug' => 'lama-sekali']);
        Invitation::factory()->create(['slug' => 'sudah-dipakai']);

        foreach (['sudah-dipakai', 'www', 'Ada Spasi'] as $invalid) {
            Livewire::actingAs($this->admin)
                ->test(ViewInvitation::class, ['record' => $invitation->uuid])
                ->callAction('changeSlug', ['slug' => $invalid])
                ->assertHasActionErrors(['slug']);
        }

        Livewire::actingAs($this->admin)
            ->test(ViewInvitation::class, ['record' => $invitation->uuid])
            ->callAction('changeSlug', ['slug' => 'baru-sekali'])
            ->assertHasNoActionErrors();

        $this->assertSame('baru-sekali', $invitation->refresh()->slug);
    }

    public function test_mark_order_paid_manually_activates_and_audits(): void
    {
        Filament::setCurrentPanel('admin');
        $invitation = Invitation::factory()->create();
        $order = $this->order($invitation);

        Livewire::actingAs($this->admin)
            ->test(ViewOrder::class, ['record' => $order->uuid])
            ->callAction('markPaid', ['reason' => 'Dana masuk, notifikasi gagal'])
            ->assertHasNoActionErrors();

        $this->assertSame(OrderStatus::Paid, $order->refresh()->status);
        $this->assertSame('manual', $order->payment_method);
        $this->assertSame(InvitationStatus::Active, $invitation->refresh()->status);
        $this->assertDatabaseHas('admin_activity_logs', ['action' => 'order.mark_paid', 'subject_id' => $order->id]);
    }

    public function test_block_user_but_not_self(): void
    {
        Filament::setCurrentPanel('admin');
        $customer = User::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(ViewUser::class, ['record' => $customer->id])
            ->callAction('block');

        $this->assertTrue($customer->refresh()->is_blocked);

        Livewire::actingAs($this->admin)
            ->test(ViewUser::class, ['record' => $this->admin->id])
            ->assertActionHidden('block');
    }

    public function test_promo_code_can_be_created_uppercased(): void
    {
        Filament::setCurrentPanel('admin');

        Livewire::actingAs($this->admin)
            ->test(CreatePromoCode::class)
            ->fillForm(['code' => 'hemat10', 'type' => 'percent', 'value' => 10, 'is_active' => true])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertSame('HEMAT10', PromoCode::first()->code);

        Livewire::actingAs($this->admin)
            ->test(CreatePromoCode::class)
            ->fillForm(['code' => 'LEBIH', 'type' => 'percent', 'value' => 150])
            ->call('create')
            ->assertHasFormErrors(['value']);
    }

    public function test_theme_without_view_cannot_be_activated(): void
    {
        Filament::setCurrentPanel('admin');
        $theme = Theme::factory()->create(['slug' => 'tanpa-view', 'name' => 'Tanpa View', 'is_active' => false]);

        Livewire::actingAs($this->admin)
            ->test(EditTheme::class, ['record' => $theme->id])
            ->fillForm(['is_active' => true])
            ->call('save')
            ->assertHasFormErrors(['is_active']);

        $this->assertFalse($theme->refresh()->is_active);
    }

    public function test_settings_update_price_and_audit(): void
    {
        Filament::setCurrentPanel('admin');

        Livewire::actingAs($this->admin)
            ->test(ManageSettings::class)
            ->fillForm(['package_price' => 149000])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(149000, app(SettingService::class)->packagePrice());
        $log = AdminActivityLog::firstWhere('action', 'settings.update');
        $this->assertEquals(149000, $log->meta['package_price']['to']);
        $this->get('/')->assertSee('Rp 149.000');
    }
}
