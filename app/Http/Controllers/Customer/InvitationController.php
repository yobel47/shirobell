<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Livewire\Invitation\Editor;
use App\Models\Invitation;
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
        'tema' => ['Tema', Editor\ThemePicker::class],
        'pengaturan' => ['Pengaturan', Editor\SettingsForm::class],
    ];

    public function create(): View
    {
        return view('customer.invitations.create');
    }

    public function edit(Invitation $invitation, string $tab = 'mempelai'): View
    {
        Gate::authorize('update', $invitation);
        abort_unless(array_key_exists($tab, self::TABS), 404);

        return view('customer.invitations.edit', [
            'invitation' => $invitation->load('theme'),
            'tabs' => self::TABS,
            'tab' => $tab,
        ]);
    }

    /** Owner-only preview on the main domain; watermarked unless the invitation is active. */
    public function preview(Request $request, Invitation $invitation, InvitationRenderer $renderer): Response
    {
        Gate::authorize('view', $invitation);

        return $renderer->response(
            $invitation,
            guestName: $request->string('to')->toString() ?: null,
            preview: ! $invitation->isActive(),
        );
    }
}
