<?php

namespace App\Models;

use App\Enums\Attendance;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Written only by WishService; ip_hash and is_hidden are never mass assignable. */
class Wish extends Model
{
    protected $fillable = ['name', 'attendance', 'pax', 'message'];

    protected function casts(): array
    {
        return [
            'attendance' => Attendance::class,
            'pax' => 'integer',
            'is_hidden' => 'boolean',
        ];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function scopeVisibleMessages(Builder $query): Builder
    {
        return $query->where('is_hidden', false)->whereNotNull('message')->where('message', '!=', '');
    }
}
