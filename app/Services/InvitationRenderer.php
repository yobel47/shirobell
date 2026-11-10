<?php

namespace App\Services;

use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Builds the single data contract every theme receives (ADR 005).
 * Themes only read these variables; they never query the database.
 */
class InvitationRenderer
{
    public function __construct(private readonly ThemeRegistry $themes) {}

    public function response(Invitation $invitation, ?string $guestName = null, bool $preview = false, ?Guest $guest = null): Response
    {
        return response()
            ->view($invitation->theme->viewName(), $this->data($invitation, $guestName, $preview, $guest))
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }

    /** @return array<string, mixed> */
    public function data(Invitation $invitation, ?string $guestName = null, bool $preview = false, ?Guest $guest = null): array
    {
        $invitation->loadMissing(['theme', 'events', 'photos', 'stories', 'giftAccounts', 'musicTrack']);

        $events = $invitation->events;
        $first = $events->first();

        return [
            'invitation' => $invitation,
            'preview' => $preview,
            'guestName' => $guestName ? Str::limit(trim(strip_tags($guestName)), 80, '') : null,
            'guest' => $guest,
            'events' => $events,
            'mainDate' => $first?->date,
            'countdownTarget' => $invitation->sectionEnabled('countdown') ? $first?->startsAt()->toIso8601String() : null,
            'photos' => $this->section($invitation, 'gallery', $invitation->photos),
            'stories' => $this->section($invitation, 'story', $invitation->stories),
            'giftAccounts' => $this->section($invitation, 'gift', $invitation->giftAccounts),
            'showInteraction' => $invitation->sectionEnabled('rsvp') || $invitation->sectionEnabled('wishes'),
            'musicUrl' => $invitation->musicUrl(),
            'coverUrl' => $invitation->mediaUrl($invitation->cover_photo_path),
            'groomPhotoUrl' => $invitation->mediaUrl($invitation->groom_photo_path),
            'bridePhotoUrl' => $invitation->mediaUrl($invitation->bride_photo_path),
            'calendarTitle' => 'Pernikahan '.$invitation->coupleName(),
            'themeStyle' => $this->themes->style($invitation->theme->slug, $invitation->theme_variant, $invitation->customization['font_pair'] ?? null),
            'themeCss' => $invitation->theme->cssEntry(),
            'engine' => $this->themes->engine($invitation->theme->slug),
            // Entry ticket on a guest's personal link once the receptionist module is open.
            'ticketQr' => $guest && $invitation->exists && app(EventDayService::class)->isOpen($invitation)
                ? app(EventDayService::class)->qrSvg($guest->code)
                : null,
        ];
    }

    private function section(Invitation $invitation, string $key, Collection $items): Collection
    {
        return $invitation->sectionEnabled($key) ? $items : collect();
    }
}
