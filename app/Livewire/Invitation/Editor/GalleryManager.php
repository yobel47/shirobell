<?php

namespace App\Livewire\Invitation\Editor;

use App\Livewire\Invitation\Editor\Concerns\EditsInvitation;
use App\Livewire\Invitation\Editor\Concerns\ReordersItems;
use App\Services\MediaService;
use App\Services\SettingService;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Livewire\Component;
use Livewire\WithFileUploads;

class GalleryManager extends Component
{
    use EditsInvitation, ReordersItems, WithFileUploads;

    public $cover = null;

    /** @var array<int, mixed> */
    public array $uploads = [];

    public function updatedCover(): void
    {
        $this->validateOnly('cover', ['cover' => MediaService::imageRules(true)], [], ['cover' => 'foto sampul']);
    }

    public function saveCover(MediaService $media): void
    {
        $this->authorizeUpdate();
        $this->validate(['cover' => MediaService::imageRules(true)], [], ['cover' => 'foto sampul']);

        $old = $this->invitation->cover_photo_path;
        $this->invitation->cover_photo_path = $media->storeImage($this->cover, $this->invitation->mediaDirectory());
        $this->invitation->save();
        $media->delete($old);

        $this->reset('cover');
        $this->notify('Foto sampul tersimpan.');
    }

    public function removeCover(MediaService $media): void
    {
        $this->authorizeUpdate();
        $media->delete($this->invitation->cover_photo_path);
        $this->invitation->cover_photo_path = null;
        $this->invitation->save();
        $this->notify('Foto sampul dihapus.');
    }

    /** Runs automatically after the browser finishes uploading the selected files. */
    public function updatedUploads(MediaService $media): void
    {
        $this->authorizeUpdate();

        $remaining = $this->max() - $this->invitation->photos()->count();
        $this->validate(
            ['uploads' => ['array', 'max:'.max($remaining, 0)], 'uploads.*' => MediaService::imageRules(true)],
            ['uploads.max' => $remaining > 0 ? "Sisa slot galeri hanya {$remaining} foto." : "Galeri sudah penuh ({$this->max()} foto)."],
            ['uploads.*' => 'foto'],
        );

        $order = (int) $this->invitation->photos()->max('sort_order');
        foreach ($this->uploads as $upload) {
            $this->invitation->photos()->create([
                'path' => $media->storeImage($upload, $this->invitation->mediaDirectory()),
                'sort_order' => ++$order,
            ]);
        }

        $count = count($this->uploads);
        $this->reset('uploads');
        $this->notify("{$count} foto ditambahkan.");
    }

    public function deletePhoto(int $id, MediaService $media): void
    {
        $this->authorizeUpdate();
        $photo = $this->invitation->photos()->findOrFail($id);
        $media->delete($photo->path);
        $photo->delete();
        $this->notify('Foto dihapus.');
    }

    public function render()
    {
        return view('livewire.invitation.editor.gallery-manager', [
            'photos' => $this->invitation->photos()->get(),
            'max' => $this->max(),
        ]);
    }

    protected function reorderRelation(): HasMany
    {
        return $this->invitation->photos();
    }

    private function max(): int
    {
        return app(SettingService::class)->int('gallery_max_photos');
    }
}
