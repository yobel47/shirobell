<?php

namespace App\Console\Commands;

use App\Enums\InvitationStatus;
use App\Mail\InvitationExpiringSoon;
use App\Models\Invitation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class RemindExpiringInvitations extends Command
{
    protected $signature = 'invitations:remind';

    protected $description = 'Email owners whose invitation ends within 7 days (once per period)';

    public function handle(): int
    {
        $count = 0;

        Invitation::query()
            ->where('status', InvitationStatus::Active)
            ->whereBetween('active_until', [now(), now()->addDays(7)])
            ->whereNull('expiry_reminded_at')
            ->with('user')
            ->chunkById(100, function ($invitations) use (&$count) {
                foreach ($invitations as $invitation) {
                    $invitation->forceFill(['expiry_reminded_at' => now()])->save();
                    Mail::to($invitation->user)->queue(new InvitationExpiringSoon($invitation));
                    $count++;
                }
            });

        $this->info("{$count} pengingat dikirim.");

        return self::SUCCESS;
    }
}
