<?php

namespace App\Livewire\Invitation\Editor\Concerns;

use App\Models\Invitation;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;

/**
 * Shared by every editor tab. Authorization runs on mount AND before every
 * write, because Livewire actions are separate HTTP requests.
 */
trait EditsInvitation
{
    #[Locked]
    public Invitation $invitation;

    public function mount(Invitation $invitation): void
    {
        Gate::authorize('update', $invitation);
        $this->invitation = $invitation;

        if (method_exists($this, 'fillForm')) {
            $this->fillForm();
        }
    }

    protected function authorizeUpdate(): void
    {
        $this->invitation->refresh();
        Gate::authorize('update', $this->invitation);
    }

    protected function notify(string $message = 'Perubahan tersimpan.'): void
    {
        $this->dispatch('notify', message: $message);
    }
}
