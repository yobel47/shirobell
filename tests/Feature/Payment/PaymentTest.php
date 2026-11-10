<?php

namespace Tests\Feature\Payment;

use App\Enums\InvitationStatus;
use App\Enums\OrderStatus;
use App\Livewire\Payment\Checkout;
use App\Livewire\Payment\OrderStatus as OrderStatusPage;
use App\Mail\InvitationExpired;
use App\Mail\InvitationExpiringSoon;
use App\Mail\PaymentSucceeded;
use App\Models\Invitation;
use App\Models\Order;
use App\Models\PromoCode;
use App\Models\User;
use App\Services\SettingService;
use Database\Seeders\ThemeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    private const KEY = 'SB-Mid-server-test-key';

    private User $owner;

    private Invitation $invitation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ThemeSeeder::class);
        config(['services.midtrans.server_key' => self::KEY, 'services.midtrans.is_production' => false]);
        Mail::fake();

        $this->owner = User::factory()->create();
        $this->invitation = Invitation::factory()->for($this->owner)->create(['slug' => 'budi-ani']);
    }

    private function fakeMidtrans(string $transactionStatus = 'settlement', ?int $gross = null, string $fraud = 'accept'): void
    {
        Http::fake([
            'app.sandbox.midtrans.com/snap/v1/transactions' => Http::response(['token' => 'snap-token', 'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/snap-token'], 201),
            'api.sandbox.midtrans.com/v2/*' => function ($request) use ($transactionStatus, $gross, $fraud) {
                $orderId = explode('/', parse_url($request->url(), PHP_URL_PATH))[2];
                $order = Order::firstWhere('uuid', $orderId);

                return Http::response([
                    'status_code' => '200',
                    'order_id' => $orderId,
                    'transaction_id' => 'trx-123',
                    'transaction_status' => $transactionStatus,
                    'fraud_status' => $fraud,
                    'payment_type' => 'bank_transfer',
                    'va_numbers' => [['bank' => 'bca', 'va_number' => '123']],
                    'gross_amount' => number_format($gross ?? $order?->total_amount ?? 0, 2, '.', ''),
                ]);
            },
        ]);
    }

    private function notification(Order $order, string $status = 'settlement', ?string $signature = null): array
    {
        $gross = number_format($order->total_amount, 2, '.', '');
        $statusCode = $status === 'settlement' ? '200' : '201';

        return [
            'order_id' => $order->uuid,
            'status_code' => $statusCode,
            'gross_amount' => $gross,
            'transaction_status' => $status,
            'signature_key' => $signature ?? hash('sha512', $order->uuid.$statusCode.$gross.self::KEY),
        ];
    }

    private function checkout(?string $promo = null): Order
    {
        $component = Livewire::actingAs($this->owner)->test(Checkout::class, ['invitation' => $this->invitation]);
        if ($promo) {
            $component->set('promoInput', $promo)->call('applyPromo')->assertHasNoErrors();
        }
        $component->call('pay')->assertRedirect('https://app.sandbox.midtrans.com/snap/v4/redirection/snap-token');

        return Order::latest('id')->first();
    }

    public function test_checkout_creates_pending_order_and_redirects_to_midtrans(): void
    {
        $this->fakeMidtrans();
        app(SettingService::class)->set('package_price', 150000);

        $order = $this->checkout();

        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertSame('new', $order->type);
        $this->assertSame(150000, $order->total_amount);
        $this->assertSame(90, $order->duration_days);
        $this->assertSame('snap-token', $order->snap_token);

        Http::assertSent(fn ($r) => str_contains($r->url(), '/snap/v1/transactions')
            && $r['transaction_details']['order_id'] === $order->uuid
            && $r['transaction_details']['gross_amount'] === 150000
            && $r->hasHeader('Authorization', 'Basic '.base64_encode(self::KEY.':')));
    }

    public function test_paying_again_reuses_open_order(): void
    {
        $this->fakeMidtrans();
        $this->checkout();
        $this->checkout();

        $this->assertSame(1, Order::count());
    }

    public function test_percent_and_fixed_promos_are_applied(): void
    {
        $this->fakeMidtrans();
        PromoCode::factory()->create(['code' => 'DISKON20', 'type' => 'percent', 'value' => 20]);

        $order = $this->checkout('diskon20');
        $this->assertSame(19800, $order->discount_amount);
        $this->assertSame(79200, $order->total_amount);

        PromoCode::factory()->create(['code' => 'POTONG50', 'type' => 'fixed', 'value' => 50000]);
        $this->invitation = Invitation::factory()->for($this->owner)->create();
        $this->assertSame(49000, $this->checkout('POTONG50')->total_amount);
    }

    public function test_invalid_promos_are_rejected(): void
    {
        PromoCode::factory()->create(['code' => 'LEWAT', 'ends_at' => now()->subDay()]);
        PromoCode::factory()->create(['code' => 'HABIS', 'max_uses' => 1, 'used_count' => 1]);
        PromoCode::factory()->create(['code' => 'NANTI', 'starts_at' => now()->addDay()]);
        PromoCode::factory()->create(['code' => 'MATI', 'is_active' => false]);

        foreach (['LEWAT', 'HABIS', 'NANTI', 'MATI', 'TIDAKADA'] as $code) {
            Livewire::actingAs($this->owner)
                ->test(Checkout::class, ['invitation' => $this->invitation])
                ->set('promoInput', $code)
                ->call('applyPromo')
                ->assertHasErrors('promoInput')
                ->assertSet('appliedPromo', null);
        }
    }

    public function test_full_discount_activates_without_midtrans(): void
    {
        Http::fake();
        PromoCode::factory()->create(['code' => 'GRATIS', 'type' => 'percent', 'value' => 100]);

        Livewire::actingAs($this->owner)
            ->test(Checkout::class, ['invitation' => $this->invitation])
            ->set('promoInput', 'GRATIS')->call('applyPromo')
            ->call('pay')
            ->assertRedirect();

        $order = Order::first();
        $this->assertSame(OrderStatus::Paid, $order->status);
        $this->assertSame(InvitationStatus::Active, $this->invitation->refresh()->status);
        $this->assertSame(1, PromoCode::first()->used_count);
        Http::assertNothingSent();
    }

    public function test_other_user_cannot_checkout_or_view_order(): void
    {
        $this->fakeMidtrans();
        $order = $this->checkout();
        $intruder = User::factory()->create();

        $this->actingAs($intruder)->get(route('app.checkout', $this->invitation))->assertForbidden();
        $this->actingAs($intruder)->get(route('app.orders.show', $order))->assertForbidden();
    }

    public function test_valid_notification_activates_invitation_once(): void
    {
        $this->fakeMidtrans('settlement');
        $promo = PromoCode::factory()->create(['code' => 'HEMAT', 'type' => 'fixed', 'value' => 9000]);
        $order = $this->checkout('HEMAT');

        $this->postJson('/webhooks/midtrans', $this->notification($order))->assertOk();
        $this->postJson('/webhooks/midtrans', $this->notification($order))->assertOk();

        $order->refresh();
        $this->assertSame(OrderStatus::Paid, $order->status);
        $this->assertSame('bank_transfer', $order->payment_method);
        $this->assertSame('bca', $order->payment_channel);

        $invitation = $this->invitation->refresh();
        $this->assertSame(InvitationStatus::Active, $invitation->status);
        $this->assertNotNull($invitation->published_at);
        $this->assertTrue($invitation->active_until->between(now()->addDays(89), now()->addDays(91)));

        $this->assertSame(1, $promo->refresh()->used_count);
        Mail::assertQueued(PaymentSucceeded::class, 1);
    }

    public function test_invalid_signature_is_rejected_and_logged(): void
    {
        $this->fakeMidtrans('settlement');
        $order = $this->checkout();

        $this->postJson('/webhooks/midtrans', $this->notification($order, 'settlement', str_repeat('a', 128)))
            ->assertForbidden();

        $this->assertSame(OrderStatus::Pending, $order->refresh()->status);
        $this->assertDatabaseHas('payment_logs', ['order_id' => $order->id, 'error' => 'invalid signature']);
    }

    public function test_forged_settlement_is_ignored_when_status_api_says_pending(): void
    {
        // Attacker with a leaked signature cannot activate: the status API is the source of truth.
        $this->fakeMidtrans('pending');
        $order = $this->checkout();

        $this->postJson('/webhooks/midtrans', $this->notification($order, 'settlement'))->assertOk();

        $this->assertSame(OrderStatus::Pending, $order->refresh()->status);
        $this->assertSame(InvitationStatus::Draft, $this->invitation->refresh()->status);
    }

    public function test_amount_mismatch_does_not_activate(): void
    {
        $this->fakeMidtrans('settlement', gross: 1000);
        $order = $this->checkout();

        $this->postJson('/webhooks/midtrans', $this->notification($order))->assertOk();

        $this->assertSame(OrderStatus::Pending, $order->refresh()->status);
        $this->assertDatabaseHas('payment_logs', ['order_id' => $order->id, 'error' => 'amount mismatch']);
    }

    public function test_challenged_card_payment_is_not_activated(): void
    {
        $this->fakeMidtrans('capture', fraud: 'challenge');
        $order = $this->checkout();

        $this->postJson('/webhooks/midtrans', $this->notification($order, 'capture'))->assertOk();

        $this->assertSame(OrderStatus::Pending, $order->refresh()->status);
    }

    public function test_expire_notification_marks_order_expired(): void
    {
        $this->fakeMidtrans('expire');
        $order = $this->checkout();

        $this->postJson('/webhooks/midtrans', $this->notification($order, 'expire'))->assertOk();

        $this->assertSame(OrderStatus::Expired, $order->refresh()->status);
        $this->assertSame(InvitationStatus::Draft, $this->invitation->refresh()->status);
    }

    public function test_unknown_order_notification_is_acknowledged(): void
    {
        $this->postJson('/webhooks/midtrans', ['order_id' => 'nope', 'status_code' => '200', 'gross_amount' => '1.00', 'signature_key' => hash('sha512', 'nope2001.00'.self::KEY)])
            ->assertOk()
            ->assertJson(['status' => 'unknown_order']);
    }

    public function test_order_page_syncs_status_from_midtrans(): void
    {
        $this->fakeMidtrans('settlement');
        $order = $this->checkout();

        Livewire::actingAs($this->owner)
            ->test(OrderStatusPage::class, ['order' => $order])
            ->assertSee('Pembayaran berhasil');

        $this->assertSame(InvitationStatus::Active, $this->invitation->refresh()->status);
    }

    public function test_renewal_extends_from_current_end_date(): void
    {
        $this->fakeMidtrans('settlement');
        $end = now()->addDays(5)->startOfSecond();
        $this->invitation->forceFill(['status' => InvitationStatus::Active, 'published_at' => now()->subDays(85), 'active_until' => $end])->save();

        $order = $this->checkout();
        $this->assertSame('renewal', $order->type);

        $this->postJson('/webhooks/midtrans', $this->notification($order))->assertOk();

        $this->assertEquals($end->copy()->addDays(90)->timestamp, $this->invitation->refresh()->active_until->timestamp);
    }

    public function test_suspended_invitation_cannot_checkout(): void
    {
        $this->invitation->forceFill(['status' => InvitationStatus::Suspended])->save();

        $this->actingAs($this->owner)->get(route('app.checkout', $this->invitation))->assertForbidden();
    }

    public function test_scheduler_expires_and_reminds(): void
    {
        $expired = Invitation::factory()->active()->for($this->owner)->create(['active_until' => now()->subHour()]);
        $soon = Invitation::factory()->active()->for($this->owner)->create(['active_until' => now()->addDays(3)]);
        $later = Invitation::factory()->active()->for($this->owner)->create(['active_until' => now()->addDays(30)]);

        $this->artisan('invitations:expire')->assertSuccessful();
        $this->artisan('invitations:remind')->assertSuccessful();
        $this->artisan('invitations:remind')->assertSuccessful();

        $this->assertSame(InvitationStatus::Expired, $expired->refresh()->status);
        $this->assertSame(InvitationStatus::Active, $soon->refresh()->status);
        Mail::assertQueued(InvitationExpired::class, 1);
        Mail::assertQueued(InvitationExpiringSoon::class, fn ($m) => $m->invitation->is($soon));
        Mail::assertQueued(InvitationExpiringSoon::class, 1);
        $this->assertNull($later->refresh()->expiry_reminded_at);
    }

    public function test_stale_pending_orders_expire(): void
    {
        $this->fakeMidtrans();
        $order = $this->checkout();
        $order->forceFill(['expires_at' => now()->subHours(2)])->save();

        $this->artisan('orders:expire')->assertSuccessful();

        $this->assertSame(OrderStatus::Expired, $order->refresh()->status);
    }
}
