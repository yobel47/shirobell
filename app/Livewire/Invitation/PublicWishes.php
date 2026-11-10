<?php

namespace App\Livewire\Invitation;

use App\Enums\Attendance;
use App\Models\Guest;
use App\Models\Invitation;
use App\Services\WishService;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * RSVP + wishes form and wishes list on the public invitation page.
 * Each theme provides its own markup at themes/{slug}/livewire/wishes.blade.php.
 */
class PublicWishes extends Component
{
    #[Locked]
    public Invitation $invitation;

    #[Locked]
    public ?int $guestId = null;

    #[Locked]
    public bool $preview = false;

    public string $name = '';

    public string $attendance = '';

    public int $pax = 1;

    public string $message = '';

    /** Honeypot: hidden from humans, bots tend to fill it. */
    public string $website = '';

    public bool $submitted = false;

    public int $perPage = 10;

    public function mount(Invitation $invitation, ?int $guestId = null, ?string $guestName = null, bool $preview = false): void
    {
        $this->invitation = $invitation;
        $this->preview = $preview;

        $guest = $guestId ? $invitation->guests()->find($guestId) : null;
        $this->guestId = $guest?->id;
        $this->name = (string) ($guest?->name ?? $guestName ?? '');

        if ($guest && ($previous = $guest->rsvp)) {
            $this->attendance = (string) $previous->attendance?->value;
            $this->pax = $previous->pax ?? 1;
        }
    }

    public function submit(WishService $service): void
    {
        if ($this->website !== '') {
            $this->afterSubmit();

            return;
        }

        $this->validate();

        $service->submit($this->invitation, $this->guest(), [
            'name' => trim($this->name),
            'attendance' => $this->attendance ?: null,
            'pax' => $this->pax,
            'message' => trim($this->message) ?: null,
        ], (string) request()->ip());

        $this->afterSubmit();
    }

    public function loadMore(): void
    {
        $this->perPage = min($this->perPage + 10, 200);
    }

    public function render()
    {
        $view = "themes.{$this->invitation->theme->slug}.livewire.wishes";

        $wishes = $this->invitation->sectionEnabled('wishes')
            ? $this->invitation->wishes()->visibleMessages()->latest()->take($this->perPage)->get(['id', 'name', 'attendance', 'message', 'created_at'])
            : collect();

        return view(view()->exists($view) ? $view : 'livewire.invitation.public-wishes', [
            'wishes' => $wishes,
            'total' => $this->invitation->sectionEnabled('wishes') ? $this->invitation->wishes()->visibleMessages()->count() : 0,
            'rsvpEnabled' => $this->invitation->sectionEnabled('rsvp'),
            'wishesEnabled' => $this->invitation->sectionEnabled('wishes'),
            'attendanceOptions' => Attendance::cases(),
            'maxPax' => max(1, $this->invitation->rsvp_max_pax),
        ]);
    }

    protected function rules(): array
    {
        $rsvp = $this->invitation->sectionEnabled('rsvp');
        $wishes = $this->invitation->sectionEnabled('wishes');

        return [
            'name' => ['required', 'string', 'max:100'],
            'attendance' => [$rsvp && ! $wishes ? 'required' : 'nullable', Rule::in(array_column(Attendance::cases(), 'value'))],
            'pax' => ['required_if:attendance,yes', 'integer', 'min:1', 'max:'.max(1, $this->invitation->rsvp_max_pax)],
            'message' => [$wishes && ! $rsvp ? 'required' : 'nullable', 'string', 'max:500', 'required_without:attendance'],
        ];
    }

    protected function validationAttributes(): array
    {
        return ['name' => 'nama', 'attendance' => 'konfirmasi kehadiran', 'pax' => 'jumlah tamu', 'message' => 'ucapan'];
    }

    protected function messages(): array
    {
        return ['message.required_without' => 'Isi konfirmasi kehadiran atau ucapan.'];
    }

    private function guest(): ?Guest
    {
        return $this->guestId ? $this->invitation->guests()->find($this->guestId) : null;
    }

    private function afterSubmit(): void
    {
        $this->submitted = true;
        $this->message = '';
        $this->website = '';
        $this->resetErrorBag();
    }
}
