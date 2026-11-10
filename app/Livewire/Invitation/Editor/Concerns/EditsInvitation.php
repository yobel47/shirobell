<?php

namespace App\Livewire\Invitation\Editor\Concerns;

use App\Models\Invitation;
use App\Policies\InvitationPolicy;
use App\Services\Admin\AdminEditAudit;
use App\Services\InvitationVersionService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;

/**
 * Shared by every editor tab. Authorization runs on mount AND before every
 * write, because Livewire actions are separate HTTP requests. When an admin
 * works on a customer's invitation, every change in the request is audited.
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

        if (InvitationPolicy::actingAsAdmin(auth()->user(), $this->invitation)) {
            app(AdminEditAudit::class)->begin($this->invitation, static::class);
        }

        // Keeps the state before this change restorable (ADR 010).
        if ($this->autoSnapshots()) {
            app(InvitationVersionService::class)->autoSnapshot($this->invitation, auth()->user());
        }
    }

    /** Components that create versions themselves override this. */
    protected function autoSnapshots(): bool
    {
        return true;
    }

    protected function notify(string $message = 'Perubahan tersimpan.'): void
    {
        $this->dispatch('notify', message: $message);
    }
}
