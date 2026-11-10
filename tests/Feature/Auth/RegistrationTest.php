<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_renders(): void
    {
        $this->get('/register')->assertOk()->assertSee('Buat akun');
    }

    public function test_new_user_becomes_customer_and_gets_verification_email(): void
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'Budi Santoso',
            'email' => 'Budi@Example.com',
            'phone' => '081234567890',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertRedirect('/app');

        $user = User::query()->where('email', 'budi@example.com')->firstOrFail();
        $this->assertSame(UserRole::Customer, $user->role);
        $this->assertFalse($user->is_blocked);
        $this->assertAuthenticatedAs($user);
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_role_cannot_be_injected_through_registration(): void
    {
        $this->post('/register', [
            'name' => 'Penyusup',
            'email' => 'evil@example.com',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'role' => 'admin',
            'is_blocked' => false,
        ]);

        $this->assertSame(UserRole::Customer, User::query()->where('email', 'evil@example.com')->first()->role);
    }

    public function test_invalid_phone_is_rejected(): void
    {
        $this->post('/register', [
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'phone' => '12345',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertSessionHasErrors('phone');
    }

    public function test_registration_is_rate_limited_per_ip(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->post('/register', ['name' => 'Spam', 'email' => "spam{$i}@example.com", 'password' => 'rahasia123', 'password_confirmation' => 'rahasia123']);
            auth()->logout();
        }

        $this->post('/register', ['name' => 'Spam', 'email' => 'spam6@example.com', 'password' => 'rahasia123', 'password_confirmation' => 'rahasia123'])
            ->assertSessionHasErrors('email');

        $this->assertDatabaseMissing('users', ['email' => 'spam6@example.com']);
    }
}
