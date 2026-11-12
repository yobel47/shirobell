<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Livewire\Invitation\Editor;
use App\Models\Invitation;
use App\Policies\InvitationPolicy;
use App\Services\Admin\AuditLogger;
use App\Services\EventDayService;
use App\Services\InvitationRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class InvitationController extends Controller
{
    /** Editor tabs: URL segment => [label, Livewire component]. */
    public const TABS = [
        'mempelai' => ['Mempelai', Editor\CoupleForm::class],
        'acara' => ['Acara', Editor\EventsManager::class],
        'galeri' => ['Galeri', Editor\GalleryManager::class],
        'cerita' => ['Cerita', Editor\StoriesManager::class],
        'teks' => ['Teks', Editor\TextsForm::class],
        'musik' => ['Musik', Editor\MusicForm::class],
        'amplop' => ['Amplop', Editor\GiftManager::class],
        'tema' => ['Tema', Editor\ThemePicker::class],
        'desain' => ['Desain & Susunan', Editor\CanvasEditor::class],
        'pengaturan' => ['Pengaturan', Editor\SettingsForm::class],
        'riwayat' => ['Riwayat', Editor\VersionHistory::class],
    ];

    public function create(): View
    {
        return view('customer.invitations.create');
    }

    public function edit(Invitation $invitation, string $tab = 'mempelai'): View
    {
        Gate::authorize('update', $invitation);
        abort_unless(array_key_exists($tab, self::TABS), 404);
        $this->auditAdminVisit($invitation, 'editor:'.$tab);

        return view('customer.invitations.edit', [
            'invitation' => $invitation->load('theme'),
            'tabs' => self::TABS,
            'tab' => $tab,
        ]);
    }

    public function guests(Invitation $invitation): View
    {
        Gate::authorize('update', $invitation);
        $this->auditAdminVisit($invitation, 'tamu');

        return view('customer.invitations.guests', ['invitation' => $invitation]);
    }

    public function wishes(Invitation $invitation): View
    {
        Gate::authorize('update', $invitation);
        $this->auditAdminVisit($invitation, 'rsvp');

        return view('customer.invitations.wishes', ['invitation' => $invitation]);
    }

    /** Owner-only preview on the main domain; watermarked unless the invitation is active. */
    public function eventDay(Invitation $invitation): View
    {
        Gate::authorize('update', $invitation);
        $this->auditAdminVisit($invitation, 'hari-h');

        return view('customer.invitations.event-day', ['invitation' => $invitation]);
    }

    /** Printable QR cards; only once the receptionist module is open. */
    public function qrCards(Invitation $invitation, EventDayService $eventDay): View
    {
        Gate::authorize('update', $invitation);
        abort_unless($eventDay->isOpen($invitation), 404);
        $this->auditAdminVisit($invitation, 'kartu-qr');

        return view('customer.invitations.qr-cards', [
            'invitation' => $invitation,
            'guests' => $invitation->guests()->orderBy('group_name')->orderBy('name')->get(['id', 'name', 'group_name', 'code']),
            'qr' => fn (string $code) => $eventDay->qrSvg($code, 160),
        ]);
    }

    public function preview(Request $request, Invitation $invitation, InvitationRenderer $renderer): Response
    {
        Gate::authorize('view', $invitation);
        // The editor's live preview frame reloads on every save; the editor visit is already audited.
        if (! $request->boolean('frame')) {
            $this->auditAdminVisit($invitation, 'preview');
        }

        return $renderer->response(
            $invitation,
            guestName: $request->string('to')->toString() ?: null,
            preview: ! $invitation->isActive(),
        );
    }

    /** Admins opening a customer's invitation pages leave a trail (owners do not). */
    private function auditAdminVisit(Invitation $invitation, string $page): void
    {
        if (InvitationPolicy::actingAsAdmin(auth()->user(), $invitation)) {
            app(AuditLogger::class)->log('invitation.admin_open', $invitation, [
                'owner_id' => $invitation->user_id,
                'page' => $page,
            ]);
        }
    }
}
