<?php

namespace App\Policies;

use App\Enums\InvitationStatus;
use App\Models\Invitation;
use App\Models\User;

/**
 * Owners manage their own invitations. Admins may also view and edit any
 * invitation's content through the same editor (support use case); every
 * admin change there is recorded by AdminEditAudit. Admins can never delete
 * a customer's invitation from the editor.
 */
class InvitationPolicy
{
    public function view(User $user, Invitation $invitation): bool
    {
        return $this->owns($user, $invitation) || $this->isAdmin($user);
    }

    public function update(User $user, Invitation $invitation): bool
    {
        if ($this->isAdmin($user)) {
            return ! $invitation->trashed();
        }

        return $this->owns($user, $invitation)
            && $invitation->status !== InvitationStatus::Suspended;
    }

    /** Paid invitations cannot be deleted by the customer. */
    public function delete(User $user, Invitation $invitation): bool
    {
        return $this->owns($user, $invitation)
            && $invitation->status === InvitationStatus::Draft;
    }

    /** Locked for customers once published; admins may change it (audited). */
    public function changeSlug(User $user, Invitation $invitation): bool
    {
        if ($this->isAdmin($user)) {
            return $this->update($user, $invitation);
        }

        return $this->update($user, $invitation) && $invitation->published_at === null;
    }

    /** True when an admin is working on someone else's invitation. */
    public static function actingAsAdmin(?User $user, Invitation $invitation): bool
    {
        return $user !== null && $user->isAdmin() && ! $user->is_blocked && $invitation->user_id !== $user->id;
    }

    private function owns(User $user, Invitation $invitation): bool
    {
        return $invitation->user_id === $user->id;
    }

    private function isAdmin(User $user): bool
    {
        return $user->isAdmin() && ! $user->is_blocked;
    }
}
