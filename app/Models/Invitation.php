<?php

namespace App\Models;

use App\Enums\InvitationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Status, ownership, slug, theme and activation dates are NOT mass assignable.
 * They change only through InvitationService (and PaymentService in M4).
 */
class Invitation extends Model
{
    use HasFactory, SoftDeletes;

    public const SECTIONS = [
        'countdown' => 'Hitung mundur',
        'story' => 'Cerita cinta',
        'gallery' => 'Galeri foto',
        'rsvp' => 'Konfirmasi kehadiran (RSVP)',
        'wishes' => 'Ucapan & doa',
        'gift' => 'Amplop digital',
    ];

    protected $fillable = [
        'groom_nickname', 'groom_fullname', 'groom_father', 'groom_mother', 'groom_child_order', 'groom_instagram',
        'bride_nickname', 'bride_fullname', 'bride_father', 'bride_mother', 'bride_child_order', 'bride_instagram',
        'opening_text', 'quote_text', 'quote_source', 'closing_text',
        'sections', 'rsvp_max_pax', 'wa_message_template',
    ];

    /** Receptionist secrets never leave the model in arrays or JSON. */
    protected $hidden = ['checkin_token', 'checkin_pin'];

    protected function casts(): array
    {
        return [
            'status' => InvitationStatus::class,
            'sections' => 'array',
            'customization' => 'array',
            'event_day_unlocked_at' => 'datetime',
            'checkin_pin' => 'encrypted',
            'checkin_selfie' => 'boolean',
            'rsvp_max_pax' => 'integer',
            'active_until' => 'datetime',
            'expiry_reminded_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Invitation $invitation): void {
            $invitation->uuid ??= (string) Str::uuid();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    public function musicTrack(): BelongsTo
    {
        return $this->belongsTo(MusicTrack::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(InvitationEvent::class)->orderBy('sort_order')->orderBy('date')->orderBy('start_time');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(InvitationPhoto::class)->orderBy('sort_order')->orderBy('id');
    }

    public function stories(): HasMany
    {
        return $this->hasMany(InvitationStory::class)->orderBy('sort_order')->orderBy('id');
    }

    public function giftAccounts(): HasMany
    {
        return $this->hasMany(GiftAccount::class)->orderBy('sort_order')->orderBy('id');
    }

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    public function wishes(): HasMany
    {
        return $this->hasMany(Wish::class);
    }

    public function eventGifts(): HasMany
    {
        return $this->hasMany(EventGift::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(InvitationVersion::class)->latest('id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function isActive(): bool
    {
        return $this->status === InvitationStatus::Active
            && ($this->active_until === null || $this->active_until->isFuture());
    }

    public function sectionEnabled(string $key): bool
    {
        return (bool) ($this->sections[$key] ?? true);
    }

    public function coupleName(): string
    {
        $groom = $this->groom_nickname ?: 'Mempelai Pria';
        $bride = $this->bride_nickname ?: 'Mempelai Wanita';

        return "{$groom} & {$bride}";
    }

    public function publicUrl(): string
    {
        $url = parse_url((string) config('app.url'));
        $scheme = $url['scheme'] ?? 'https';
        $port = isset($url['port']) ? ':'.$url['port'] : '';

        return "{$scheme}://{$this->slug}.".config('app.domain').$port;
    }

    public function mediaUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }

    public function musicUrl(): ?string
    {
        if ($this->custom_music_path) {
            return $this->mediaUrl($this->custom_music_path);
        }

        return $this->musicTrack?->is_active ? $this->musicTrack->url() : null;
    }

    /** Directory on the public disk holding this invitation's uploads. */
    public function mediaDirectory(): string
    {
        return "invitations/{$this->uuid}";
    }
}
