<?php

namespace App\Livewire\Reception;

use App\Livewire\Reception\Concerns\ReceptionGuard;
use App\Services\EventDayService;
use App\Services\ThemeRegistry;
use Livewire\Component;

/** Welcome screen for a TV or projector: greets each guest right after check-in (polling). */
class WelcomeScreen extends Component
{
    use ReceptionGuard;

    /** Seconds a greeting stays on screen before returning to the idle view. */
    public const GREETING_SECONDS = 20;

    public function render(EventDayService $eventDay, ThemeRegistry $themes)
    {
        $this->ensureAccess();
        $arrivals = $eventDay->recentArrivals($this->invitation, 6);
        $latest = $arrivals->first();

        [$bg, $primary, $accent] = $themes->swatch($this->invitation->theme->slug, $this->invitation->theme_variant);

        return view('livewire.reception.welcome-screen', [
            'greeting' => $latest && $latest->checked_in_at->gt(now()->subSeconds(self::GREETING_SECONDS)) ? $latest : null,
            'arrivals' => $arrivals,
            'coverUrl' => $this->invitation->mediaUrl($this->invitation->cover_photo_path),
            'mainDate' => $this->invitation->events()->orderBy('date')->value('date'),
            'colors' => ['bg' => $bg, 'primary' => $primary, 'accent' => $accent],
        ]);
    }
}
