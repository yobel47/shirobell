<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_sees_profile_page_from_account_menu(): void
    {
        $user = User::factory()->create(['phone' => '081234567890']);

        $this->actingAs($user)->get(route('app.dashboard'))->assertSee(route('app.profile'));
        $this->actingAs($user)->get(route('app.profile'))
            ->assertOk()
            ->assertSee('Profil &amp; kata sandi', false)
            ->assertSee('081234567890');
    }

    public function test_customer_updates_name_and_phone(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('app.profile'))
            ->put(route('user-profile-information.update'), ['name' => 'Budi Baru', 'email' => $user->email, 'phone' => '6281299998888'])
            ->assertRedirect(route('app.profile'));

        $user->refresh();
        $this->assertSame(['Budi Baru', '6281299998888'], [$user->name, $user->phone]);

        $this->actingAs($user)->get(route('app.profile'))->assertSee('Profil berhasil diperbarui.');
    }

    public function test_invalid_phone_is_rejected_and_phone_can_be_cleared(): void
    {
        $user = User::factory()->create(['phone' => '081234567890']);

        $this->actingAs($user)->from(route('app.profile'))
            ->put(route('user-profile-information.update'), ['name' => $user->name, 'email' => $user->email, 'phone' => '12345'])
            ->assertSessionHasErrorsIn('updateProfileInformation', 'phone');

        $this->actingAs($user)->from(route('app.profile'))
            ->put(route('user-profile-information.update'), ['name' => $user->name, 'email' => $user->email, 'phone' => ''])
            ->assertSessionHasNoErrors();
        $this->assertNull($user->refresh()->phone);
    }

    public function test_changing_email_requires_verifying_the_new_address(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->actingAs($user)->from(route('app.profile'))
            ->put(route('user-profile-information.update'), ['name' => $user->name, 'email' => 'baru@example.com']);

        $user->refresh();
        $this->assertSame('baru@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
        $this->actingAs($user)->get(route('app.dashboard'))->assertRedirect(route('verification.notice'));
    }

    public function test_customer_changes_password_only_with_the_current_one(): void
    {
        $user = User::factory()->create(['password' => Hash::make('sandi-lama-123')]);

        $this->actingAs($user)->from(route('app.profile'))
            ->put(route('user-password.update'), ['current_password' => 'salah-total', 'password' => 'sandi-baru-456', 'password_confirmation' => 'sandi-baru-456'])
            ->assertSessionHasErrorsIn('updatePassword', 'current_password');
        $this->assertTrue(Hash::check('sandi-lama-123', $user->refresh()->password));

        $this->actingAs($user)->from(route('app.profile'))
            ->put(route('user-password.update'), ['current_password' => 'sandi-lama-123', 'password' => 'sandi-baru-456', 'password_confirmation' => 'sandi-baru-456'])
            ->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('sandi-baru-456', $user->refresh()->password));
    }

    public function test_guests_and_admins_do_not_use_this_page(): void
    {
        $this->get(route('app.profile'))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->admin()->create())->get(route('app.profile'))->assertRedirect('/admin');
    }
}
