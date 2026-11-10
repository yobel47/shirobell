<?php

namespace App\Services\Payment;

use App\Enums\OrderStatus;
use App\Mail\PaymentSucceeded;
use App\Models\Order;
use App\Models\PaymentLog;
use App\Services\InvitationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * The only place where an order becomes paid and an invitation is activated.
 * Every status decision is based on Midtrans' status API, never on the
 * notification body or the browser redirect alone (ADR 007).
 */
class PaymentService
{
    public const RESULT_OK = 'ok';

    public const RESULT_INVALID_SIGNATURE = 'invalid_signature';

    public const RESULT_UNKNOWN_ORDER = 'unknown_order';

    public function __construct(
        private readonly MidtransClient $midtrans,
        private readonly InvitationService $invitations,
        private readonly PromoService $promos,
    ) {}

    /** HTTP notification from Midtrans. */
    public function handleNotification(array $payload): string
    {
        $order = Order::query()->where('uuid', (string) ($payload['order_id'] ?? ''))->first();
        $log = $this->log($order, $payload['transaction_status'] ?? null, $payload);

        if (! $this->midtrans->verifySignature($payload)) {
            $log->forceFill(['error' => 'invalid signature'])->save();

            return self::RESULT_INVALID_SIGNATURE;
        }

        if (! $order) {
            $log->forceFill(['error' => 'unknown order'])->save();

            return self::RESULT_UNKNOWN_ORDER;
        }

        // Throws on network errors so Midtrans retries the notification.
        $this->sync($order, $log);

        return self::RESULT_OK;
    }

    /**
     * Ask Midtrans for the current status and apply it. Used by the
     * notification handler and the "cek status" action on the order page.
     */
    public function sync(Order $order, ?PaymentLog $log = null): Order
    {
        $status = $this->midtrans->status($order->uuid);

        if ($status === null) {
            $log?->forceFill(['is_processed' => true, 'error' => 'not found at gateway'])->save();

            return $order;
        }

        $log ??= $this->log($order, ($status['transaction_status'] ?? null).' (status api)', $status);
        $error = $this->apply($order, $status);
        $log->forceFill(['is_processed' => $error === null, 'error' => $error])->save();

        return $order->refresh();
    }

    /** Settle an order. Idempotent: a paid order is never processed twice. */
    public function markPaid(Order $order, array $gateway, ?string $note = null): void
    {
        $paid = DB::transaction(function () use ($order, $gateway) {
            $locked = Order::query()->whereKey($order->id)->lockForUpdate()->first();

            if ($locked->status === OrderStatus::Paid) {
                return false;
            }

            $locked->forceFill([
                'status' => OrderStatus::Paid,
                'paid_at' => now(),
                'gateway_transaction_id' => $gateway['transaction_id'] ?? $locked->gateway_transaction_id,
                'payment_method' => $gateway['payment_type'] ?? null,
                'payment_channel' => $this->channel($gateway),
            ])->save();

            $this->promos->markUsed($locked->promo_code_id);
            $this->invitations->activate($locked->invitation, $locked->duration_days);

            return true;
        });

        if ($paid) {
            if ($note) {
                $this->log($order, 'paid', ['note' => $note])->forceFill(['is_processed' => true])->save();
            }
            Mail::to($order->user)->queue(new PaymentSucceeded($order->fresh(['invitation', 'user'])));
        }
    }

    /** @return string|null error message when the status could not be applied */
    private function apply(Order $order, array $status): ?string
    {
        $transaction = (string) ($status['transaction_status'] ?? '');
        $fraud = (string) ($status['fraud_status'] ?? 'accept');

        $isPaid = $transaction === 'settlement' || ($transaction === 'capture' && $fraud === 'accept');

        if ($isPaid) {
            if ((int) round((float) ($status['gross_amount'] ?? 0)) !== $order->total_amount) {
                return 'amount mismatch';
            }
            $this->markPaid($order, $status);

            return null;
        }

        $final = match ($transaction) {
            'expire' => OrderStatus::Expired,
            'cancel', 'deny', 'failure' => OrderStatus::Failed,
            default => null,
        };

        if ($final) {
            Order::query()->whereKey($order->id)
                ->where('status', OrderStatus::Pending)
                ->update(['status' => $final->value, 'updated_at' => now()]);
        }

        return null;
    }

    private function channel(array $gateway): ?string
    {
        $channel = $gateway['va_numbers'][0]['bank']
            ?? $gateway['issuer']
            ?? $gateway['acquirer']
            ?? $gateway['store']
            ?? $gateway['bank']
            ?? (isset($gateway['permata_va_number']) ? 'permata' : null);

        return $channel ? substr((string) $channel, 0, 50) : null;
    }

    private function log(?Order $order, ?string $event, array $payload): PaymentLog
    {
        $log = new PaymentLog;
        $log->forceFill([
            'order_id' => $order?->id,
            'provider' => 'midtrans',
            'event' => $event ? substr($event, 0, 50) : null,
            'payload' => $payload,
            'created_at' => now(),
        ])->save();

        return $log;
    }
}
