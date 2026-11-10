<?php

namespace App\Services\Payment;

use App\Models\PromoCode;
use Illuminate\Validation\ValidationException;

class PromoService
{
    /** Find a usable promo code or throw a validation error on `promo`. */
    public function resolve(string $code): PromoCode
    {
        $code = strtoupper(trim($code));
        $promo = $code === '' ? null : PromoCode::query()->where('code', $code)->first();

        $error = match (true) {
            ! $promo, ! $promo->is_active => 'Kode promo tidak ditemukan.',
            $promo->starts_at && $promo->starts_at->isFuture() => 'Kode promo belum berlaku.',
            $promo->ends_at && $promo->ends_at->isPast() => 'Kode promo sudah berakhir.',
            $promo->max_uses !== null && $promo->used_count >= $promo->max_uses => 'Kuota kode promo sudah habis.',
            default => null,
        };

        if ($error) {
            throw ValidationException::withMessages(['promo' => $error]);
        }

        return $promo;
    }

    /** Counted when the order is paid, not at checkout. */
    public function markUsed(?int $promoId): void
    {
        if ($promoId) {
            PromoCode::query()->whereKey($promoId)->increment('used_count');
        }
    }
}
