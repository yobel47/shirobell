<?php

namespace App\Services;

use App\Models\Invitation;

/**
 * Receptionist session for one invitation, granted by the PIN. Bound to the
 * current check-in token, so resetting the link and PIN logs every device out.
 */
class ReceptionAccess
{
    public const HOURS = 12;

    public function grant(Invitation $invitation): void
    {
        session()->put($this->key($invitation), [
            'token' => hash('sha256', (string) $invitation->checkin_token),
            'expires' => now()->addHours(self::HOURS)->getTimestamp(),
        ]);
    }

    public function has(Invitation $invitation): bool
    {
        $access = session()->get($this->key($invitation));

        return is_array($access)
            && ($access['expires'] ?? 0) > now()->getTimestamp()
            && hash_equals((string) ($access['token'] ?? ''), hash('sha256', (string) $invitation->checkin_token));
    }

    public function revoke(Invitation $invitation): void
    {
        session()->forget($this->key($invitation));
    }

    private function key(Invitation $invitation): string
    {
        return 'reception.'.$invitation->id;
    }
}
