<?php

namespace App\Livewire\Invitation\Editor;

use App\Livewire\Invitation\Editor\Concerns\EditsInvitation;
use App\Services\CanvasService;
use App\Services\MediaService;
use App\Services\ThemeRegistry;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Editor tab "Desain": drag-and-drop designer for the cover and free pages,
 * plus section order and visibility (ADR 012). The canvas
 * state lives in the browser (resources/js/canvas-editor.js); the server
 * only stores sanitized snapshots of it.
 */
class CanvasEditor extends Component
{
    use EditsInvitation;
    use WithFileUploads;

    /** Uploaded photo for an image element (Livewire temporary upload). */
    public $photo = null;

    /** Max stored canvas photos per invitation (files, not elements). */
    public const MAX_FILES = 40;

    /** @return array<string, mixed> sanitized design document */
    #[Renderless]
    public function save(array $doc, CanvasService $canvas): array
    {
        $this->authorizeUpdate();
        abort_unless(app(ThemeRegistry::class)->isEngine($this->invitation->theme->slug), 422);

        $canvas->saveDocument($this->invitation, $doc);
        $this->notify(($this->invitation->canvas['cover']['enabled'] ?? false)
            ? 'Desain disimpan. Sampul desain sendiri dipakai.'
            : 'Desain disimpan. Sampul memakai desain tema.');
        $this->dispatch('invitation-saved');

        return $canvas->document($this->invitation);
    }

    /** @return array<string, mixed> starting design from the current theme */
    #[Renderless]
    public function template(CanvasService $canvas): array
    {
        $this->authorizeUpdate();

        return $canvas->sanitize($this->invitation, $canvas->template($this->invitation));
    }

    public function updatedPhoto(): void
    {
        $this->skipRender();
    }

    /** @return array{path: string, url: string} */
    #[Renderless]
    public function storePhoto(MediaService $media, CanvasService $canvas): array
    {
        $this->authorizeUpdate();
        $this->validate(['photo' => ['required', 'image', 'max:8192']], [], ['photo' => 'foto']);

        $directory = $this->invitation->mediaDirectory().'/canvas';
        abort_if(count(Storage::disk(MediaService::DISK)->files($directory)) >= self::MAX_FILES, 422, 'Batas foto desain tercapai.');

        $path = $media->storeImage($this->photo, $directory, 1600, 82);
        $this->reset('photo');

        return ['path' => $path, 'url' => $canvas->imageUrl($path)];
    }

    public function render(CanvasService $canvas, ThemeRegistry $themes)
    {
        $slug = $this->invitation->theme->slug;
        $isEngine = $themes->isEngine($slug);
        $doc = $isEngine ? $canvas->document($this->invitation) : null;

        $library = [];
        if ($isEngine) {
            foreach ($canvas->stickerKeys() as $set => $keys) {
                foreach ($keys as $key) {
                    $library["{$set}:{$key}"] = view('themes._engine.canvas.sticker', ['set' => $set, 'key' => $key])->render();
                }
            }
        }

        $images = [];
        foreach ([$doc['cover'] ?? [], ...($doc['pages'] ?? [])] as $page) {
            foreach ($page['elements'] ?? [] as $element) {
                if ($element['type'] === 'image' && $element['props']['path']) {
                    $images[$element['props']['path']] = $canvas->imageUrl($element['props']['path']);
                }
            }
        }

        // Where the content of each built-in section is edited.
        $tabs = ['hero' => 'mempelai', 'opening' => 'teks', 'couple' => 'mempelai', 'countdown' => 'pengaturan', 'events' => 'acara',
            'story' => 'cerita', 'gallery' => 'galeri', 'gift' => 'amplop', 'rsvp' => 'pengaturan', 'closing' => 'teks'];
        $sections = collect(CanvasService::SECTIONS)->map(fn ($label, $key) => [
            'label' => $label,
            'url' => route('app.invitations.edit', [$this->invitation, $tabs[$key]]),
        ])->all();

        $engine = $themes->engine($slug);

        return view('livewire.invitation.editor.canvas-editor', [
            'isEngine' => $isEngine,
            'doc' => $doc,
            'sections' => $sections,
            'library' => $library,
            'images' => $images,
            'themeStyle' => $themes->style($slug, $this->invitation->theme_variant, $this->invitation->customization['font_pair'] ?? null),
            'pattern' => $engine && $engine['pattern'] !== 'none' ? 't-pattern t-pattern-'.$engine['pattern'] : null,
            'context' => [
                'groom' => $this->invitation->groom_nickname ?: 'Mempelai Pria',
                'bride' => $this->invitation->bride_nickname ?: 'Mempelai Wanita',
                'date' => $this->invitation->events()->orderBy('date')->first()?->date?->translatedFormat('l, d F Y') ?? 'Sabtu, 14 Februari 2027',
                'guest' => 'Nama Tamu',
            ],
            'size' => ['w' => CanvasService::WIDTH, 'h' => CanvasService::HEIGHT],
        ]);
    }
}
