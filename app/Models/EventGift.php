<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventGift extends Model
{
    public const KINDS = ['amplop' => 'Amplop', 'kado' => 'Kado', 'transfer' => 'Transfer'];

    protected $fillable = ['giver_name', 'kind', 'amount', 'note'];

    protected function casts(): array
    {
        return ['amount' => 'integer'];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function kindLabel(): string
    {
        return self::KINDS[$this->kind] ?? $this->kind;
    }
}
