<?php

namespace App\Livewire\Reception;

use App\Livewire\Reception\Concerns\ReceptionGuard;
use App\Models\Guest;
use App\Services\EventDayService;
use App\Services\GuestService;
use Livewire\Component;

/** Receptionist app: scan a guest QR, search by name, or add a walk-in guest. */
class CheckinApp extends Component
{
    use ReceptionGuard;

    public string $mode = 'scan';

    public string $search = '';

    public string $walkInName = '';

    public int $walkInPax = 1;

    /** Last action shown in the result card: ok | already | unknown. */
    public ?array $result = null;

    public function scan(string $payload, EventDayService $eventDay, GuestService $guests): void
    {
        $this->ensureAccess();

        $guest = $guests->findByCode($this->invitation, $this->codeFrom($payload));
        if (! $guest) {
            $this->result = ['status' => 'unknown'];

            return;
        }

        $this->checkInGuest($guest, $eventDay->suggestedPax($guest), $eventDay);
    }

    public function checkIn(int $guestId, int $pax, EventDayService $eventDay): void
    {
        $this->ensureAccess();
        $this->checkInGuest($this->guest($guestId), $pax, $eventDay);
    }

    public function setPax(int $guestId, int $pax, EventDayService $eventDay): void
    {
        $this->ensureAccess();
        $guest = $this->guest($guestId);
        $eventDay->updatePax($this->invitation, $guest, $pax);
        $this->result = $this->resultFor($guest->refresh(), $this->result['status'] ?? 'ok');
    }

    public function undo(int $guestId, EventDayService $eventDay): void
    {
        $this->ensureAccess();
        $eventDay->undoCheckIn($this->invitation, $this->guest($guestId));
        $this->result = null;
    }

    public function addWalkIn(EventDayService $eventDay): void
    {
        $this->ensureAccess();
        $this->validate([
            'walkInName' => ['required', 'string', 'min:2', 'max:100'],
            'walkInPax' => ['required', 'integer', 'min:1', 'max:'.EventDayService::MAX_PAX],
        ], [], ['walkInName' => 'nama tamu', 'walkInPax' => 'jumlah orang']);

        $guest = $eventDay->walkIn($this->invitation, $this->walkInName, $this->walkInPax);
        $this->result = $this->resultFor($guest, 'ok');
        $this->reset('walkInName', 'walkInPax');
    }

    public function render(EventDayService $eventDay)
    {
        $term = trim($this->search);
        $matches = mb_strlen($term) >= 2
            ? $this->invitation->guests()->with('rsvp')
                ->where(fn ($q) => $q->where('name', 'like', '%'.addcslashes($term, '%_\\').'%')->orWhere('group_name', 'like', '%'.addcslashes($term, '%_\\').'%'))
                ->orderBy('name')->take(20)->get()
            : collect();

        return view('livewire.reception.checkin-app', [
            'stats' => $eventDay->stats($this->invitation),
            'matches' => $matches,
            'maxPax' => EventDayService::MAX_PAX,
        ]);
    }

    private function checkInGuest(Guest $guest, int $pax, EventDayService $eventDay): void
    {
        $outcome = $eventDay->checkIn($this->invitation, $guest, $pax);
        $this->result = $this->resultFor($outcome['guest'], $outcome['already'] ? 'already' : 'ok');
    }

    /** Accepts a bare guest code or a personal link containing ?g=code. */
    private function codeFrom(string $payload): string
    {
        $payload = trim(mb_substr($payload, 0, 500));
        if (str_contains($payload, 'g=')) {
            parse_str((string) parse_url($payload, PHP_URL_QUERY), $query);
            $payload = (string) ($query['g'] ?? '');
        }

        return strtolower($payload);
    }

    private function guest(int $id): Guest
    {
        return $this->invitation->guests()->findOrFail($id);
    }

    private function resultFor(Guest $guest, string $status): array
    {
        return [
            'status' => $status,
            'id' => $guest->id,
            'name' => $guest->name,
            'group' => $guest->group_name,
            'pax' => $guest->checked_in_pax,
            'time' => $guest->checked_in_at?->format('H.i'),
            'walk_in' => $guest->is_walk_in,
        ];
    }
}
