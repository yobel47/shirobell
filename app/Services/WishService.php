<?php

namespace App\Services;

use App\Enums\Attendance;
use App\Models\Guest;
use App\Models\Invitation;
use App\Models\Wish;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * Guest RSVP and wishes. Only active invitations accept submissions.
 * A guest arriving through a personal link updates one RSVP record instead
 * of creating duplicates; anonymous visitors create a new record each time.
 */
class WishService
{
    /**
     * @param  array{name: string, attendance: ?string, pax: ?int, message: ?string}  $data  validated
     */
    public function submit(Invitation $invitation, ?Guest $guest, array $data, string $ip): Wish
    {
        if (! $invitation->isActive()) {
            throw ValidationException::withMessages(['form' => 'Form ini aktif setelah undangan diterbitkan.']);
        }

        $key = 'wish:'.$invitation->id.':'.$this->hashIp($ip);
        $limit = (int) config('shirobell.invitation.wish_rate_limit', 5);

        if (RateLimiter::tooManyAttempts($key, $limit)) {
            $minutes = (int) ceil(RateLimiter::availableIn($key) / 60);
            throw ValidationException::withMessages(['form' => "Terlalu banyak kiriman. Coba lagi dalam {$minutes} menit."]);
        }
        RateLimiter::hit($key, 600);

        $attendance = $invitation->sectionEnabled('rsvp') ? ($data['attendance'] ?? null) : null;
        $values = [
            'name' => $data['name'],
            'attendance' => $attendance,
            'pax' => $attendance === Attendance::Yes->value ? min((int) ($data['pax'] ?? 1), $invitation->rsvp_max_pax) : null,
            'message' => $invitation->sectionEnabled('wishes') ? ($data['message'] ?? null) : null,
        ];

        $wish = $guest
            ? $invitation->wishes()->firstOrNew(['guest_id' => $guest->id])
            : $invitation->wishes()->make();

        // Keep an earlier message if this submission only updates the RSVP.
        if ($wish->exists && blank($values['message'])) {
            unset($values['message']);
        }

        $wish->fill($values);
        $wish->guest()->associate($guest);
        $wish->ip_hash = $this->hashIp($ip);
        $wish->save();

        return $wish;
    }

    /** @return array{yes: int, yes_pax: int, no: int, maybe: int, messages: int} */
    public function stats(Invitation $invitation): array
    {
        $rows = $invitation->wishes()
            ->selectRaw('attendance, COUNT(*) as total, COALESCE(SUM(pax), 0) as pax')
            ->whereNotNull('attendance')
            ->groupBy('attendance')
            ->get()
            ->keyBy(fn ($row) => $row->attendance?->value);

        return [
            'yes' => (int) ($rows['yes']->total ?? 0),
            'yes_pax' => (int) ($rows['yes']->pax ?? 0),
            'no' => (int) ($rows['no']->total ?? 0),
            'maybe' => (int) ($rows['maybe']->total ?? 0),
            'messages' => $invitation->wishes()->whereNotNull('message')->where('message', '!=', '')->count(),
        ];
    }

    private function hashIp(string $ip): string
    {
        return hash_hmac('sha256', $ip, (string) config('app.key'));
    }
}
