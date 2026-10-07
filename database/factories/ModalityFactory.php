<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ModalityFactory extends Factory
{
    public function definition(): array
    {
        $modalities = [
            ['slug' => 'ap', 'label' => 'Apresentação'],
            ['slug' => 'dp', 'label' => 'Desafio Prático (DP)'],
            ['slug' => 'mc', 'label' => 'Mérito Científico (MC)'],
            ['slug' => 'om', 'label' => 'Organização e Método (OM)'],
            ['slug' => 'te', 'label' => 'Tecnologia e Engenharia (TE)'],
        ];

        $mod = $this->faker->randomElement($modalities);

        return [
            'level' => $this->faker->randomElement(['basic', 'intermediary', 'advanced']),
            'config_id' => strtoupper($this->faker->regexify('[A-Z0-9]{12}')),
            'slug' => $mod['slug'],
            'label' => $mod['label'],
            'sort_order' => $this->faker->numberBetween(1, 10),
        ];
    }

    public function forLevel(string $level): static
    {
        return $this->state(fn(array $attributes) => [
            'level' => $level,
        ]);
    }
}
