<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->words(2, true),
            'description' => fake()->optional()->sentence(),
            'value' => fake()->randomFloat(2, 50, 500),
            'billing_cycle' => fake()->randomElement(['monthly', 'quarterly', 'yearly']),
            'active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'active' => false,
        ]);
    }

    public function monthly(float $value = 100): static
    {
        return $this->state(fn(array $attributes) => [
            'value' => $value,
            'billing_cycle' => 'monthly',
        ]);
    }
}
