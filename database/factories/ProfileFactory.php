<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->firstName(),
            'avatar' => null,
            'avatar_color' => null,
            'is_main' => false,
        ];
    }

    public function main(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_main' => true,
        ]);
    }

    public function forOwner(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'owner_id' => $user->id,
            'user_id' => $user->id,
        ]);
    }
}
