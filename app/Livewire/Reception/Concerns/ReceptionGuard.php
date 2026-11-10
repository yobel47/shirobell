<?php

namespace App\Livewire\Reception\Concerns;

use App\Models\Invitation;
use App\Services\EventDayService;
use App\Services\ReceptionAccess;
use Livewire\Attributes\Locked;

/**
 * Livewire actions are separate requests: every one re-checks that the
 * module is still open and that this browser holds a valid PIN session.
 */
trait ReceptionGuard
{
    #[Locked]
    public Invitation $invitation;

    public function mount(Invitation $invitation): void
    {
        $this->invitation = $invitation;
        $this->ensureAccess();
    }

    protected function ensureAccess(): void
    {
        $this->invitation->refresh();

        abort_unless(
            app(EventDayService::class)->isOpen($this->invitation) && app(ReceptionAccess::class)->has($this->invitation),
            403,
            'Sesi penerima tamu berakhir. Muat ulang halaman dan masukkan PIN.'
        );
    }
}
