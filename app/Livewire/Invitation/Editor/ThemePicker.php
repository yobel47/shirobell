<?php

namespace App\Livewire\Invitation\Editor;

use App\Livewire\Invitation\Editor\Concerns\EditsInvitation;
use App\Models\Theme;
use App\Services\InvitationService;
use Livewire\Component;

class ThemePicker extends Component
{
    use EditsInvitation;

    public function choose(int $themeId, InvitationService $service): void
    {
        $this->authorizeUpdate();
        $service->changeTheme($this->invitation, $themeId);
        $this->notify('Tema diganti.');
    }

    public function render()
    {
        return view('livewire.invitation.editor.theme-picker', [
            'themes' => Theme::query()->available()->get()->filter->hasView()->values(),
        ]);
    }
}
