<?php

namespace Tests\Feature\EventDay;

use App\Livewire\Reception\CheckinApp;
use App\Models\Invitation;
use App\Services\EventDayService;
use App\Services\GuestService;
use App\Services\ReceptionAccess;
use Database\Seeders\ThemeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReceptionGuestListTest extends TestCase
{
    use RefreshDatabase;

    private Invitation $invitation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ThemeSeeder::class);
        $this->invitation = Invitation::factory()->active()->create();
        app(EventDayService::class)->enable($this->invitation);
        $this->invitation->refresh();
        app(ReceptionAccess::class)->grant($this->invitation);
    }

    public function test_receptionist_browses_and_picks_guests_without_typing(): void
    {
        $guests = app(GuestService::class);
        $joko = $guests->create($this->invitation, ['name' => 'Pak Joko', 'group_name' => 'Kantor']);
        $guests->create($this->invitation, ['name' => 'Bu Rina', 'group_name' => 'Keluarga']);
        $arrived = $guests->create($this->invitation, ['name' => 'Om Budi', 'group_name' => 'Keluarga']);
        app(EventDayService::class)->checkIn($this->invitation, $arrived, 1);

        $app = Livewire::test(CheckinApp::class, ['invitation' => $this->invitation])
            ->set('mode', 'search')
            ->assertSee(['Pak Joko', 'Bu Rina'])
            ->assertDontSee('Om Budi')
            ->assertSee('2 tamu');

        $app->set('status', 'arrived')->assertSee('Om Budi')->assertDontSee('Pak Joko');
        $app->set('status', 'all')->set('group', 'Keluarga')->assertSee(['Bu Rina', 'Om Budi'])->assertDontSee('Pak Joko');

        $app->set('group', '')->set('status', 'pending')
            ->call('checkIn', $joko->id, 3)
            ->assertSet('result.name', 'Pak Joko')
            ->assertSet('result.pax', 3)
            ->assertDontSee('Check-in berhasil Pak Joko Kantor');
        $this->assertNotNull($joko->refresh()->checked_in_at);
    }

    public function test_long_lists_load_in_pages(): void
    {
        app(GuestService::class)->bulkCreate($this->invitation, collect(range(1, 60))->map(fn ($i) => ['name' => sprintf('Tamu %03d', $i), 'phone' => null])->all());

        Livewire::test(CheckinApp::class, ['invitation' => $this->invitation])
            ->set('mode', 'search')
            ->assertSee('Tamu 050')
            ->assertDontSee('Tamu 051')
            ->assertSee('Muat lebih banyak (10 lagi)')
            ->call('loadMore')
            ->assertSee('Tamu 060');
    }
}
