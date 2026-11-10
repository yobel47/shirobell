<?php

namespace App\Http\Controllers;

use App\Enums\InvitationStatus;
use App\Models\Invitation;
use App\Services\GuestService;
use App\Services\InvitationRenderer;
use App\Services\SlugService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Serves {slug}.APP_DOMAIN. Only active, non-expired invitations are rendered. */
class PublicInvitationController extends Controller
{
    public function __invoke(Request $request, string $slug, InvitationRenderer $renderer, SlugService $slugs, GuestService $guests): Response|RedirectResponse
    {
        if ($slugs->isReserved($slug)) {
            return redirect()->to(config('app.url'));
        }

        $invitation = Invitation::query()->where('slug', $slug)->first();

        if (! $invitation) {
            return $this->status('not-found', 404);
        }

        if ($invitation->isActive()) {
            $guest = $guests->findByCode($invitation, $request->string('g')->toString());
            if ($guest) {
                $guests->markOpened($guest);
            }

            return $renderer->response(
                $invitation,
                guestName: $guest?->name ?? ($request->string('to')->toString() ?: null),
                guest: $guest,
            );
        }

        return match ($invitation->status) {
            InvitationStatus::Suspended => $this->status('suspended', 403),
            InvitationStatus::Draft => $this->status('draft', 404),
            default => $this->status('expired', 410),
        };
    }

    private function status(string $state, int $code): Response
    {
        return response()
            ->view('invitation-status', ['state' => $state], $code)
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
