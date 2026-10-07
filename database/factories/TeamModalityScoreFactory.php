<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamModalityScoreFactory extends Factory
{
    private static array $modalities = ['ap', 'dp', 'mc', 'om', 'te'];

    public function definition(): array
    {
        $modality = $this->faker->randomElement(self::$modalities);
        $isDp = $modality === 'dp';

        $scores = $isDp
            ? $this->generateDpScores()
            : $this->generateRegularScores();

        return [
            'team_id' => Team::factory(),
            'modality_slug' => $modality,
            'round' => $isDp ? $this->faker->randomElement(['r1', 'r2', 'r3']) : null,
            'scores' => $scores,
            'total' => array_sum($scores),
            'comment' => $this->faker->optional(0.3)->sentence(),
        ];
    }

    public function forModality(string $slug): static
    {
        return $this->state(fn(array $attributes) => [
            'modality_slug' => $slug,
            'round' => $slug === 'dp' ? 'r1' : null,
        ]);
    }

    private function generateRegularScores(): array
    {
        $count = $this->faker->numberBetween(3, 5);
        $scores = [];
        for ($i = 0; $i < $count; $i++) {
            $scores[] = $this->faker->randomFloat(1, 0, 10);
        }
        return $scores;
    }

    private function generateDpScores(): array
    {
        $missions = $this->faker->numberBetween(3, 6);
        $scores = [];
        for ($i = 0; $i < $missions; $i++) {
            $scores['mission_' . ($i + 1)] = $this->faker->numberBetween(0, 100);
        }
        return $scores;
    }
}
