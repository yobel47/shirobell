<?php

namespace App\Livewire\Invitation\Editor;

use App\Livewire\Invitation\Editor\Concerns\EditsInvitation;
use App\Livewire\Invitation\Editor\Concerns\ReordersItems;
use App\Models\InvitationEvent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;

class EventsManager extends Component
{
    use EditsInvitation, ReordersItems;

    public bool $showForm = false;

    #[Locked]
    public ?int $editingId = null;

    public array $form = [];

    public function create(): void
    {
        $this->authorizeUpdate();

        if ($this->invitation->events()->count() >= $this->max()) {
            $this->addError('form', "Maksimal {$this->max()} acara.");

            return;
        }

        $this->resetErrorBag();
        $this->editingId = null;
        $this->form = [
            'name' => $this->invitation->events()->exists() ? 'Resepsi' : 'Akad Nikah',
            'date' => '', 'start_time' => '', 'end_time' => '', 'timezone' => 'WIB',
            'venue_name' => '', 'address' => '', 'maps_url' => '',
        ];
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $this->authorizeUpdate();
        $event = $this->invitation->events()->findOrFail($id);

        $this->resetErrorBag();
        $this->editingId = $event->id;
        $this->form = [
            'name' => $event->name,
            'date' => $event->date->format('Y-m-d'),
            'start_time' => substr($event->start_time, 0, 5),
            'end_time' => $event->end_time ? substr($event->end_time, 0, 5) : '',
            'timezone' => $event->timezone,
            'venue_name' => $event->venue_name,
            'address' => (string) $event->address,
            'maps_url' => (string) $event->maps_url,
        ];
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->authorizeUpdate();
        $data = $this->validate()['form'];
        $data = array_map(fn ($v) => $v === '' ? null : $v, $data);

        if ($this->editingId) {
            $this->invitation->events()->findOrFail($this->editingId)->update($data);
        } else {
            if ($this->invitation->events()->count() >= $this->max()) {
                $this->addError('form', "Maksimal {$this->max()} acara.");

                return;
            }
            $data['sort_order'] = (int) $this->invitation->events()->max('sort_order') + 1;
            $this->invitation->events()->create($data);
        }

        $this->cancel();
        $this->notify('Acara tersimpan.');
    }

    public function delete(int $id): void
    {
        $this->authorizeUpdate();
        $this->invitation->events()->findOrFail($id)->delete();
        $this->notify('Acara dihapus.');
    }

    public function cancel(): void
    {
        $this->reset('showForm', 'editingId', 'form');
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.invitation.editor.events-manager', [
            'events' => $this->invitation->events()->get(),
            'timezones' => array_keys(InvitationEvent::TIMEZONES),
        ]);
    }

    protected function reorderRelation(): HasMany
    {
        return $this->invitation->events();
    }

    protected function rules(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:100'],
            'form.date' => ['required', 'date_format:Y-m-d'],
            'form.start_time' => ['required', 'date_format:H:i'],
            'form.end_time' => ['nullable', 'date_format:H:i'],
            'form.timezone' => ['required', Rule::in(array_keys(InvitationEvent::TIMEZONES))],
            'form.venue_name' => ['required', 'string', 'max:150'],
            'form.address' => ['nullable', 'string', 'max:500'],
            'form.maps_url' => ['nullable', 'url:https', 'max:500'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.name' => 'nama acara', 'form.date' => 'tanggal', 'form.start_time' => 'jam mulai',
            'form.end_time' => 'jam selesai', 'form.timezone' => 'zona waktu', 'form.venue_name' => 'nama tempat',
            'form.address' => 'alamat', 'form.maps_url' => 'link Google Maps',
        ];
    }

    private function max(): int
    {
        return (int) config('shirobell.invitation.max_events', 5);
    }
}
