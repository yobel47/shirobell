<?php

namespace App\Services;

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
    public function response(Invitation $invitation, ?string $guestName = null, bool $preview = false): Response
    {
        return response()
            ->view($invitation->theme->viewName(), $this->data($invitation, $guestName, $preview))
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }

    /** @return array<string, mixed> */
    public function data(Invitation $invitation, ?string $guestName = null, bool $preview = false): array
    {
        $invitation->loadMissing(['theme', 'events', 'photos', 'stories', 'musicTrack']);

        $events = $invitation->events;
        $first = $events->first();

        return [
            'invitation' => $invitation,
            'preview' => $preview,
            'guestName' => $guestName ? Str::limit(trim(strip_tags($guestName)), 80, '') : null,
            'events' => $events,
            'mainDate' => $first?->date,
            'countdownTarget' => $invitation->sectionEnabled('countdown') ? $first?->startsAt()->toIso8601String() : null,
            'photos' => $this->section($invitation, 'gallery', $invitation->photos),
            'stories' => $this->section($invitation, 'story', $invitation->stories),
            'musicUrl' => $invitation->musicUrl(),
            'coverUrl' => $invitation->mediaUrl($invitation->cover_photo_path),
            'groomPhotoUrl' => $invitation->mediaUrl($invitation->groom_photo_path),
            'bridePhotoUrl' => $invitation->mediaUrl($invitation->bride_photo_path),
            'calendarTitle' => 'Pernikahan '.$invitation->coupleName(),
        ];
    }

    private function section(Invitation $invitation, string $key, Collection $items): Collection
    {
        return $invitation->sectionEnabled($key) ? $items : collect();
    }
}
