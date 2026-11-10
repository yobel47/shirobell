<?php

namespace App\Services;

use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class GuestService
{
    /**
     * Parse "one guest per line" input. A line may end with a phone number,
     * separated by "-", "," or ";" (e.g. "Pak Joko - 0812...").
     *
     * @return array<int, array{name: string, phone: ?string}>
     */
    public function parseLines(string $text): array
    {
        $guests = [];

        foreach (preg_split('/\r\n|\r|\n/', $text) as $line) {
            $line = trim(strip_tags($line));
            if ($line === '') {
                continue;
            }

            $phone = null;
            if (preg_match('/^(.*?)[\s]*[-,;][\s]*(\+?[0-9][0-9\s-]{7,18})$/u', $line, $m)) {
                $line = trim($m[1]);
                $phone = $this->normalizePhone($m[2]);
            }

            if ($line !== '') {
                $guests[] = ['name' => Str::limit($line, 100, ''), 'phone' => $phone];
            }
        }

        return $guests;
    }

    /**
     * @param  array<int, array{name: string, phone: ?string}>  $rows
     */
    public function bulkCreate(Invitation $invitation, array $rows, ?string $group = null): int
    {
        $max = (int) config('shirobell.invitation.max_guests', 1000);
        $remaining = $max - $invitation->guests()->count();

        if (count($rows) > $remaining) {
            throw ValidationException::withMessages([
                'bulk' => $remaining > 0
                    ? "Sisa kuota tamu hanya {$remaining}. Kurangi jumlah nama."
                    : "Daftar tamu sudah mencapai batas {$max}.",
            ]);
        }

        DB::transaction(function () use ($invitation, $rows, $group) {
            foreach ($rows as $row) {
                $this->create($invitation, ['name' => $row['name'], 'phone' => $row['phone'], 'group_name' => $group]);
            }
        });

        return count($rows);
    }

    /** @param array{name: string, phone?: ?string, group_name?: ?string} $data */
    public function create(Invitation $invitation, array $data): Guest
    {
        $guest = new Guest($data);
        $guest->invitation()->associate($invitation);
        $guest->code = $this->uniqueCode($invitation);
        $guest->save();

        return $guest;
    }

    public function personalUrl(Invitation $invitation, Guest $guest): string
    {
        return $invitation->publicUrl().'/?'.http_build_query(['to' => $guest->name, 'g' => $guest->code]);
    }

    public function whatsappUrl(Invitation $invitation, Guest $guest): string
    {
        $message = strtr($invitation->wa_message_template ?: InvitationService::DEFAULT_WA_TEMPLATE, [
            '{nama_tamu}' => $guest->name,
            '{link}' => $this->personalUrl($invitation, $guest),
            '{nama_pasangan}' => $invitation->coupleName(),
        ]);

        $phone = $guest->phone ? $this->toWhatsappNumber($guest->phone) : '';

        return 'https://wa.me/'.$phone.'?text='.rawurlencode($message);
    }

    /** Resolve a personal-link code; null when missing or not this invitation's. */
    public function findByCode(Invitation $invitation, ?string $code): ?Guest
    {
        if (! $code || ! preg_match('/^[a-z0-9]{6,12}$/', $code)) {
            return null;
        }

        return $invitation->guests()->where('code', $code)->first();
    }

    public function markOpened(Guest $guest): void
    {
        if ($guest->opened_at === null) {
            $guest->forceFill(['opened_at' => now()])->save();
        }
    }

    public function normalizePhone(?string $phone): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $phone);

        return $digits === '' ? null : Str::limit($digits, 20, '');
    }

    private function toWhatsappNumber(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        return str_starts_with($digits, '0') ? '62'.substr($digits, 1) : $digits;
    }

    private function uniqueCode(Invitation $invitation): string
    {
        do {
            $code = Str::lower(Str::random(8));
        } while (! ctype_alnum($code) || $invitation->guests()->where('code', $code)->exists());

        return $code;
    }
}
