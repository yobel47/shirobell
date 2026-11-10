<?php

namespace App\Livewire\Invitation\Editor;

use App\Livewire\Invitation\Editor\Concerns\EditsInvitation;
use App\Livewire\Invitation\Editor\Concerns\ReordersItems;
use App\Models\GiftAccount;
use App\Services\MediaService;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Couple's own bank / e-wallet / QRIS details (ADR 006).
 * The platform only displays them; no funds pass through Shirobell.
 */
class GiftManager extends Component
{
    use EditsInvitation, ReordersItems, WithFileUploads;

    public bool $showForm = false;

    #[Locked]
    public ?int $editingId = null;

    public array $form = [];

    public $qris = null;

    public function create(string $type = 'bank'): void
    {
        $this->authorizeUpdate();

        if ($this->invitation->giftAccounts()->count() >= $this->max()) {
            $this->addError('form', "Maksimal {$this->max()} akun.");

            return;
        }

        $this->resetErrorBag();
        $this->reset('editingId', 'qris');
        $type = array_key_exists($type, GiftAccount::TYPES) ? $type : 'bank';
        $this->form = [
            'type' => $type,
            'provider' => $type === 'qris' ? 'QRIS' : '',
            'account_number' => '',
            'account_name' => '',
        ];
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $this->authorizeUpdate();
        $account = $this->invitation->giftAccounts()->findOrFail($id);

        $this->resetErrorBag();
        $this->reset('qris');
        $this->editingId = $account->id;
        $this->form = $account->only(['type', 'provider', 'account_number', 'account_name']);
        $this->form['account_number'] = (string) $this->form['account_number'];
        $this->showForm = true;
    }

    public function save(MediaService $media): void
    {
        $this->authorizeUpdate();

        $account = $this->editingId ? $this->invitation->giftAccounts()->findOrFail($this->editingId) : null;
        $this->validate($this->rulesFor($account));

        if (! $account && $this->invitation->giftAccounts()->count() >= $this->max()) {
            $this->addError('form', "Maksimal {$this->max()} akun.");

            return;
        }

        $data = [
            'type' => $this->form['type'],
            'provider' => trim($this->form['provider']),
            'account_name' => trim($this->form['account_name']),
            'account_number' => $this->form['type'] === 'qris' ? null : preg_replace('/[^0-9A-Za-z-]/', '', $this->form['account_number']),
        ];

        $account ??= $this->invitation->giftAccounts()->make(['sort_order' => (int) $this->invitation->giftAccounts()->max('sort_order') + 1]);
        $account->fill($data);

        if ($this->qris && $data['type'] === 'qris') {
            $old = $account->qris_image_path;
            // Higher quality and size keep the QR code scannable.
            $account->qris_image_path = $media->storeImage($this->qris, $this->invitation->mediaDirectory(), 1200, 92);
            $media->delete($old);
        } elseif ($data['type'] !== 'qris' && $account->qris_image_path) {
            $media->delete($account->qris_image_path);
            $account->qris_image_path = null;
        }

        $account->save();
        $this->cancel();
        $this->notify('Akun tersimpan.');
    }

    public function delete(int $id, MediaService $media): void
    {
        $this->authorizeUpdate();
        $account = $this->invitation->giftAccounts()->findOrFail($id);
        $media->delete($account->qris_image_path);
        $account->delete();
        $this->notify('Akun dihapus.');
    }

    public function cancel(): void
    {
        $this->reset('showForm', 'editingId', 'form', 'qris');
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.invitation.editor.gift-manager', [
            'accounts' => $this->invitation->giftAccounts()->get(),
            'types' => GiftAccount::TYPES,
            'editing' => $this->editingId ? $this->invitation->giftAccounts()->find($this->editingId) : null,
        ]);
    }

    protected function reorderRelation(): HasMany
    {
        return $this->invitation->giftAccounts();
    }

    private function rulesFor(?GiftAccount $account): array
    {
        $isQris = ($this->form['type'] ?? null) === 'qris';
        $needsImage = $isQris && ! $account?->qris_image_path;

        return [
            'form.type' => ['required', Rule::in(array_keys(GiftAccount::TYPES))],
            'form.provider' => ['required', 'string', 'max:50'],
            'form.account_name' => ['required', 'string', 'max:100'],
            'form.account_number' => [$isQris ? 'nullable' : 'required', 'string', 'max:50', 'regex:/^[0-9A-Za-z\s.-]+$/'],
            'qris' => MediaService::imageRules($needsImage),
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.type' => 'jenis', 'form.provider' => 'nama bank / e-wallet', 'form.account_name' => 'atas nama',
            'form.account_number' => 'nomor rekening / akun', 'qris' => 'gambar QRIS',
        ];
    }

    private function max(): int
    {
        return (int) config('shirobell.invitation.max_gift_accounts', 6);
    }
}
