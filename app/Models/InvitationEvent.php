<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvitationEvent extends Model
{
    public const TIMEZONES = ['WIB' => 'Asia/Jakarta', 'WITA' => 'Asia/Makassar', 'WIT' => 'Asia/Jayapura'];

    protected $fillable = ['name', 'date', 'start_time', 'end_time', 'timezone', 'venue_name', 'address', 'maps_url', 'sort_order'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'sort_order' => 'integer',
        ];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function startsAt(): CarbonImmutable
    {
        return $this->at($this->start_time);
    }

    public function endsAt(): ?CarbonImmutable
    {
        return $this->end_time ? $this->at($this->end_time) : null;
    }

    public function timeLabel(): string
    {
        $end = $this->end_time ? substr($this->end_time, 0, 5) : 'Selesai';

        return substr($this->start_time, 0, 5).' - '.$end.' '.$this->timezone;
    }

    /** Google Calendar "add event" link (no API key needed). */
    public function googleCalendarUrl(string $title): string
    {
        $start = $this->startsAt()->utc();
        $end = ($this->endsAt() ?? $this->startsAt()->addHours(2))->utc();

        return 'https://calendar.google.com/calendar/render?'.http_build_query([
            'action' => 'TEMPLATE',
            'text' => $title.' - '.$this->name,
            'dates' => $start->format('Ymd\THis\Z').'/'.$end->format('Ymd\THis\Z'),
            'location' => trim($this->venue_name.', '.$this->address, ', '),
        ]);
    }

    private function at(string $time): CarbonImmutable
    {
        $tz = self::TIMEZONES[$this->timezone] ?? 'Asia/Jakarta';

        return CarbonImmutable::parse($this->date->format('Y-m-d').' '.substr($time, 0, 5), $tz);
    }
}
