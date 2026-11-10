<?php

namespace App\Livewire\Invitation;

use App\Livewire\Invitation\Editor\Concerns\EditsInvitation;
use App\Services\EventDayService;
use Livewire\Component;

/** Owner page "Hari-H": enable the receptionist link, PIN, stats and arrivals. */
class EventDayManager extends Component
{
    use EditsInvitation;

    public function enable(EventDayService $eventDay): void
    {
        $this->authorizeUpdate();
        $eventDay->enable($this->invitation);
        $this->notify('Penerima tamu diaktifkan.');
    }

    public function resetAccess(EventDayService $eventDay): void
    {
        $this->authorizeUpdate();
        $eventDay->resetAccess($this->invitation);
        $this->notify('Tautan dan PIN baru dibuat. Perangkat lama harus masuk ulang.');
    }

    public function disable(EventDayService $eventDay): void
    {
        $this->authorizeUpdate();
        $eventDay->disable($this->invitation);
        $this->notify('Penerima tamu dinonaktifkan.');
    }

    protected function autoSnapshots(): bool
    {
        return false;
    }

    public function render(EventDayService $eventDay)
    {
        return view('livewire.invitation.event-day-manager', [
            'available' => $eventDay->isAvailable($this->invitation),
            'unlocked' => $eventDay->isUnlocked($this->invitation),
            'addonMode' => $eventDay->isAddonMode(),
            'addonPrice' => $eventDay->addonPrice(),
            'open' => $eventDay->isOpen($this->invitation),
            'stats' => $eventDay->stats($this->invitation),
            'arrivals' => $eventDay->recentArrivals($this->invitation, 10),
        ]);
    }
}
