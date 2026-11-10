<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromoCode extends Model
{
    use HasFactory;

    public const TYPES = ['fixed' => 'Potongan nominal (Rp)', 'percent' => 'Persen (%)'];

    protected $fillable = ['code', 'type', 'value', 'max_uses', 'starts_at', 'ends_at', 'is_active'];

    protected function casts(): array
    {
        return [
            'value' => 'integer',
            'max_uses' => 'integer',
            'used_count' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(fn (PromoCode $promo) => $promo->code = strtoupper(trim($promo->code)));
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function discountFor(int $amount): int
    {
        $discount = $this->type === 'percent'
            ? intdiv($amount * min($this->value, 100), 100)
            : $this->value;

        return max(0, min($discount, $amount));
    }

    public function label(): string
    {
        return $this->type === 'percent' ? "{$this->value}%" : 'Rp '.number_format($this->value, 0, ',', '.');
    }
}
