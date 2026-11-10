<?php

namespace App\Enums;

enum InvitationStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Expired = 'expired';
    case Suspended = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Active => 'Aktif',
            self::Expired => 'Berakhir',
            self::Suspended => 'Ditangguhkan',
        };
    }

    /** Tailwind classes for the status badge. */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'bg-amber-50 text-amber-700',
            self::Active => 'bg-emerald-50 text-emerald-700',
            self::Expired => 'bg-gray-100 text-gray-600',
            self::Suspended => 'bg-red-50 text-red-700',
        };
    }
}
