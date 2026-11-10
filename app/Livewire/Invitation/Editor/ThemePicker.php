<?php

namespace App\Livewire\Invitation\Editor;

use App\Livewire\Invitation\Editor\Concerns\EditsInvitation;
use App\Models\Theme;
use App\Services\InvitationService;
use App\Services\ThemeRegistry;
use Livewire\Attributes\Url;
use Livewire\Component;

class ThemePicker extends Component
{
    use EditsInvitation;

    #[Url(as: 'kategori')]
    public string $category = '';

    public function choose(int $themeId, ?string $variant, InvitationService $service): void
    {
        $this->authorizeUpdate();
        $service->changeTheme($this->invitation, $themeId, $variant);
        $this->notify('Tema diganti.');
        $this->dispatch('invitation-saved');
    }

    /** Engine themes only; '' restores the theme's own fonts. */
    public function setFontPair(string $pair): void
    {
        $this->authorizeUpdate();
        $customization = $this->invitation->customization ?? [];

        if ($pair === '' || ! array_key_exists($pair, config('themes.font_pairs', []))) {
            unset($customization['font_pair']);
        } else {
            $customization['font_pair'] = $pair;
        }

        $this->invitation->forceFill(['customization' => $customization ?: null])->save();
        $this->notify('Font diganti.');
        $this->dispatch('invitation-saved');
    }

    public function render(ThemeRegistry $registry)
    {
        $themes = Theme::query()->available()->get()->filter->hasView()
            ->when($this->category !== '', fn ($themes) => $themes->filter(fn (Theme $theme) => $theme->category() === $this->category))
            ->values();

        return view('livewire.invitation.editor.theme-picker', [
            'themes' => $themes,
            'categories' => $registry->categories(),
            'isEngine' => $registry->isEngine($this->invitation->theme->slug),
            'fontPairs' => config('themes.font_pairs', []),
            'fonts' => config('themes.fonts', []),
            'currentFontPair' => $this->invitation->customization['font_pair'] ?? '',
            'currentVariant' => $registry->variantKey($this->invitation->theme->slug, $this->invitation->theme_variant),
        ]);
    }
}
