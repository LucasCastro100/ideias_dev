<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AssessmentQuestionFactory extends Factory
{
    private static array $levels = ['basic', 'advanced'];
    private static array $modalitiesByLevel = [
        'basic' => ['ap'],
        'advanced' => ['mc', 'om', 'te'],
    ];

    private static array $objectNames = [
        'Projeto', 'Engenharia e Design', 'Apresentação', 'Trabalho em Equipe',
        'Problema Abordado', 'Pesquisa do Problema', 'Solução Inovadora', 'Publicação',
        'Estratégia Geral', 'Organização da Equipe', 'Capacidade Operacional', 'Capacidade de Gestão',
        'Abordagem dos Desafios Práticos', 'Competência Técnica e Tecnológica', 'Documentação Técnica',
    ];

    public function definition(): array
    {
        $level = $this->faker->randomElement(self::$levels);
        $modality = $this->faker->randomElement(self::$modalitiesByLevel[$level]);
        $criteriaCount = $this->faker->numberBetween(3, 6);

        $criteria = [];
        for ($i = 0; $i < $criteriaCount; $i++) {
            $criteria[] = $this->faker->sentence(10);
        }

        return [
            'level' => $level,
            'modality_slug' => $modality,
            'object_name' => $this->faker->randomElement(self::$objectNames),
            'image' => '',
            'mission' => $this->faker->boolean(20),
            'criteria' => $criteria,
            'sort_order' => $this->faker->numberBetween(1, 20),
        ];
    }

    public function forLevel(string $level): static
    {
        return $this->state(fn(array $attributes) => [
            'level' => $level,
        ]);
    }

    public function forModality(string $modalitySlug): static
    {
        return $this->state(fn(array $attributes) => [
            'modality_slug' => $modalitySlug,
        ]);
    }
}
