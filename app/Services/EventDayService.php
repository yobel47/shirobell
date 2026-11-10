<?php

namespace App\Services;

use App\Models\EventGift;
use App\Models\Guest;
use App\Models\Invitation;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Event-day module (ADR 011): receptionist check-in by QR or name, walk-in
 * guests, welcome screen feed and guest QR tickets.
 */
class EventDayService
{
    public const MAX_PAX = 20;

    public const SELFIE_MAX_PX = 1080;

    public function __construct(
        private readonly SettingService $settings,
        private readonly GuestService $guests,
        private readonly MediaService $media,
    ) {}

    public function isAddonMode(): bool
    {
        return $this->settings->get('event_day_mode') === 'addon';
    }

    public function addonPrice(): int
    {
        return $this->settings->int('event_day_price');
    }

    /** Owner may use the module: invitation active and, in addon mode, add-on paid. */
    public function isUnlocked(Invitation $invitation): bool
    {
        return ! $this->isAddonMode() || $invitation->event_day_unlocked_at !== null;
    }

    public function isAvailable(Invitation $invitation): bool
    {
        return $invitation->isActive() && $this->isUnlocked($invitation);
    }

    /** Receptionist link works: available and check-in enabled by the owner. */
    public function isOpen(Invitation $invitation): bool
    {
        return $invitation->checkin_token !== null && $this->isAvailable($invitation);
    }

    public function enable(Invitation $invitation): void
    {
        if (! $this->isAvailable($invitation)) {
            throw ValidationException::withMessages(['event_day' => 'Fitur hari-H belum tersedia untuk undangan ini.']);
        }

        if ($invitation->checkin_token === null) {
            $invitation->forceFill(['checkin_token' => $this->newToken(), 'checkin_pin' => $this->newPin()])->save();
        }
    }

    /** New link and PIN: every receptionist device must log in again. */
    public function resetAccess(Invitation $invitation): void
    {
        $invitation->forceFill(['checkin_token' => $this->newToken(), 'checkin_pin' => $this->newPin()])->save();
    }

    public function disable(Invitation $invitation): void
    {
        $invitation->forceFill(['checkin_token' => null, 'checkin_pin' => null])->save();
    }

    public function findOpenByToken(string $token): ?Invitation
    {
        if (! preg_match('/^[A-Za-z0-9]{40}$/', $token)) {
            return null;
        }

        $invitation = Invitation::query()->where('checkin_token', $token)->first();

        return $invitation && $this->isOpen($invitation) ? $invitation : null;
    }

    public function pinMatches(Invitation $invitation, string $pin): bool
    {
        return $invitation->checkin_pin !== null && hash_equals((string) $invitation->checkin_pin, trim($pin));
    }

    /**
     * Idempotent: a guest already checked in keeps the first time.
     *
     * @return array{guest: Guest, already: bool}
     */
    public function checkIn(Invitation $invitation, Guest $guest, int $pax = 1): array
    {
        abort_unless($guest->invitation_id === $invitation->id, 404);

        return DB::transaction(function () use ($guest, $pax) {
            $locked = Guest::query()->whereKey($guest->id)->lockForUpdate()->first();

            if ($locked->checked_in_at !== null) {
                return ['guest' => $locked, 'already' => true];
            }

            $locked->forceFill([
                'checked_in_at' => now(),
                'checked_in_pax' => max(1, min(self::MAX_PAX, $pax)),
            ])->save();

            return ['guest' => $locked, 'already' => false];
        });
    }

    public function updatePax(Invitation $invitation, Guest $guest, int $pax): void
    {
        abort_unless($guest->invitation_id === $invitation->id && $guest->checked_in_at !== null, 404);
        $guest->forceFill(['checked_in_pax' => max(1, min(self::MAX_PAX, $pax))])->save();
    }

    public function undoCheckIn(Invitation $invitation, Guest $guest): void
    {
        abort_unless($guest->invitation_id === $invitation->id, 404);
        $guest->forceFill(['checked_in_at' => null, 'checked_in_pax' => null])->save();
    }

    /** A guest not on the list, added and checked in at the door. */
    public function walkIn(Invitation $invitation, string $name, int $pax = 1): Guest
    {
        $guest = $this->guests->create($invitation, ['name' => mb_substr(trim($name), 0, 100)]);
        $guest->forceFill(['is_walk_in' => true])->save();

        return $this->checkIn($invitation, $guest, $pax)['guest'];
    }

    /** Pax suggested at the door: the guest's own RSVP, otherwise 1. */
    public function suggestedPax(Guest $guest): int
    {
        return max(1, (int) ($guest->rsvp?->pax ?? 1));
    }

