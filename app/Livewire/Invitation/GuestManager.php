<?php

namespace App\Livewire\Invitation;

use App\Livewire\Invitation\Editor\Concerns\EditsInvitation;
use App\Services\GuestImportService;
use App\Services\GuestService;
use App\Services\InvitationService;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class GuestManager extends Component
{
    use EditsInvitation, WithFileUploads, WithPagination;

    /** CSV or Excel file with guests (Livewire temporary upload). */
    public $importFile = null;

    /** Summary of the last import, shown under the form. */
    public ?array $importResult = null;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'grup')]
    public string $group = '';

    public string $bulk = '';

    public string $bulkGroup = '';

    public bool $showBulk = false;

    #[Locked]
    public ?int $editingId = null;

    public array $form = [];

    public string $template = '';

    public function fillForm(): void
    {
        $this->template = $this->invitation->wa_message_template ?: InvitationService::DEFAULT_WA_TEMPLATE;
        $this->showBulk = ! $this->invitation->guests()->exists();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedGroup(): void
    {
        $this->resetPage();
    }

    public function addBulk(GuestService $guests): void
    {
        $this->authorizeUpdate();
        $this->validate([
            'bulk' => ['required', 'string', 'max:20000'],
            'bulkGroup' => ['nullable', 'string', 'max:50'],
        ], [], ['bulk' => 'daftar nama', 'bulkGroup' => 'grup']);

        $rows = $guests->parseLines($this->bulk);
        $maxLines = (int) config('shirobell.invitation.bulk_guest_lines', 200);

        if ($rows === []) {
            $this->addError('bulk', 'Tidak ada nama yang bisa ditambahkan.');

            return;
        }
        if (count($rows) > $maxLines) {
            $this->addError('bulk', "Maksimal {$maxLines} nama sekali tambah.");

            return;
        }

        $count = $guests->bulkCreate($this->invitation, $rows, trim($this->bulkGroup) ?: null);

        $this->reset('bulk', 'showBulk');
        $this->resetPage();
        $this->notify("{$count} tamu ditambahkan.");
    }

    public function importGuests(GuestImportService $importer): void
    {
        $this->authorizeUpdate();
        $this->validate(
            ['importFile' => ['required', 'file', 'max:2048', 'mimes:csv,txt,xlsx']],
            ['importFile.mimes' => 'Gunakan file CSV atau Excel (.xlsx).'],
            ['importFile' => 'file'],
        );

        $this->importResult = $importer->import($this->invitation, $this->importFile->getRealPath(), $this->importFile->getClientOriginalExtension());
        $this->reset('importFile');
        $this->resetPage();
        $this->notify($this->importResult['added'].' tamu ditambahkan dari file.');
    }

    public function edit(int $id): void
    {
        $this->authorizeUpdate();
        $guest = $this->invitation->guests()->findOrFail($id);

        $this->resetErrorBag();
        $this->editingId = $guest->id;
        $this->form = ['name' => $guest->name, 'phone' => (string) $guest->phone, 'group_name' => (string) $guest->group_name, 'table_number' => (string) $guest->table_number];
    }

    public function update(GuestService $guests): void
    {
        $this->authorizeUpdate();
        $guest = $this->invitation->guests()->findOrFail($this->editingId);

        $data = $this->validate([
            'form.name' => ['required', 'string', 'max:100'],
            'form.phone' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9\s-]{8,20}$/'],
            'form.group_name' => ['nullable', 'string', 'max:50'],
            'form.table_number' => ['nullable', 'string', 'max:20'],
        ], [], ['form.name' => 'nama', 'form.phone' => 'nomor WhatsApp', 'form.group_name' => 'grup', 'form.table_number' => 'nomor meja'])['form'];

        $guest->update([
            'name' => trim($data['name']),
            'phone' => $guests->normalizePhone($data['phone'] ?? null),
            'group_name' => trim((string) ($data['group_name'] ?? '')) ?: null,
            'table_number' => trim((string) ($data['table_number'] ?? '')) ?: null,
        ]);

        $this->cancelEdit();
        $this->notify('Tamu diperbarui.');
    }

    public function cancelEdit(): void
    {
        $this->reset('editingId', 'form');
        $this->resetErrorBag();
    }

    public function delete(int $id): void
    {
        $this->authorizeUpdate();
        $this->invitation->guests()->findOrFail($id)->delete();
        $this->notify('Tamu dihapus.');
    }

    /** Called when the owner taps "Kirim WA"; the link itself opens in a new tab. */
    public function markShared(int $id): void
    {
        $this->authorizeUpdate();
        $guest = $this->invitation->guests()->findOrFail($id);
        $guest->forceFill(['shared_at' => now()])->save();
    }

    public function saveTemplate(): void
    {
        $this->authorizeUpdate();
        $this->validate(['template' => ['required', 'string', 'max:2000']], [], ['template' => 'template pesan']);

        $this->invitation->fill(['wa_message_template' => $this->template])->save();
        $this->notify('Template pesan tersimpan.');
    }

    public function resetTemplate(): void
    {
        $this->template = InvitationService::DEFAULT_WA_TEMPLATE;
    }

    public function render(GuestService $service)
    {
        $base = $this->invitation->guests();

        $guests = $this->invitation->guests()
            ->with('rsvp')
            ->when($this->search !== '', fn ($q) => $q->where('name', 'like', '%'.addcslashes($this->search, '%_\\').'%'))
            ->when($this->group !== '', fn ($q) => $q->where('group_name', $this->group))
            ->latest('id')
            ->paginate(25);

        return view('livewire.invitation.guest-manager', [
            'guests' => $guests,
            'service' => $service,
            'groups' => (clone $base)->whereNotNull('group_name')->distinct()->orderBy('group_name')->pluck('group_name'),
            'stats' => [
                'total' => (clone $base)->count(),
                'shared' => (clone $base)->whereNotNull('shared_at')->count(),
                'opened' => (clone $base)->whereNotNull('opened_at')->count(),
                'rsvp' => (clone $base)->whereHas('wishes', fn ($q) => $q->whereNotNull('attendance'))->count(),
            ],
            'isActive' => $this->invitation->isActive(),
        ]);
    }
}
