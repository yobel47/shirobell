<?php

namespace App\Livewire\Reception;

use App\Livewire\Reception\Concerns\ReceptionGuard;
use App\Models\EventGift;
use App\Models\Guest;
use App\Services\EventDayService;
use App\Services\GuestService;
use Livewire\Component;
use Livewire\WithFileUploads;

/** Receptionist app: scan a guest QR, search by name, or add a walk-in guest. */
class CheckinApp extends Component
{
    use ReceptionGuard;
    use WithFileUploads;

    public string $mode = 'scan';

    public string $search = '';

    public string $walkInName = '';

    public int $walkInPax = 1;

    /** Photo captured by the selfie camera (Livewire temporary upload). */
    public $selfie = null;

    public string $giftKind = 'amplop';

    public ?int $giftAmount = null;

    public string $giftNote = '';

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

    public function toggleSouvenir(int $guestId, EventDayService $eventDay): void
    {
        $this->ensureAccess();
        $guest = $eventDay->toggleSouvenir($this->invitation, $this->guest($guestId));
        $this->result = $this->resultFor($guest, $this->result['status'] ?? 'ok');
    }

    /** Records a gift handed over by the guest in the result card. */
    public function recordGift(EventDayService $eventDay): void
    {
        $this->ensureAccess();
        abort_unless(isset($this->result['id']), 404);
        $this->validate([
            'giftKind' => ['required', 'in:'.implode(',', array_keys(EventGift::KINDS))],
            'giftAmount' => ['nullable', 'integer', 'min:0', 'max:1000000000'],
            'giftNote' => ['nullable', 'string', 'max:200'],
        ], [], ['giftKind' => 'jenis', 'giftAmount' => 'nominal', 'giftNote' => 'catatan']);

        $guest = $this->guest($this->result['id']);
        $eventDay->recordGift($this->invitation, $guest, [
            'giver_name' => $guest->name, 'kind' => $this->giftKind, 'amount' => $this->giftAmount, 'note' => $this->giftNote,
        ]);
        $this->reset('giftKind', 'giftAmount', 'giftNote');
        $this->result = $this->resultFor($guest, $this->result['status'] ?? 'ok');
    }

    public function saveSelfie(EventDayService $eventDay): void
    {
        $this->ensureAccess();
        abort_unless($this->invitation->checkin_selfie && isset($this->result['id']), 404);
        $this->validate(['selfie' => ['required', 'image', 'max:8192']], [], ['selfie' => 'foto']);

        $guest = $eventDay->setSelfie($this->invitation, $this->guest($this->result['id']), $this->selfie);
        $this->reset('selfie');
        $this->result = $this->resultFor($guest, $this->result['status'] ?? 'ok');
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
            'giftKinds' => EventGift::KINDS,
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
            'table' => $guest->table_number,
            'souvenir' => $guest->souvenir_at !== null,
            'gifts' => $guest->gifts()->count(),
            'selfie' => $guest->selfieUrl(),
        ];
    }
}
