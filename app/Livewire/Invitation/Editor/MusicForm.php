<?php

namespace App\Livewire\Invitation\Editor;

use App\Livewire\Invitation\Editor\Concerns\EditsInvitation;
use App\Models\MusicTrack;
use App\Services\MediaService;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class MusicForm extends Component
{
    use EditsInvitation, WithFileUploads;

    /** 'none' | track id as string */
    public string $track = 'none';

    public $upload = null;

    public function fillForm(): void
    {
        $this->track = $this->invitation->music_track_id ? (string) $this->invitation->music_track_id : 'none';
    }

    public function selectTrack(): void
    {
        $this->authorizeUpdate();
        $this->validate([
            'track' => ['required', Rule::in(array_merge(['none'], $this->tracks()->pluck('id')->map(fn ($id) => (string) $id)->all()))],
        ]);

        $this->invitation->music_track_id = $this->track === 'none' ? null : (int) $this->track;
        $this->invitation->save();
        $this->notify('Musik tersimpan.');
    }

    public function saveUpload(MediaService $media): void
    {
        $this->authorizeUpdate();
        $this->validate(['upload' => MediaService::audioRules()], [], ['upload' => 'file musik']);

        $old = $this->invitation->custom_music_path;
        $this->invitation->custom_music_path = $media->storeAudio($this->upload, $this->invitation->mediaDirectory());
        $this->invitation->save();
        $media->delete($old);

        $this->reset('upload');
        $this->notify('Musik diunggah.');
    }

    public function removeUpload(MediaService $media): void
    {
        $this->authorizeUpdate();
        $media->delete($this->invitation->custom_music_path);
        $this->invitation->custom_music_path = null;
        $this->invitation->save();
        $this->notify('Musik unggahan dihapus.');
    }

    public function render()
    {
        return view('livewire.invitation.editor.music-form', ['tracks' => $this->tracks()]);
    }

    private function tracks()
    {
        return MusicTrack::query()->where('is_active', true)->orderBy('title')->get();
    }
}
