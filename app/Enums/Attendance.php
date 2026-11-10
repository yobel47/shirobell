<?php

namespace App\Enums;

enum Attendance: string
{
    case Yes = 'yes';
    case No = 'no';
    case Maybe = 'maybe';

    public function label(): string
    {
        return match ($this) {
            self::Yes => 'Hadir',
            self::No => 'Tidak hadir',
            self::Maybe => 'Masih ragu',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Yes => 'bg-emerald-50 text-emerald-700',
            self::No => 'bg-gray-100 text-gray-600',
            self::Maybe => 'bg-amber-50 text-amber-700',
        };
    }
}
