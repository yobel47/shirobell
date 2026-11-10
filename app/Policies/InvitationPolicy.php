<?php

namespace App\Policies;

use App\Enums\InvitationStatus;
use App\Models\Invitation;
use App\Models\User;

/**
 * Customer area access: only the owner. Admin management goes through the
 * Filament panel (M5) with its own authorization.
 */
class InvitationPolicy
{
    public function view(User $user, Invitation $invitation): bool
    {
        return $invitation->user_id === $user->id;
    }

    public function update(User $user, Invitation $invitation): bool
    {
        return $invitation->user_id === $user->id
            && $invitation->status !== InvitationStatus::Suspended;
    }

    /** Paid invitations cannot be deleted by the customer. */
    public function delete(User $user, Invitation $invitation): bool
    {
        return $invitation->user_id === $user->id
            && $invitation->status === InvitationStatus::Draft;
    }

    /** The subdomain is locked once the invitation has been published. */
    public function changeSlug(User $user, Invitation $invitation): bool
    {
        return $this->update($user, $invitation) && $invitation->published_at === null;
    }
}
