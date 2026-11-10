<?php

namespace App\Services;

use App\Enums\InvitationStatus;
use App\Models\GiftAccount;
use App\Models\Invitation;
use App\Models\InvitationEvent;
use App\Models\InvitationStory;
use App\Models\Theme;
use App\Models\Wish;
use Illuminate\Support\Collection;

/**
 * Builds an unsaved sample invitation so every theme can be previewed at
 * /tema/{slug} without demo rows in the database.
 */
class ThemeDemoService
{
    public function __construct(private readonly InvitationRenderer $renderer) {}

    /** @return array<string, mixed> view data for the theme */
    public function data(Theme $theme, ?string $variant = null): array
    {
        $invitation = $this->invitation($theme);
        $invitation->theme_variant = app(ThemeRegistry::class)->hasVariant($theme->slug, $variant) ? $variant : null;
        $data = $this->renderer->data($invitation, 'Bapak/Ibu Tamu Undangan');

        return array_merge($data, [
            'demo' => true,
            'demoWishes' => $this->wishes(),
        ]);
    }

    private function invitation(Theme $theme): Invitation
    {
        $invitation = new Invitation([
            'groom_nickname' => 'Raka', 'groom_fullname' => 'Raka Pratama, S.T.', 'groom_child_order' => 'Putra pertama dari',
            'groom_father' => 'Bapak Hendra Wijaya', 'groom_mother' => 'Ibu Sari Lestari', 'groom_instagram' => null,
            'bride_nickname' => 'Nadia', 'bride_fullname' => 'Nadia Putri, S.Ds.', 'bride_child_order' => 'Putri kedua dari',
            'bride_father' => 'Bapak Agus Santoso', 'bride_mother' => 'Ibu Rina Marlina', 'bride_instagram' => null,
            'opening_text' => InvitationService::DEFAULT_OPENING,
            'quote_text' => 'Dan di antara tanda-tanda kekuasaan-Nya ialah Dia menciptakan untukmu pasangan hidup dari jenismu sendiri, supaya kamu merasa tenteram kepadanya.',
            'quote_source' => 'QS. Ar-Rum: 21',
            'closing_text' => InvitationService::DEFAULT_CLOSING,
            'sections' => array_fill_keys(array_keys(Invitation::SECTIONS), true),
            'rsvp_max_pax' => 2,
        ]);
        $invitation->slug = 'contoh-'.$theme->slug;
        $invitation->status = InvitationStatus::Active;
        $invitation->setRelation('theme', $theme);
        $invitation->setRelation('musicTrack', null);
        $invitation->setRelation('photos', collect());
        $invitation->setRelation('events', $this->events());
        $invitation->setRelation('stories', $this->stories());
        $invitation->setRelation('giftAccounts', $this->gifts());

        return $invitation;
    }

    private function events(): Collection
    {
        $date = now()->addDays(60)->startOfDay();

        return collect([
            ['Akad Nikah', '08:00', '10:00', 'Masjid Agung Al-Ikhlas', 'Jl. Merdeka No. 10, Bandung'],
            ['Resepsi', '11:00', null, 'Gedung Serbaguna Melati', 'Jl. Asia Afrika No. 25, Bandung'],
        ])->map(function (array $row) use ($date) {
            $event = new InvitationEvent([
                'name' => $row[0], 'date' => $date, 'start_time' => $row[1], 'end_time' => $row[2],
                'timezone' => 'WIB', 'venue_name' => $row[3], 'address' => $row[4], 'maps_url' => 'https://maps.google.com',
            ]);

            return $event;
        });
    }

    private function stories(): Collection
    {
        return collect([
            ['Pertama Bertemu', 'Maret 2021', 'Kami bertemu di sebuah acara komunitas buku. Obrolan singkat tentang novel favorit berlanjut menjadi persahabatan.'],
            ['Lamaran', 'Desember 2025', 'Dengan restu kedua keluarga, kami memutuskan untuk melangkah ke jenjang yang lebih serius.'],
        ])->map(fn (array $row) => new InvitationStory(['title' => $row[0], 'story_date' => $row[1], 'body' => $row[2]]));
    }

    private function gifts(): Collection
    {
        return collect([
            new GiftAccount(['type' => 'bank', 'provider' => 'BCA', 'account_number' => '1234567890', 'account_name' => 'Raka Pratama']),
            new GiftAccount(['type' => 'ewallet', 'provider' => 'DANA', 'account_number' => '081234567890', 'account_name' => 'Nadia Putri']),
        ]);
    }

    private function wishes(): Collection
    {
        return collect([
            ['Dimas & Keluarga', 'yes', 'Selamat menempuh hidup baru! Semoga menjadi keluarga yang sakinah, mawaddah, warahmah.', 2],
            ['Tante Wulan', 'maybe', 'Bahagia selalu untuk kalian berdua. Semoga lancar sampai hari H.', 5],
            ['Teman Kantor', 'no', 'Mohon maaf belum bisa hadir. Doa terbaik untuk kalian.', 26],
        ])->map(function (array $row, int $i) {
            $wish = new Wish(['name' => $row[0], 'attendance' => $row[1], 'message' => $row[2]]);
            $wish->id = $i + 1;
            $wish->created_at = now()->subHours($row[3]);

            return $wish;
        });
    }
}
