<?php

namespace Database\Factories;

use App\Models\PromoCode;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<PromoCode> */
class PromoCodeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('HEMAT##??')),
            'type' => 'percent',
            'value' => 10,
            'is_active' => true,
        ];
    }
}
