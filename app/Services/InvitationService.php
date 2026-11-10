<?php

namespace App\Services;

use App\Enums\InvitationStatus;
use App\Models\Invitation;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Owns invitation lifecycle changes that are not plain content edits:
 * creation, slug, theme and deletion. Activation is added with payments (M4).
 */
class InvitationService
{
    public const DEFAULT_OPENING = 'Dengan memohon rahmat dan ridho Tuhan Yang Maha Esa, kami bermaksud menyelenggarakan pernikahan putra-putri kami:';

    public const DEFAULT_CLOSING = 'Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir dan memberikan doa restu. Atas kehadiran dan doanya, kami ucapkan terima kasih.';

    public const DEFAULT_WA_TEMPLATE = "Kepada Yth.\n{nama_tamu}\n\nTanpa mengurangi rasa hormat, kami mengundang Anda untuk hadir di acara pernikahan kami.\n\nInfo lengkap acara:\n{link}\n\nMerupakan kebahagiaan bagi kami apabila Anda berkenan hadir dan memberikan doa restu.\n\nTerima kasih.\n{nama_pasangan}";

    public function __construct(
        private readonly SlugService $slugs,
        private readonly MediaService $media,
    ) {}

    /**
     * @param  array{groom_nickname: string, bride_nickname: string, slug: string, theme_id: int}  $data  validated
     */
    public function create(User $user, array $data): Invitation
    {
        $limit = (int) config('shirobell.invitation.max_drafts_per_user', 5);
        $drafts = Invitation::ownedBy($user)->where('status', InvitationStatus::Draft)->count();

        if ($drafts >= $limit) {
            throw ValidationException::withMessages([
                'slug' => "Anda sudah memiliki {$limit} undangan draft. Selesaikan atau hapus salah satunya terlebih dahulu.",
            ]);
        }

        $theme = $this->availableTheme((int) $data['theme_id']);

        return DB::transaction(function () use ($user, $data, $theme) {
            $invitation = new Invitation([
                'groom_nickname' => $data['groom_nickname'],
                'bride_nickname' => $data['bride_nickname'],
                'opening_text' => self::DEFAULT_OPENING,
                'closing_text' => self::DEFAULT_CLOSING,
                'wa_message_template' => self::DEFAULT_WA_TEMPLATE,
                'sections' => array_fill_keys(array_keys(Invitation::SECTIONS), true),
            ]);
            $invitation->user()->associate($user);
            $invitation->theme()->associate($theme);
            $invitation->slug = $data['slug'];
            $invitation->status = InvitationStatus::Draft;
            $invitation->save();

            return $invitation;
        });
    }

    public function changeTheme(Invitation $invitation, int $themeId): void
    {
        $invitation->theme()->associate($this->availableTheme($themeId));
        $invitation->save();
    }

    public function changeSlug(Invitation $invitation, string $slug): void
    {
        if ($invitation->published_at !== null) {
            throw ValidationException::withMessages(['slug' => 'Alamat undangan tidak bisa diubah setelah diterbitkan.']);
        }

        $invitation->slug = $slug;
        $invitation->save();
    }

    public function delete(Invitation $invitation): void
    {
        if ($invitation->status !== InvitationStatus::Draft) {
            throw ValidationException::withMessages(['invitation' => 'Hanya undangan draft yang bisa dihapus.']);
        }

        // Free the subdomain so it can be claimed again. A double hyphen can never
        // match SlugService::PATTERN, so the placeholder cannot collide with a real slug.
        DB::transaction(function () use ($invitation) {
            $invitation->slug = 'deleted--'.$invitation->id;
            $invitation->save();
            $invitation->delete();
        });

        $this->media->deleteDirectory($invitation->mediaDirectory());
    }

    /**
     * Publish or extend after a verified payment (or an audited admin action in M5).
     * An active invitation is extended from its current end date; otherwise from now.
     * A suspended invitation keeps its status but still receives the paid period.
     */
    public function activate(Invitation $invitation, int $days): void
    {
        $from = $invitation->active_until && $invitation->active_until->isFuture()
            ? $invitation->active_until
            : now();

        $invitation->active_until = $from->copy()->addDays($days);
        $invitation->published_at ??= now();
        $invitation->expiry_reminded_at = null;

        if ($invitation->status !== InvitationStatus::Suspended) {
            $invitation->status = InvitationStatus::Active;
        }

        $invitation->save();
    }

    private function availableTheme(int $themeId): Theme
    {
        $theme = Theme::query()->where('is_active', true)->find($themeId);

        if (! $theme || ! $theme->hasView()) {
            throw ValidationException::withMessages(['theme_id' => 'Tema tidak tersedia.']);
        }

        return $theme;
    }
}
