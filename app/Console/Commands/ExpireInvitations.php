<?php

namespace App\Console\Commands;

use App\Enums\InvitationStatus;
use App\Mail\InvitationExpired;
use App\Models\Invitation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ExpireInvitations extends Command
{
    protected $signature = 'invitations:expire';

    protected $description = 'Mark active invitations past active_until as expired';

    public function handle(): int
    {
        $count = 0;

        Invitation::query()
            ->where('status', InvitationStatus::Active)
            ->where('active_until', '<', now())
            ->with('user')
            ->chunkById(100, function ($invitations) use (&$count) {
                foreach ($invitations as $invitation) {
                    // Conditional update keeps this safe if a payment extends it meanwhile.
                    $updated = Invitation::query()->whereKey($invitation->id)
                        ->where('status', InvitationStatus::Active)
                        ->where('active_until', '<', now())
                        ->update(['status' => InvitationStatus::Expired->value, 'updated_at' => now()]);

                    if ($updated) {
                        $count++;
                        Mail::to($invitation->user)->queue(new InvitationExpired($invitation));
                    }
                }
            });

        $this->info("{$count} undangan berakhir.");

        return self::SUCCESS;
    }
}
