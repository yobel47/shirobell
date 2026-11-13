<?php

namespace Tests\Feature\Core;

use App\Filament\Pages\ManageSettings;
use App\Models\Invitation;
use App\Models\User;
use App\Services\SettingService;
use Database\Seeders\ThemeSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{string, string}> */
    public static function pages(): array
    {
        return [
            'terms' => ['/syarat-ketentuan', 'Syarat &amp; Ketentuan'],
            'privacy' => ['/kebijakan-privasi', 'Kebijakan Privasi'],
            'refund' => ['/kebijakan-refund', 'Kebijakan Refund'],
            'contact' => ['/kontak', 'Kontak'],
        ];
    }

    #[DataProvider('pages')]
    public function test_legal_page_renders_with_business_identity_from_settings(string $path, string $title): void
    {
        $settings = app(SettingService::class);
        $settings->set('business_name', 'CV Lonceng Putih');
        $settings->set('business_address', 'Jl. Melati No. 1, Bandung');
        $settings->set('support_email', 'halo@shirobell.test');
        $settings->set('support_whatsapp', '081234567890');

        $this->get($path)
            ->assertOk()
            ->assertSee('<h1', false)
            ->assertSee($title, false)
            ->assertSee('CV Lonceng Putih')
            ->assertSee('Jl. Melati No. 1, Bandung')
            ->assertSee('halo@shirobell.test')
            ->assertSee('https://wa.me/6281234567890', false)
            ->assertSee('Terakhir diperbarui');
    }

    public function test_terms_show_current_price_and_duration(): void
    {
        app(SettingService::class)->set('package_price', 149000);

        $this->get('/syarat-ketentuan')->assertSee('Rp 149.000')->assertSee('90 hari');
    }

    public function test_pages_fall_back_when_contacts_are_empty(): void
    {
        $this->get('/kontak')
            ->assertOk()
            ->assertSee('Kontak support belum tersedia')
            ->assertSee('Shirobell');
    }

    public function test_legal_pages_are_not_served_on_invitation_subdomains(): void
    {
        $this->get('http://budi-ani.shirobell.test/syarat-ketentuan')->assertNotFound();
    }

    public function test_footer_register_and_checkout_link_to_policies(): void
    {
        $this->get('/')
            ->assertSee(route('legal.terms'))
            ->assertSee(route('legal.privacy'))
            ->assertSee(route('legal.refund'))
            ->assertSee(route('legal.contact'));

        $this->get('/register')
            ->assertSee('Dengan mendaftar, Anda menyetujui')
            ->assertSee(route('legal.terms'));
    }

    public function test_admin_can_save_business_identity(): void
    {
        Filament::setCurrentPanel('admin');

        Livewire::actingAs(User::factory()->admin()->create())
            ->test(ManageSettings::class)
            ->set('data.business_name', 'CV Lonceng Putih')
            ->set('data.business_address', 'Jl. Melati No. 1, Bandung')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('CV Lonceng Putih', app(SettingService::class)->get('business_name'));
        $this->assertSame('Jl. Melati No. 1, Bandung', app(SettingService::class)->get('business_address'));
    }

    public function test_instagram_and_footer_contacts_with_floating_whatsapp_button(): void
    {
        $settings = app(SettingService::class);
        $settings->set('support_whatsapp', '081234567890');
        $settings->set('support_instagram', '@shirobell.id');
        $settings->set('support_email', 'halo@shirobell.test');

        $this->get('/kontak')
            ->assertSee('https://instagram.com/shirobell.id', false)
            ->assertSee('@shirobell.id');

        $this->get('/')
            ->assertSee('Hubungi kami')
            ->assertSee('https://instagram.com/shirobell.id', false)
            ->assertSee('mailto:halo@shirobell.test', false)
            ->assertSee('Tanya kami')
            ->assertSee('https://wa.me/6281234567890?text=', false);
    }

    public function test_no_contact_block_or_chat_button_when_contacts_are_empty(): void
    {
        $this->get('/')->assertDontSee('Hubungi kami')->assertDontSee('Tanya kami');
    }

    public function test_guest_invitations_never_show_the_platform_chat_button(): void
    {
        $this->seed(ThemeSeeder::class);
        app(SettingService::class)->set('support_whatsapp', '081234567890');
        Invitation::factory()->active()->create(['slug' => 'budi-ani']);

        $this->get('http://budi-ani.'.config('app.domain'))->assertOk()->assertDontSee('Tanya kami');
    }

    public function test_admin_saves_instagram_username(): void
    {
        Filament::setCurrentPanel('admin');

        Livewire::actingAs(User::factory()->admin()->create())
            ->test(ManageSettings::class)
            ->set('data.support_instagram', 'shirobell.id')
            ->call('save')
            ->assertHasNoErrors();
        $this->assertSame('shirobell.id', app(SettingService::class)->get('support_instagram'));

        Livewire::actingAs(User::factory()->admin()->create())
            ->test(ManageSettings::class)
            ->set('data.support_instagram', 'https://instagram.com/x')
            ->call('save')
            ->assertHasErrors('data.support_instagram');
    }
}
