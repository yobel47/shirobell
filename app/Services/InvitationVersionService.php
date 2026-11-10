<?php

namespace App\Services;

use App\Models\Invitation;
use App\Models\InvitationVersion;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Restorable snapshots of an invitation's content (ADR 010).
 *
 * Covered: invitation texts and settings, theme, variant, customization,
 * events, stories and gift accounts. Not covered: gallery photos, couple and
 * cover photos, music, guests and wishes (live data). Files deleted since
 * the snapshot cannot come back; their paths are dropped on restore.
 */
class InvitationVersionService
{
    public const MAX_VERSIONS = 30;

    public const AUTO_INTERVAL_MINUTES = 10;

    private const INVITATION_FIELDS = [
        'groom_nickname', 'groom_fullname', 'groom_father', 'groom_mother', 'groom_child_order', 'groom_instagram',
        'bride_nickname', 'bride_fullname', 'bride_father', 'bride_mother', 'bride_child_order', 'bride_instagram',
        'opening_text', 'quote_text', 'quote_source', 'closing_text',
        'sections', 'rsvp_max_pax', 'wa_message_template',
        'theme_id', 'theme_variant', 'customization',
    ];

    private const EVENT_FIELDS = ['name', 'date', 'start_time', 'end_time', 'timezone', 'venue_name', 'address', 'maps_url', 'sort_order'];

    private const STORY_FIELDS = ['title', 'story_date', 'body', 'photo_path', 'sort_order'];

    private const GIFT_FIELDS = ['type', 'provider', 'account_number', 'account_name', 'qris_image_path', 'sort_order'];

    public function snapshot(Invitation $invitation, string $reason, ?User $user = null): InvitationVersion
    {
        // Read from the database: an in-memory model may lack column defaults.
        $fresh = $invitation->fresh(['events', 'stories', 'giftAccounts']);

        $version = $invitation->versions()->create([
            'user_id' => $user?->id,
            'reason' => $reason,
            'snapshot' => [
                'invitation' => collect(self::INVITATION_FIELDS)->mapWithKeys(fn ($f) => [$f => $fresh->getAttribute($f)])->all(),
                'events' => $fresh->events->map(fn ($e) => [
                    ...$e->only(self::EVENT_FIELDS),
                    'date' => $e->date?->toDateString(),
                ])->values()->all(),
                'stories' => $fresh->stories->map->only(self::STORY_FIELDS)->values()->all(),
                'gifts' => $fresh->giftAccounts->map->only(self::GIFT_FIELDS)->values()->all(),
            ],
        ]);

        $this->prune($invitation);

        return $version;
    }

    /** Called before every editor write; at most one auto snapshot per interval. */
    public function autoSnapshot(Invitation $invitation, ?User $user = null): ?InvitationVersion
    {
        $recent = $invitation->versions()
            ->where('created_at', '>=', now()->subMinutes(self::AUTO_INTERVAL_MINUTES))
            ->exists();

        return $recent ? null : $this->snapshot($invitation, 'auto', $user);
    }

    public function restore(InvitationVersion $version, ?User $user = null): void
    {
        $invitation = $version->invitation;

        DB::transaction(function () use ($version, $invitation, $user) {
            $this->snapshot($invitation, 'before_restore', $user);

            $data = $version->snapshot;
            $fields = array_intersect_key($data['invitation'] ?? [], array_flip(self::INVITATION_FIELDS));

            // A theme deactivated since the snapshot keeps the current theme.
            if (! Theme::query()->whereKey($fields['theme_id'] ?? 0)->where('is_active', true)->exists()) {
                unset($fields['theme_id'], $fields['theme_variant']);
            }

            $invitation->forceFill($fields)->save();

            // Rows are deleted one by one so model events (admin audit) fire.
            // Their files are kept: an older snapshot may still reference them.

            $invitation->events()->get()->each->delete();
            foreach ($data['events'] ?? [] as $event) {
                $invitation->events()->create(array_intersect_key($event, array_flip(self::EVENT_FIELDS)));
            }

            $invitation->stories()->get()->each->delete();
            foreach ($data['stories'] ?? [] as $story) {
                $story = array_intersect_key($story, array_flip(self::STORY_FIELDS));
                $story['photo_path'] = $this->existingFile($story['photo_path'] ?? null);
                $invitation->stories()->create($story);
            }

            $invitation->giftAccounts()->get()->each->delete();
            foreach ($data['gifts'] ?? [] as $gift) {
                $path = $this->existingFile($gift['qris_image_path'] ?? null);
                $account = $invitation->giftAccounts()->make(array_intersect_key($gift, array_flip(array_diff(self::GIFT_FIELDS, ['qris_image_path']))));
                $account->forceFill(['qris_image_path' => $path])->save();
            }
        });

        $invitation->unsetRelations();
    }

    private function existingFile(?string $path): ?string
    {
        return $path && Storage::disk(MediaService::DISK)->exists($path) ? $path : null;
    }

    private function prune(Invitation $invitation): void
    {
        $keep = $invitation->versions()->take(self::MAX_VERSIONS)->pluck('id');
        $invitation->versions()->whereNotIn('id', $keep)->delete();
    }
}
