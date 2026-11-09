<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Credentials come from .env (ADMIN_EMAIL, ADMIN_PASSWORD); never hardcoded.
     */
    public function run(): void
    {
        $email = config('shirobell.admin.email');
        $password = config('shirobell.admin.password');

        if (! $email || ! $password) {
            $this->command?->warn('ADMIN_EMAIL / ADMIN_PASSWORD kosong di .env, admin tidak dibuat.');

            return;
        }

        $admin = User::query()->firstOrNew(['email' => $email]);

        if (! $admin->exists) {
            $admin->name = 'Administrator';
            $admin->password = $password;
        }

        $admin->role = UserRole::Admin;
        $admin->email_verified_at ??= now();
        $admin->save();
    }
}
