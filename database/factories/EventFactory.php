<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    public function definition(): array
    {
        $cities = ['São Paulo', 'Rio de Janeiro', 'Belo Horizonte', 'Curitiba', 'Porto Alegre', 'Salvador', 'Recife', 'Brasília'];

        return [
            'id' => strtoupper(Str::random(12)),
            'name' => 'TBR ' . $this->faker->randomElement($cities) . ' ' . $this->faker->year(),
            'date' => $this->faker->dateTimeBetween('+1 month', '+6 months')->format('Y-m-d'),
            'status' => $this->faker->boolean(70),
            'location' => [
                'city' => $this->faker->randomElement($cities),
                'state' => $this->faker->stateAbbr(),
                'venue' => $this->faker->company() . ' Arena',
            ],
            'ranking_config' => [
                'show_awards' => true,
                'categories' => $this->faker->randomElements(
                    ['baby', 'kids1', 'kids2', 'middle1', 'middle2', 'high', 'technic', 'university'],
                    $this->faker->numberBetween(2, 5)
                ),
            ],
        ];
    }

    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => true,
        ]);
    }

    public function past(): static
    {
        return $this->state(fn(array $attributes) => [
            'date' => $this->faker->dateTimeBetween('-6 months', '-1 day')->format('Y-m-d'),
            'status' => false,
        ]);
    }
}
