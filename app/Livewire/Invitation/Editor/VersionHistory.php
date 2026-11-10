<?php

namespace App\Livewire\Invitation\Editor;

use App\Livewire\Invitation\Editor\Concerns\EditsInvitation;
use App\Services\InvitationVersionService;
use Livewire\Component;

/** Editor tab "Riwayat": list, save and restore content snapshots (ADR 010). */
class VersionHistory extends Component
{
    use EditsInvitation;

    public function saveNow(InvitationVersionService $versions): void
    {
        $this->authorizeUpdate();
        $versions->snapshot($this->invitation, 'manual', auth()->user());
        $this->notify('Versi disimpan.');
    }

    public function restore(int $versionId, InvitationVersionService $versions): void
    {
        $this->authorizeUpdate();
        $version = $this->invitation->versions()->findOrFail($versionId);

        $versions->restore($version, auth()->user());
        $this->invitation->refresh();

        $this->notify('Versi '.$version->created_at->translatedFormat('d M, H.i').' dipulihkan.');
        $this->dispatch('invitation-saved');
    }

    protected function autoSnapshots(): bool
    {
        return false;
    }

    public function render()
    {
        return view('livewire.invitation.editor.version-history', [
            'versions' => $this->invitation->versions()->with('user:id,name,role')->get(),
            'max' => InvitationVersionService::MAX_VERSIONS,
            'interval' => InvitationVersionService::AUTO_INTERVAL_MINUTES,
        ]);
    }
}
