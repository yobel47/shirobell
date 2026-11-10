<?php

namespace Tests\Feature\Invitation;

use App\Livewire\Invitation\Editor\GiftManager;
use App\Livewire\Invitation\GuestManager;
use App\Models\Invitation;
use App\Models\User;
use App\Services\GuestService;
use Database\Seeders\ThemeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class GuestAndGiftTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private Invitation $invitation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ThemeSeeder::class);
        Storage::fake('public');

        $this->owner = User::factory()->create();
        $this->invitation = Invitation::factory()->active()->for($this->owner)->create(['slug' => 'budi-ani']);
    }

    public function test_bulk_add_parses_names_and_phones(): void
    {
        Livewire::actingAs($this->owner)
            ->test(GuestManager::class, ['invitation' => $this->invitation])
            ->set('bulk', "Bapak Joko\nIbu Rina - 0812-3456-7890\n\n  Keluarga Hartono ; +62811111111  ")
            ->set('bulkGroup', 'Keluarga')
            ->call('addBulk')
            ->assertHasNoErrors();

        $guests = $this->invitation->guests()->orderBy('id')->get();
        $this->assertSame(['Bapak Joko', 'Ibu Rina', 'Keluarga Hartono'], $guests->pluck('name')->all());
        $this->assertSame([null, '081234567890', '62811111111'], $guests->pluck('phone')->all());
        $this->assertSame('Keluarga', $guests->first()->group_name);
        $this->assertCount(3, $guests->pluck('code')->unique());
    }

    public function test_guest_quota_is_enforced(): void
    {
        config(['shirobell.invitation.max_guests' => 2]);

        Livewire::actingAs($this->owner)
            ->test(GuestManager::class, ['invitation' => $this->invitation])
            ->set('bulk', "A1\nB2\nC3")
            ->call('addBulk')
            ->assertHasErrors('bulk');

        $this->assertSame(0, $this->invitation->guests()->count());
    }

    public function test_guest_page_is_owner_only(): void
    {
        $this->actingAs($this->owner)->get(route('app.invitations.guests', $this->invitation))->assertOk();
        $this->actingAs(User::factory()->create())->get(route('app.invitations.guests', $this->invitation))->assertForbidden();
    }

    public function test_foreign_guest_cannot_be_deleted(): void
    {
        $other = Invitation::factory()->create();
        $foreign = app(GuestService::class)->create($other, ['name' => 'Orang Lain']);

        Livewire::actingAs($this->owner)
            ->test(GuestManager::class, ['invitation' => $this->invitation])
            ->call('delete', $foreign->id)
            ->assertNotFound();

        $this->assertDatabaseHas('guests', ['id' => $foreign->id]);
    }

    public function test_whatsapp_link_uses_template_and_international_number(): void
    {
        $guest = app(GuestService::class)->create($this->invitation, ['name' => 'Pak Joko', 'phone' => '081234567890']);
        $this->invitation->fill(['wa_message_template' => 'Halo {nama_tamu}, buka {link} dari {nama_pasangan}'])->save();

        $url = app(GuestService::class)->whatsappUrl($this->invitation, $guest);

        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', $url);
        $text = rawurldecode(substr($url, strpos($url, '=') + 1));
        $this->assertStringContainsString('Halo Pak Joko', $text);
        $this->assertStringContainsString('g='.$guest->code, $text);
        $this->assertStringContainsString('Budi & Ani', $text);
    }

    public function test_mark_shared_sets_timestamp(): void
    {
        $guest = app(GuestService::class)->create($this->invitation, ['name' => 'Pak Joko']);

        Livewire::actingAs($this->owner)
            ->test(GuestManager::class, ['invitation' => $this->invitation])
            ->call('markShared', $guest->id);

        $this->assertNotNull($guest->refresh()->shared_at);
    }

    public function test_bank_account_and_qris_can_be_added(): void
    {
        Livewire::actingAs($this->owner)
            ->test(GiftManager::class, ['invitation' => $this->invitation])
            ->call('create', 'bank')
            ->set('form.provider', 'BCA')
            ->set('form.account_number', '1234 5678 90')
            ->set('form.account_name', 'Budi Santoso')
            ->call('save')
            ->assertHasNoErrors()
            ->call('create', 'qris')
            ->set('form.account_name', 'Budi Santoso')
            ->set('qris', UploadedFile::fake()->image('qris.png', 800, 800))
            ->call('save')
            ->assertHasNoErrors();

        $accounts = $this->invitation->giftAccounts()->get();
        $this->assertSame('1234567890', $accounts[0]->account_number);
        $this->assertSame('qris', $accounts[1]->type);
        $this->assertNull($accounts[1]->account_number);
        Storage::disk('public')->assertExists($accounts[1]->qris_image_path);
    }

    public function test_qris_requires_image_and_bank_requires_number(): void
    {
        Livewire::actingAs($this->owner)
            ->test(GiftManager::class, ['invitation' => $this->invitation])
            ->call('create', 'qris')
            ->set('form.account_name', 'Budi')
            ->call('save')
            ->assertHasErrors('qris')
            ->call('create', 'bank')
            ->set('form.provider', 'BCA')
            ->set('form.account_name', 'Budi')
            ->call('save')
            ->assertHasErrors('form.account_number');
    }

    public function test_gift_section_renders_on_public_page(): void
    {
        $this->invitation->giftAccounts()->create(['type' => 'bank', 'provider' => 'BCA', 'account_number' => '1234567890', 'account_name' => 'Budi Santoso']);

        $this->get('http://budi-ani.'.config('app.domain').'/')
            ->assertOk()
            ->assertSee('Amplop Digital')
            ->assertSee('1234567890');
    }
}
