<?php

namespace App\Livewire\Invitation;

use App\Livewire\Invitation\Editor\Concerns\EditsInvitation;
use App\Models\EventGift;
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

    public function toggleSelfie(): void
    {
        $this->authorizeUpdate();
        $this->invitation->forceFill(['checkin_selfie' => ! $this->invitation->checkin_selfie])->save();
        $this->notify($this->invitation->checkin_selfie ? 'Selfie check-in diaktifkan.' : 'Selfie check-in dinonaktifkan.');
    }

    public function deleteSelfie(int $guestId, EventDayService $eventDay): void
    {
        $this->authorizeUpdate();
        $eventDay->deleteSelfie($this->invitation, $this->invitation->guests()->findOrFail($guestId));
        $this->notify('Foto dihapus.');
    }

    public function deleteGift(int $giftId): void
    {
        $this->authorizeUpdate();
        $this->invitation->eventGifts()->findOrFail($giftId)->delete();
        $this->notify('Catatan hadiah dihapus.');
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
            'recap' => $eventDay->recap($this->invitation),
            'gifts' => $this->invitation->eventGifts()->latest('id')->take(50)->get(),
            'giftKinds' => EventGift::KINDS,
            'selfies' => $this->invitation->guests()->whereNotNull('selfie_path')->latest('checked_in_at')->get(['id', 'name', 'selfie_path', 'checked_in_at']),
        ]);
    }
}
