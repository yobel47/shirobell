<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Created only by CheckoutService; status changes only through PaymentService.
 * Nothing is mass assignable on purpose.
 */
class Order extends Model
{
    protected $guarded = ['*'];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'base_amount' => 'integer',
            'discount_amount' => 'integer',
            'total_amount' => 'integer',
            'duration_days' => 'integer',
            'expires_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class)->withTrashed();
    }

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(PaymentLog::class);
    }

    public function isPending(): bool
    {
        return $this->status === OrderStatus::Pending;
    }

    public function canContinuePayment(): bool
    {
        return $this->isPending() && $this->payment_url && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public const TYPES = [
        'new' => 'Penerbitan',
        'renewal' => 'Perpanjangan',
        'event_day' => 'Add-on Hari-H',
    ];

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
