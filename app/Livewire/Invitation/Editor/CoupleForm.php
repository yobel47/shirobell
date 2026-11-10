<?php

namespace App\Livewire\Invitation\Editor;

use App\Livewire\Invitation\Editor\Concerns\EditsInvitation;
use App\Services\MediaService;
use Livewire\Component;
use Livewire\WithFileUploads;

class CoupleForm extends Component
{
    use EditsInvitation, WithFileUploads;

    private const FIELDS = ['nickname', 'fullname', 'father', 'mother', 'child_order', 'instagram'];

    public array $form = [];

    public $groomPhoto = null;

    public $bridePhoto = null;

    public function fillForm(): void
    {
        foreach (['groom', 'bride'] as $side) {
            foreach (self::FIELDS as $field) {
                $this->form["{$side}_{$field}"] = (string) $this->invitation->{"{$side}_{$field}"};
            }
        }
    }

    public function save(MediaService $media): void
    {
        $this->authorizeUpdate();

        foreach (['groom_instagram', 'bride_instagram'] as $key) {
            $this->form[$key] = $this->normalizeInstagram($this->form[$key] ?? '');
        }

        $this->validate();

        $values = array_intersect_key($this->form, array_flip($this->formKeys()));
        $this->invitation->fill(array_map(fn ($v) => $v === '' ? null : $v, $values));

        foreach (['groom' => $this->groomPhoto, 'bride' => $this->bridePhoto] as $side => $upload) {
            if ($upload) {
                $old = $this->invitation->{"{$side}_photo_path"};
                $this->invitation->{"{$side}_photo_path"} = $media->storeImage($upload, $this->invitation->mediaDirectory(), 900);
                $media->delete($old);
            }
        }

        $this->invitation->save();
        $this->reset('groomPhoto', 'bridePhoto');
        $this->notify();
    }

    public function removePhoto(string $side, MediaService $media): void
    {
        $this->authorizeUpdate();
        abort_unless(in_array($side, ['groom', 'bride'], true), 404);

        $media->delete($this->invitation->{"{$side}_photo_path"});
        $this->invitation->{"{$side}_photo_path"} = null;
        $this->invitation->save();
        $this->notify('Foto dihapus.');
    }

    public function render()
    {
        return view('livewire.invitation.editor.couple-form');
    }

    protected function rules(): array
    {
        $rules = [];

        foreach (['groom', 'bride'] as $side) {
            $rules["form.{$side}_nickname"] = ['required', 'string', 'max:50'];
            $rules["form.{$side}_fullname"] = ['nullable', 'string', 'max:150'];
            $rules["form.{$side}_father"] = ['nullable', 'string', 'max:150'];
            $rules["form.{$side}_mother"] = ['nullable', 'string', 'max:150'];
            $rules["form.{$side}_child_order"] = ['nullable', 'string', 'max:50'];
            $rules["form.{$side}_instagram"] = ['nullable', 'string', 'max:30', 'regex:/^[A-Za-z0-9._]+$/'];
            $rules["{$side}Photo"] = MediaService::imageRules();
        }

        return $rules;
    }

    protected function validationAttributes(): array
    {
        $labels = [
            'nickname' => 'nama panggilan', 'fullname' => 'nama lengkap', 'father' => 'nama ayah',
            'mother' => 'nama ibu', 'child_order' => 'urutan anak', 'instagram' => 'username Instagram',
        ];
        $attributes = ['groomPhoto' => 'foto mempelai pria', 'bridePhoto' => 'foto mempelai wanita'];

        foreach (['groom' => 'pria', 'bride' => 'wanita'] as $side => $label) {
            foreach ($labels as $field => $text) {
                $attributes["form.{$side}_{$field}"] = "{$text} {$label}";
            }
        }

        return $attributes;
    }

    /** @return array<int, string> */
    private function formKeys(): array
    {
        $keys = [];
        foreach (['groom', 'bride'] as $side) {
            foreach (self::FIELDS as $field) {
                $keys[] = "{$side}_{$field}";
            }
        }

        return $keys;
    }

    private function normalizeInstagram(string $value): string
    {
        $value = preg_replace('#^(https?://)?(www\.)?instagram\.com/#i', '', trim($value));

        return trim(ltrim((string) $value, '@'), '/');
    }
}
