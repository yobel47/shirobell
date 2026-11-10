<?php

namespace Database\Factories;

use App\Enums\InvitationStatus;
use App\Models\Invitation;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Invitation> */
class InvitationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'theme_id' => fn () => Theme::query()->firstWhere('slug', 'rose-garden')?->id ?? Theme::factory(),
            'slug' => $this->faker->unique()->regexify('[a-z]{6}-[a-z]{6}'),
            'status' => InvitationStatus::Draft,
            'groom_nickname' => 'Budi',
            'groom_fullname' => 'Budi Santoso',
            'bride_nickname' => 'Ani',
            'bride_fullname' => 'Ani Lestari',
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => InvitationStatus::Active,
            'published_at' => now(),
            'active_until' => now()->addDays(90),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'status' => InvitationStatus::Expired,
            'published_at' => now()->subDays(100),
            'active_until' => now()->subDays(10),
        ]);
    }
}
