<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TeamFactory extends Factory
{
    private static array $categorySlugs = [
        'baby', 'kids1', 'kids2', 'middle1', 'middle2', 'high', 'technic', 'university',
    ];

    private static array $teamPrefixes = [
        'Robo', 'Tech', 'Cyber', 'Mega', 'Ultra', 'Super', 'Power', 'Alpha', 'Beta', 'Omega',
    ];

    private static array $teamSuffixes = [
        'Stars', 'Ninjas', 'Warriors', 'Dragons', 'Tigers', 'Falcons', ' Wolves', 'Bots', 'Kids', 'Makers',
    ];

    public function definition(): array
    {
        return [
            'id' => strtoupper(Str::random(12)),
            'event_id' => Event::factory(),
            'name' => $this->faker->randomElement(self::$teamPrefixes)
                . $this->faker->randomElement(self::$teamSuffixes)
                . ' ' . $this->faker->randomNumber(2),
            'category_slug' => $this->faker->randomElement(self::$categorySlugs),
            'total_score' => $this->faker->randomFloat(2, 0, 1000),
        ];
    }

    public function forEvent(Event $event): static
    {
        return $this->state(fn(array $attributes) => [
            'event_id' => $event->id,
        ]);
    }

    public function inCategory(string $slug): static
    {
        return $this->state(fn(array $attributes) => [
            'category_slug' => $slug,
        ]);
    }
}
