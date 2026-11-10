<?php

namespace App\Livewire\Invitation;

use App\Models\Theme;
use App\Services\InvitationService;
use App\Services\SlugService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class CreateWizard extends Component
{
    public int $step = 1;

    public string $groom_nickname = '';

    public string $bride_nickname = '';

    public string $slug = '';

    public bool $slugTouched = false;

    public ?int $theme_id = null;

    public function mount(): void
    {
        $this->theme_id = $this->themes->first()?->id;
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['groom_nickname', 'bride_nickname'], true) && ! $this->slugTouched) {
            $this->slug = app(SlugService::class)->suggest($this->groom_nickname, $this->bride_nickname);
        }

        if ($property === 'slug') {
            $this->slugTouched = true;
            $this->slug = strtolower(trim($this->slug));
            $this->validateOnly('slug', $this->stepOneRules());
        }
    }

    #[Computed]
    public function themes(): Collection
    {
        return Theme::query()->available()->get()->filter->hasView()->values();
    }

    #[Computed]
    public function slugAvailable(): bool
    {
        return $this->slug !== '' && app(SlugService::class)->isAvailable($this->slug);
    }

    public function next(): void
    {
        $this->validate($this->stepOneRules());
        $this->step = 2;
    }

    public function back(): void
    {
        $this->step = 1;
    }

    public function create(InvitationService $service)
    {
        $data = $this->validate($this->stepOneRules() + [
            'theme_id' => ['required', 'integer'],
        ]);

        $invitation = $service->create(auth()->user(), $data);

        session()->flash('status', 'Undangan berhasil dibuat. Silakan lengkapi isinya.');

        return $this->redirectRoute('app.invitations.edit', ['invitation' => $invitation], navigate: false);
    }

    public function render()
    {
        return view('livewire.invitation.create-wizard');
    }

    protected function stepOneRules(): array
    {
        return [
            'groom_nickname' => ['required', 'string', 'max:50'],
            'bride_nickname' => ['required', 'string', 'max:50'],
            'slug' => app(SlugService::class)->rules(),
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'groom_nickname' => 'nama panggilan pria',
            'bride_nickname' => 'nama panggilan wanita',
            'slug' => 'alamat undangan',
            'theme_id' => 'tema',
        ];
    }

    protected function messages(): array
    {
        return [
            'slug.regex' => 'Alamat hanya boleh huruf kecil, angka, dan tanda hubung (tidak di awal/akhir).',
            'slug.not_in' => 'Alamat ini tidak bisa dipakai. Silakan pilih yang lain.',
            'slug.unique' => 'Alamat ini sudah dipakai. Silakan pilih yang lain.',
        ];
    }
}
