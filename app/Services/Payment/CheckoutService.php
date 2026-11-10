<?php

namespace App\Services\Payment;

use App\Enums\InvitationStatus;
use App\Enums\OrderStatus;
use App\Models\Invitation;
use App\Models\Order;
use App\Models\PromoCode;
use App\Models\User;
use App\Services\SettingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class CheckoutService
{
    public function __construct(
        private readonly SettingService $settings,
        private readonly PromoService $promos,
        private readonly MidtransClient $midtrans,
        private readonly PaymentService $payments,
    ) {}

    /**
     * Price snapshot shown on the checkout page and stored on the order.
     *
     * @return array{type: string, base: int, discount: int, total: int, days: int, promo: ?PromoCode}
     */
    public function quote(Invitation $invitation, ?string $promoCode = null): array
    {
        $base = $this->settings->packagePrice();
        $promo = filled($promoCode) ? $this->promos->resolve($promoCode) : null;
        $discount = $promo?->discountFor($base) ?? 0;

        return [
            'type' => $invitation->published_at ? 'renewal' : 'new',
            'base' => $base,
            'discount' => $discount,
            'total' => $base - $discount,
            'days' => $this->settings->packageDurationDays(),
            'promo' => $promo,
        ];
    }

    /**
     * Create (or reuse) a pending order and return it with a payment URL.
     * A fully discounted order is settled immediately without Midtrans.
     */
    public function start(User $user, Invitation $invitation, ?string $promoCode = null): Order
    {
        if ($invitation->user_id !== $user->id || $invitation->status === InvitationStatus::Suspended) {
            throw ValidationException::withMessages(['checkout' => 'Undangan ini tidak bisa dibayar.']);
        }

        $quote = $this->quote($invitation, $promoCode);

        $reusable = $invitation->orders()
            ->where('status', OrderStatus::Pending)
            ->where('total_amount', $quote['total'])
            ->where('promo_code_id', $quote['promo']?->id)
            ->where('expires_at', '>', now()->addMinutes(10))
            ->whereNotNull('payment_url')
            ->latest('id')
            ->first();

        if ($reusable) {
            return $reusable;
        }

        $order = DB::transaction(function () use ($user, $invitation, $quote) {
            $order = new Order;
            $order->forceFill([
                'uuid' => (string) Str::uuid(),
                'number' => $this->newNumber(),
                'user_id' => $user->id,
                'invitation_id' => $invitation->id,
                'type' => $quote['type'],
                'base_amount' => $quote['base'],
                'discount_amount' => $quote['discount'],
                'total_amount' => $quote['total'],
                'promo_code_id' => $quote['promo']?->id,
                'duration_days' => $quote['days'],
                'status' => OrderStatus::Pending,
                'expires_at' => now()->addHours((int) config('services.midtrans.expiry_hours', 24)),
            ])->save();

            return $order;
        });

        if ($order->total_amount === 0) {
            $this->payments->markPaid($order, ['payment_type' => 'promo'], 'Promo 100%');

            return $order->refresh();
        }

        try {
            $snap = $this->midtrans->createTransaction($order);
        } catch (Throwable $e) {
            Log::error('Midtrans checkout failed', ['order' => $order->number, 'error' => $e->getMessage()]);
            $order->forceFill(['status' => OrderStatus::Failed])->save();

            throw ValidationException::withMessages(['checkout' => 'Gagal terhubung ke Midtrans. Silakan coba beberapa saat lagi.']);
        }

        $order->forceFill(['snap_token' => $snap['token'], 'payment_url' => $snap['redirect_url']])->save();

        return $order;
    }

    private function newNumber(): string
    {
        do {
            $number = 'INV-'.now()->format('Ymd').'-'.strtoupper(Str::random(5));
        } while (Order::query()->where('number', $number)->exists());

        return $number;
    }
}
