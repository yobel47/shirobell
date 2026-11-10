<?php

namespace Tests\Feature\EventDay;

use App\Livewire\Invitation\EventDayManager;
use App\Livewire\Invitation\GuestManager;
use App\Livewire\Reception\CheckinApp;
use App\Livewire\Reception\WelcomeScreen;
use App\Models\EventGift;
use App\Models\Guest;
use App\Models\Invitation;
use App\Models\User;
use App\Services\EventDayService;
use App\Services\GuestService;
use App\Services\ReceptionAccess;
use Database\Seeders\ThemeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EventDayStageBTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private Invitation $invitation;

    private Guest $guest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ThemeSeeder::class);
        $this->owner = User::factory()->create();
        $this->invitation = Invitation::factory()->active()->for($this->owner)->create();
        $this->guest = app(GuestService::class)->create($this->invitation, ['name' => 'Pak Joko', 'group_name' => 'Kantor', 'table_number' => '7']);
        app(EventDayService::class)->enable($this->invitation);
        $this->invitation->refresh();
        app(ReceptionAccess::class)->grant($this->invitation);
    }

    public function test_owner_sets_table_number_from_guest_list(): void
    {
        Livewire::actingAs($this->owner)
            ->test(GuestManager::class, ['invitation' => $this->invitation])
            ->call('edit', $this->guest->id)
            ->set('form.table_number', 'VIP 2')
            ->call('update')
            ->assertHasNoErrors()
            ->assertSee('Meja VIP 2');

        $this->assertSame('VIP 2', $this->guest->refresh()->table_number);
    }

    public function test_receptionist_sees_table_gives_souvenir_and_records_gift(): void
    {
        Livewire::test(CheckinApp::class, ['invitation' => $this->invitation])
            ->call('scan', $this->guest->code)
            ->assertSee('Meja 7')
            ->call('toggleSouvenir', $this->guest->id)
            ->assertSet('result.souvenir', true)
            ->set('giftKind', 'amplop')
            ->set('giftAmount', 500000)
            ->set('giftNote', 'Amplop no. 12')
            ->call('recordGift')
            ->assertHasNoErrors()
            ->assertSet('result.gifts', 1)
            ->set('giftKind', 'kado')
            ->call('recordGift')
            ->assertSet('result.gifts', 2);

        $this->assertNotNull($this->guest->refresh()->souvenir_at);
        $gift = EventGift::where('kind', 'amplop')->sole();
        $this->assertSame(['Pak Joko', 500000, 'Amplop no. 12', $this->guest->id], [$gift->giver_name, $gift->amount, $gift->note, $gift->guest_id]);
    }

    public function test_gift_validation_and_scope(): void
    {
        Livewire::test(CheckinApp::class, ['invitation' => $this->invitation])
            ->call('recordGift')
            ->assertNotFound();

        Livewire::test(CheckinApp::class, ['invitation' => $this->invitation])
            ->call('scan', $this->guest->code)
            ->set('giftKind', 'emas')
            ->call('recordGift')
            ->assertHasErrors('giftKind');

        $foreign = app(GuestService::class)->create(Invitation::factory()->active()->create(), ['name' => 'Lain']);
        Livewire::test(CheckinApp::class, ['invitation' => $this->invitation])
            ->call('toggleSouvenir', $foreign->id)
            ->assertNotFound();
    }

    public function test_recap_counts_portions_gifts_and_souvenirs(): void
    {
        $service = app(EventDayService::class);
        // The guest answered twice (the latest, 3, counts), plus an anonymous yes (2) and a no.
        $this->invitation->wishes()->make(['name' => 'Pak Joko', 'attendance' => 'yes', 'pax' => 1])->forceFill(['guest_id' => $this->guest->id])->save();
        $this->invitation->wishes()->make(['name' => 'Pak Joko', 'attendance' => 'yes', 'pax' => 3])->forceFill(['guest_id' => $this->guest->id])->save();
        $this->invitation->wishes()->create(['name' => 'Anonim', 'attendance' => 'yes', 'pax' => 2]);
        $this->invitation->wishes()->create(['name' => 'Tidak', 'attendance' => 'no', 'pax' => null]);

        $service->checkIn($this->invitation, $this->guest, 4);
        $service->toggleSouvenir($this->invitation, $this->guest);
        $service->recordGift($this->invitation, $this->guest, ['giver_name' => 'Pak Joko', 'kind' => 'amplop', 'amount' => 300000]);
        $service->recordGift($this->invitation, null, ['giver_name' => 'Bu Rina', 'kind' => 'amplop', 'amount' => 200000]);
        $service->recordGift($this->invitation, null, ['giver_name' => 'Tim', 'kind' => 'kado']);

        $recap = $service->recap($this->invitation);
        $this->assertSame(5, $recap['expected_pax']);
        $this->assertSame(4, $recap['arrived_pax']);
        $this->assertSame(1, $recap['souvenirs']);
        $this->assertSame(['count' => 2, 'amount' => 500000], $recap['gifts']['amplop']);
        $this->assertSame(['count' => 1, 'amount' => 0], $recap['gifts']['kado']);

        Livewire::actingAs($this->owner)
            ->test(EventDayManager::class, ['invitation' => $this->invitation])
            ->assertSee('Rekap hari-H')
            ->assertSee('Rp 500.000')
            ->call('deleteGift', EventGift::where('giver_name', 'Tim')->value('id'));
        $this->assertSame(2, EventGift::count());
    }

    public function test_exports_attendance_and_gifts_csv_safely(): void
    {
        app(EventDayService::class)->checkIn($this->invitation, $this->guest, 2);
        app(EventDayService::class)->recordGift($this->invitation, null, ['giver_name' => '=HYPERLINK(1)', 'kind' => 'kado']);

        $attendance = $this->actingAs($this->owner)->get(route('app.invitations.event-day.export', [$this->invitation, 'jenis' => 'kehadiran']));
        $attendance->assertOk();
        $this->assertStringContainsString('"Pak Joko",Kantor,7,Ya', $attendance->streamedContent());

        $gifts = $this->actingAs($this->owner)->get(route('app.invitations.event-day.export', [$this->invitation, 'jenis' => 'hadiah']))->streamedContent();
        $this->assertStringContainsString("'=HYPERLINK(1)", $gifts);

        $this->actingAs(User::factory()->create())->get(route('app.invitations.event-day.export', $this->invitation))->assertForbidden();
    }

    public function test_welcome_screen_shows_table_number(): void
    {
        app(EventDayService::class)->checkIn($this->invitation, $this->guest, 1);

        Livewire::test(WelcomeScreen::class, ['invitation' => $this->invitation])->assertSee('Meja 7');
    }
}