    /** @return array{guests: int, checked_in: int, pax: int, walk_in: int} */
    public function stats(Invitation $invitation): array
    {
        $row = $invitation->guests()->toBase()
            ->selectRaw('count(*) as guests')
            ->selectRaw('sum(case when checked_in_at is not null then 1 else 0 end) as checked_in')
            ->selectRaw('coalesce(sum(checked_in_pax), 0) as pax')
            ->selectRaw('sum(case when is_walk_in = 1 then 1 else 0 end) as walk_in')
            ->first();

        return [
            'guests' => (int) $row->guests,
            'checked_in' => (int) $row->checked_in,
            'pax' => (int) $row->pax,
            'walk_in' => (int) $row->walk_in,
        ];
    }

    /** @return Collection<int, Guest> latest arrivals first */
    public function recentArrivals(Invitation $invitation, int $limit = 6): Collection
    {
        return $invitation->guests()->whereNotNull('checked_in_at')
            ->latest('checked_in_at')->latest('id')->take($limit)
            ->get(['id', 'name', 'group_name', 'table_number', 'checked_in_at', 'checked_in_pax', 'selfie_path']);
    }

    public function toggleSouvenir(Invitation $invitation, Guest $guest): Guest
    {
        abort_unless($guest->invitation_id === $invitation->id, 404);
        $guest->forceFill(['souvenir_at' => $guest->souvenir_at ? null : now()])->save();

        return $guest;
    }

    /** Stores (or replaces) the guest's check-in photo as WebP. */
    public function setSelfie(Invitation $invitation, Guest $guest, UploadedFile $photo): Guest
    {
        abort_unless($guest->invitation_id === $invitation->id && $invitation->checkin_selfie, 404);

        $old = $guest->selfie_path;
        $path = $this->media->storeImage($photo, $invitation->mediaDirectory().'/selfies', self::SELFIE_MAX_PX, 80);
        $guest->forceFill(['selfie_path' => $path])->save();
        $this->media->delete($old);

        return $guest;
    }

    public function deleteSelfie(Invitation $invitation, Guest $guest): void
    {
        abort_unless($guest->invitation_id === $invitation->id, 404);
        $this->media->delete($guest->selfie_path);
        $guest->forceFill(['selfie_path' => null])->save();
    }

    /** @param array{giver_name: string, kind: string, amount?: ?int, note?: ?string} $data */
    public function recordGift(Invitation $invitation, ?Guest $guest, array $data): EventGift
    {
        abort_if($guest && $guest->invitation_id !== $invitation->id, 404);

        $gift = $invitation->eventGifts()->make([
            'giver_name' => mb_substr(trim($data['giver_name']), 0, 100),
            'kind' => array_key_exists($data['kind'], EventGift::KINDS) ? $data['kind'] : 'amplop',
            'amount' => isset($data['amount']) && $data['amount'] > 0 ? (int) $data['amount'] : null,
            'note' => filled($data['note'] ?? null) ? mb_substr(trim($data['note']), 0, 200) : null,
        ]);
        $gift->guest()->associate($guest);
        $gift->save();

        return $gift;
    }

    /**
     * Gifts, souvenirs and catering numbers for the owner's recap.
     *
     * @return array{souvenirs: int, gifts: array<string, array{count: int, amount: int}>, expected_pax: int, arrived_pax: int}
     */
    public function recap(Invitation $invitation): array
    {
        $gifts = collect(array_keys(EventGift::KINDS))->mapWithKeys(fn ($kind) => [$kind => ['count' => 0, 'amount' => 0]])->all();
        foreach ($invitation->eventGifts()->toBase()->selectRaw('kind, count(*) as n, coalesce(sum(amount), 0) as total')->groupBy('kind')->get() as $row) {
            $gifts[$row->kind] = ['count' => (int) $row->n, 'amount' => (int) $row->total];
        }

        // Expected portions: latest "yes" RSVP per guest link, plus anonymous "yes" RSVPs.
        $yes = $invitation->wishes()->where('attendance', 'yes')->latest('id')->get(['id', 'guest_id', 'pax']);
        $expected = $yes->whereNotNull('guest_id')->unique('guest_id')->sum(fn ($w) => max(1, (int) $w->pax))
            + $yes->whereNull('guest_id')->sum(fn ($w) => max(1, (int) $w->pax));

        return [
            'souvenirs' => $invitation->guests()->whereNotNull('souvenir_at')->count(),
            'gifts' => $gifts,
            'expected_pax' => (int) $expected,
            'arrived_pax' => (int) $invitation->guests()->sum('checked_in_pax'),
        ];
    }

    /** QR payload for a guest ticket: the guest's personal code. */
    public function qrSvg(string $payload, int $size = 220): string
    {
        $writer = new Writer(new ImageRenderer(new RendererStyle($size, 1), new SvgImageBackEnd));

        // Drop the XML prolog so the SVG can be inlined in HTML.
        return trim(preg_replace('/^<\?xml[^>]*\?>/', '', $writer->writeString($payload)));
    }

    private function newToken(): string
    {
        do {
            $token = Str::random(40);
        } while (Invitation::query()->where('checkin_token', $token)->exists());

        return $token;
    }

    private function newPin(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
