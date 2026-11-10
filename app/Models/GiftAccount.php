<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class GiftAccount extends Model
{
    public const TYPES = ['bank' => 'Rekening bank', 'ewallet' => 'E-wallet', 'qris' => 'QRIS'];

    protected $fillable = ['type', 'provider', 'account_number', 'account_name', 'sort_order'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function isQris(): bool
    {
        return $this->type === 'qris';
    }

    public function qrisUrl(): ?string
    {
        return $this->qris_image_path ? Storage::disk('public')->url($this->qris_image_path) : null;
    }
}
