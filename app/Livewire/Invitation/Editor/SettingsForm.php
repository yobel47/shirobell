<?php

namespace App\Livewire\Invitation\Editor;

use App\Livewire\Invitation\Editor\Concerns\EditsInvitation;
use App\Models\Invitation;
use App\Services\InvitationService;
use App\Services\SlugService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class SettingsForm extends Component
{
    use EditsInvitation;

    public array $sections = [];

    public int $rsvp_max_pax = 2;

    public string $slug = '';

    public function fillForm(): void
    {
        foreach (array_keys(Invitation::SECTIONS) as $key) {
            $this->sections[$key] = $this->invitation->sectionEnabled($key);
        }
        $this->rsvp_max_pax = $this->invitation->rsvp_max_pax ?? 2;
        $this->slug = $this->invitation->slug;
    }

    public function save(): void
    {
        $this->authorizeUpdate();
        $this->validate([
            'sections' => ['array'],
            'sections.*' => ['boolean'],
            'rsvp_max_pax' => ['required', 'integer', 'min:1', 'max:10'],
        ], [], ['rsvp_max_pax' => 'maksimal tamu per RSVP']);

        $sections = [];
        foreach (array_keys(Invitation::SECTIONS) as $key) {
            $sections[$key] = (bool) ($this->sections[$key] ?? false);
        }

        $this->invitation->fill(['sections' => $sections, 'rsvp_max_pax' => $this->rsvp_max_pax])->save();
        $this->notify();
    }

    public function saveSlug(SlugService $slugs, InvitationService $service)
    {
        $this->authorizeUpdate();
        Gate::authorize('changeSlug', $this->invitation);

        $this->slug = strtolower(trim($this->slug));
        $this->validate(['slug' => $slugs->rules($this->invitation)], [
            'slug.regex' => 'Alamat hanya boleh huruf kecil, angka, dan tanda hubung (tidak di awal/akhir).',
            'slug.not_in' => 'Alamat ini tidak bisa dipakai. Silakan pilih yang lain.',
            'slug.unique' => 'Alamat ini sudah dipakai. Silakan pilih yang lain.',
        ], ['slug' => 'alamat undangan']);

        $service->changeSlug($this->invitation, $this->slug);
        session()->flash('status', 'Alamat undangan diubah.');

        // Full reload so the header and share links show the new address.
        return $this->redirectRoute('app.invitations.edit', ['invitation' => $this->invitation, 'tab' => 'pengaturan']);
    }

    public function deleteInvitation(InvitationService $service)
    {
        $this->invitation->refresh();
        Gate::authorize('delete', $this->invitation);

        $service->delete($this->invitation);
        session()->flash('status', 'Undangan dihapus.');

        return $this->redirectRoute('app.dashboard');
    }

    public function render()
    {
        return view('livewire.invitation.editor.settings-form', [
            'sectionLabels' => Invitation::SECTIONS,
            'canChangeSlug' => Gate::allows('changeSlug', $this->invitation),
            'canDelete' => Gate::allows('delete', $this->invitation),
        ]);
    }
}
