<?php

namespace App\Livewire\Invitation\Editor;

use App\Livewire\Invitation\Editor\Concerns\EditsInvitation;
use App\Livewire\Invitation\Editor\Concerns\ReordersItems;
use App\Services\MediaService;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

class StoriesManager extends Component
{
    use EditsInvitation, ReordersItems, WithFileUploads;

    public bool $showForm = false;

    #[Locked]
    public ?int $editingId = null;

    public array $form = [];

    public $photo = null;

    public function create(): void
    {
        $this->authorizeUpdate();

        if ($this->invitation->stories()->count() >= $this->max()) {
            $this->addError('form', "Maksimal {$this->max()} cerita.");

            return;
        }

        $this->resetErrorBag();
        $this->reset('editingId', 'photo');
        $this->form = ['title' => '', 'story_date' => '', 'body' => ''];
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $this->authorizeUpdate();
        $story = $this->invitation->stories()->findOrFail($id);

        $this->resetErrorBag();
        $this->reset('photo');
        $this->editingId = $story->id;
        $this->form = ['title' => $story->title, 'story_date' => (string) $story->story_date, 'body' => $story->body];
        $this->showForm = true;
    }

    public function save(MediaService $media): void
    {
        $this->authorizeUpdate();
        $validated = $this->validate();
        $data = array_map(fn ($v) => $v === '' ? null : $v, $validated['form']);

        $story = $this->editingId
            ? $this->invitation->stories()->findOrFail($this->editingId)
            : null;

        if (! $story && $this->invitation->stories()->count() >= $this->max()) {
            $this->addError('form', "Maksimal {$this->max()} cerita.");

            return;
        }

        if ($this->photo) {
            $old = $story?->photo_path;
            $data['photo_path'] = $media->storeImage($this->photo, $this->invitation->mediaDirectory(), 1000);
            $media->delete($old);
        }

        if ($story) {
            $story->update($data);
        } else {
            $data['sort_order'] = (int) $this->invitation->stories()->max('sort_order') + 1;
            $this->invitation->stories()->create($data);
        }

        $this->cancel();
        $this->notify('Cerita tersimpan.');
    }

    public function delete(int $id, MediaService $media): void
    {
        $this->authorizeUpdate();
        $story = $this->invitation->stories()->findOrFail($id);
        $media->delete($story->photo_path);
        $story->delete();
        $this->notify('Cerita dihapus.');
    }

    public function cancel(): void
    {
        $this->reset('showForm', 'editingId', 'form', 'photo');
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.invitation.editor.stories-manager', [
            'stories' => $this->invitation->stories()->get(),
            'editing' => $this->editingId ? $this->invitation->stories()->find($this->editingId) : null,
        ]);
    }

    protected function reorderRelation(): HasMany
    {
        return $this->invitation->stories();
    }

    protected function rules(): array
    {
        return [
            'form.title' => ['required', 'string', 'max:150'],
            'form.story_date' => ['nullable', 'string', 'max:50'],
            'form.body' => ['required', 'string', 'max:2000'],
            'photo' => MediaService::imageRules(),
        ];
    }

    protected function validationAttributes(): array
    {
        return ['form.title' => 'judul', 'form.story_date' => 'waktu', 'form.body' => 'cerita', 'photo' => 'foto'];
    }

    private function max(): int
    {
        return (int) config('shirobell.invitation.max_stories', 10);
    }
}
