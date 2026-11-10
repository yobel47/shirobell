<?php

namespace App\Livewire\Invitation\Editor;

use App\Livewire\Invitation\Editor\Concerns\EditsInvitation;
use Livewire\Component;

class TextsForm extends Component
{
    use EditsInvitation;

    private const FIELDS = ['opening_text', 'quote_text', 'quote_source', 'closing_text'];

    public array $form = [];

    public function fillForm(): void
    {
        foreach (self::FIELDS as $field) {
            $this->form[$field] = (string) $this->invitation->{$field};
        }
    }

    public function save(): void
    {
        $this->authorizeUpdate();
        $data = $this->validate()['form'];

        $this->invitation->fill(array_map(fn ($v) => $v === '' ? null : $v, $data))->save();
        $this->notify();
    }

    public function render()
    {
        return view('livewire.invitation.editor.texts-form');
    }

    protected function rules(): array
    {
        return [
            'form.opening_text' => ['nullable', 'string', 'max:1000'],
            'form.quote_text' => ['nullable', 'string', 'max:1000'],
            'form.quote_source' => ['nullable', 'string', 'max:150'],
            'form.closing_text' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.opening_text' => 'salam pembuka', 'form.quote_text' => 'kutipan',
            'form.quote_source' => 'sumber kutipan', 'form.closing_text' => 'penutup',
        ];
    }
}
