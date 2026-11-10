<?php

namespace App\Livewire\Invitation;

use App\Enums\Attendance;
use App\Livewire\Invitation\Editor\Concerns\EditsInvitation;
use App\Services\WishService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class WishesRecap extends Component
{
    use EditsInvitation, WithPagination;

    /** '' | yes | no | maybe | messages | hidden */
    #[Url(as: 'filter')]
    public string $filter = '';

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function toggleHidden(int $id): void
    {
        $this->authorizeUpdate();
        $wish = $this->invitation->wishes()->findOrFail($id);
        $wish->forceFill(['is_hidden' => ! $wish->is_hidden])->save();
        $this->notify($wish->is_hidden ? 'Ucapan disembunyikan dari undangan.' : 'Ucapan ditampilkan kembali.');
    }

    public function delete(int $id): void
    {
        $this->authorizeUpdate();
        $this->invitation->wishes()->findOrFail($id)->delete();
        $this->notify('Data dihapus.');
    }

    public function render(WishService $service)
    {
        $wishes = $this->invitation->wishes()
            ->with('guest:id,name,group_name')
            ->when(in_array($this->filter, ['yes', 'no', 'maybe'], true), fn ($q) => $q->where('attendance', $this->filter))
            ->when($this->filter === 'messages', fn ($q) => $q->whereNotNull('message')->where('message', '!=', ''))
            ->when($this->filter === 'hidden', fn ($q) => $q->where('is_hidden', true))
            ->latest()
            ->paginate(20);

        return view('livewire.invitation.wishes-recap', [
            'wishes' => $wishes,
            'stats' => $service->stats($this->invitation),
            'filters' => ['' => 'Semua', 'yes' => Attendance::Yes->label(), 'no' => Attendance::No->label(), 'maybe' => Attendance::Maybe->label(), 'messages' => 'Ada ucapan', 'hidden' => 'Disembunyikan'],
        ]);
    }
}
