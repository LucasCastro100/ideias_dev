<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DpMissionFactory extends Factory
{
    private static array $dpLevels = ['baby', 'kids1', 'kids2', 'middle', 'high', 'technic_university'];

    public function definition(): array
    {
        $itemCount = $this->faker->numberBetween(1, 4);
        $items = [];

        for ($i = 0; $i < $itemCount; $i++) {
            $hasOptions = $this->faker->boolean(70);
            $optionCount = $this->faker->numberBetween(2, 4);

            $options = [];
            if ($hasOptions) {
                for ($j = 0; $j < $optionCount; $j++) {
                    $options[] = [
                        'name' => $this->faker->randomElement([
                            'Totalmente dentro', 'Parcialmente dentro', 'Outro',
                            'Tocando a área', 'Não atendeu',
                        ]),
                        'value' => $this->faker->randomElement([0, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50]),
                    ];
                }
            }

            $items[] = [
                'name' => $this->faker->words(3, true),
                'type' => $this->faker->randomElement(['radio', 'number', 'bonus']),
                'has_max' => $this->faker->boolean(30),
                'max_value' => $this->faker->optional(0.3)->numberBetween(1, 10),
                'options' => $options,
            ];
        }

        return [
            'dp_level' => $this->faker->randomElement(self::$dpLevels),
            'mission_title' => 'MISSÃO ' . $this->faker->numberBetween(1, 9) . ' - ' . strtoupper($this->faker->words(3, true)),
            'description' => $this->faker->sentence(6),
            'image' => '',
            'items' => $items,
            'sort_order' => $this->faker->numberBetween(1, 15),
        ];
    }

    public function forLevel(string $level): static
    {
        return $this->state(fn(array $attributes) => [
            'dp_level' => $level,
        ]);
    }
}
