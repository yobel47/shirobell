<?php

namespace App\Services\Admin;

use App\Enums\InvitationStatus;
use App\Enums\OrderStatus;
use App\Models\Invitation;
use App\Models\Order;
use App\Models\User;
use App\Models\Wish;
use App\Services\InvitationService;
use App\Services\Payment\PaymentService;
use App\Services\SettingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Every privileged admin intervention goes through here so it is always
 * written to admin_activity_logs (PRD US-048).
 */
class AdminActionService
{
    public function __construct(
        private readonly AuditLogger $audit,
        private readonly InvitationService $invitations,
        private readonly PaymentService $payments,
        private readonly SettingService $settings,
    ) {}

    public function suspendInvitation(Invitation $invitation, string $reason): void
    {
        DB::transaction(function () use ($invitation, $reason) {
            $from = $invitation->status->value;
            $invitation->status = InvitationStatus::Suspended;
            $invitation->suspended_reason = $reason;
            $invitation->save();

            $this->audit->log('invitation.suspend', $invitation, ['from' => $from, 'reason' => $reason]);
        });
    }

    public function unsuspendInvitation(Invitation $invitation): void
    {
        DB::transaction(function () use ($invitation) {
            $invitation->status = match (true) {
                $invitation->published_at === null => InvitationStatus::Draft,
                $invitation->active_until?->isFuture() => InvitationStatus::Active,
                default => InvitationStatus::Expired,
            };
            $invitation->suspended_reason = null;
            $invitation->save();

            $this->audit->log('invitation.unsuspend', $invitation, ['to' => $invitation->status->value]);
        });
    }

    /** Manual activation / extension without payment (e.g. compensation). */
    public function extendInvitation(Invitation $invitation, int $days, string $reason): void
    {
        DB::transaction(function () use ($invitation, $days, $reason) {
            $before = $invitation->active_until?->toDateTimeString();
            $this->invitations->activate($invitation, $days);

            $this->audit->log('invitation.extend', $invitation, [
                'days' => $days, 'reason' => $reason, 'before' => $before, 'after' => $invitation->active_until?->toDateTimeString(),
            ]);
        });
    }

    /** Admin may change a slug even after publishing (customers cannot). */
    public function changeSlug(Invitation $invitation, string $slug): void
    {
        DB::transaction(function () use ($invitation, $slug) {
            $from = $invitation->slug;
            $invitation->slug = $slug;
            $invitation->save();

            $this->audit->log('invitation.change_slug', $invitation, ['from' => $from, 'to' => $slug]);
        });
    }

    /** Mark an order paid when the gateway confirms it but the notification never arrived. */
    public function markOrderPaid(Order $order, string $reason): void
    {
        if ($order->status === OrderStatus::Paid) {
            throw ValidationException::withMessages(['order' => 'Order sudah lunas.']);
        }

        $this->payments->markPaid($order, ['payment_type' => 'manual'], 'Manual oleh admin: '.$reason);
        $this->audit->log('order.mark_paid', $order, ['reason' => $reason, 'total' => $order->total_amount]);
    }

    public function syncOrder(Order $order): Order
    {
        $order = $this->payments->sync($order);
        $this->audit->log('order.sync', $order, ['status' => $order->status->value]);

        return $order;
    }

    public function setUserBlocked(User $user, bool $blocked): void
    {
        if ($user->is(auth()->user())) {
            throw ValidationException::withMessages(['user' => 'Anda tidak bisa memblokir akun sendiri.']);
        }

        $user->forceFill(['is_blocked' => $blocked])->save();
        $this->audit->log($blocked ? 'user.block' : 'user.unblock', $user);
    }

    public function setWishHidden(Wish $wish, bool $hidden): void
    {
        $wish->forceFill(['is_hidden' => $hidden])->save();
        $this->audit->log($hidden ? 'wish.hide' : 'wish.unhide', $wish);
    }

    /** @param array<string, mixed> $values */
    public function updateSettings(array $values): void
    {
        $changes = [];
        foreach ($values as $key => $value) {
            $old = $this->settings->get($key);
            if ((string) $old !== (string) $value) {
                $this->settings->set($key, $value);
                $changes[$key] = ['from' => $old, 'to' => $value];
            }
        }

        if ($changes) {
            $this->audit->log('settings.update', null, $changes);
        }
    }
}
