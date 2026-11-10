<?php

namespace Tests\Feature\Core;

use App\Models\Invitation;
use App\Models\User;
use Database\Seeders\ThemeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_page_on_main_domain_shows_custom_404(): void
    {
        $this->get('/halaman-tidak-ada')
            ->assertNotFound()
            ->assertSee('Halaman tidak ditemukan')
            ->assertSee('Kembali ke beranda')
            ->assertSee('noindex', false);
    }

    public function test_unknown_path_on_invitation_subdomain_shows_custom_404(): void
    {
        $this->get('http://budi-ani.shirobell.test/apa-saja')
            ->assertNotFound()
            ->assertSee('Halaman tidak ditemukan')
            ->assertSee('http://shirobell.test', false);
    }

    public function test_forbidden_page_is_custom(): void
    {
        $this->seed(ThemeSeeder::class);
        $invitation = Invitation::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('app.invitations.edit', $invitation))
            ->assertForbidden()
            ->assertSee('Akses ditolak')
            ->assertDontSee('This action is unauthorized');
    }

    public function test_server_error_hides_exception_details(): void
    {
        config(['app.debug' => false]);
        Route::get('/_test-boom', fn () => throw new RuntimeException('rahasia-internal'));

        $this->get('/_test-boom')
            ->assertStatus(500)
            ->assertSee('Terjadi kesalahan')
            ->assertDontSee('rahasia-internal');
    }

    /** @return array<string, array{int, string}> */
    public static function statuses(): array
    {
        return [
            '419' => [419, 'Sesi telah berakhir'],
            '429' => [429, 'Terlalu banyak percobaan'],
            '503' => [503, 'Sedang dalam pemeliharaan'],
            '4xx fallback' => [422, 'Permintaan tidak dapat diproses'],
            '5xx fallback' => [502, 'Terjadi kesalahan'],
        ];
    }

    #[DataProvider('statuses')]
    public function test_other_statuses_render_indonesian_pages(int $status, string $title): void
    {
        config(['app.debug' => false]);
        Route::get('/_test-status', fn () => throw new HttpException($status));

        $this->get('/_test-status')->assertStatus($status)->assertSee($title);
    }
}
