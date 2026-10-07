<?php

namespace Database\Seeders;

use App\Models\AssessmentQuestion;
use App\Models\Category;
use App\Models\DpMission;
use App\Models\Modality;
use Illuminate\Database\Seeder;

class TbrReferenceSeeder extends Seeder
{
    public function run(): void
    {
        $config = config('tbr-config');

        $this->seedCategories($config['categories']);
        $this->seedModalities($config['modalities_by_level']);
        $this->seedAssessmentQuestions($config['questions_by_level']);
        $this->seedDpMissions($config['dp_by_level']);

        $this->command?->info('TBR reference data seeded successfully from config/tbr-config.php');
    }

    private function seedCategories(array $categories): void
    {
        $sortOrder = 0;

        foreach ($categories as $cat) {
            $sortOrder++;

            Category::updateOrCreate(
                ['id' => $cat['id']],
                [
                    'slug' => $cat['slug'],
                    'label' => $cat['label'],
                    'modality_level' => $cat['modalitie'],
                    'question_level' => $cat['question'],
                    'dp_level' => $cat['dp'],
                    'sort_order' => $sortOrder,
                ]
            );
        }
    }

    private function seedModalities(array $modalitiesByLevel): void
    {
        foreach ($modalitiesByLevel as $level => $modalities) {
            $sortOrder = 0;

            foreach ($modalities as $modData) {
                $sortOrder++;

                Modality::updateOrCreate(
                    [
                        'level' => $level,
                        'slug' => $modData['slug'],
                    ],
                    [
                        'config_id' => $modData['id'],
                        'label' => $modData['label'],
                        'sort_order' => $sortOrder,
                    ]
                );
            }
        }
    }

    private function seedAssessmentQuestions(array $questionsByLevel): void
    {
        foreach ($questionsByLevel as $level => $modalities) {
            foreach ($modalities as $modalityData) {
                $modalitySlug = $modalityData['modality'];
                $sortOrder = 0;

                foreach ($modalityData['assessment'] as $object) {
                    $sortOrder++;

                    AssessmentQuestion::updateOrCreate(
                        [
                            'level' => $level,
                            'modality_slug' => $modalitySlug,
                            'object_name' => $object['object'],
                        ],
                        [
                            'image' => $object['image'] ?? '',
                            'mission' => (bool)($object['mission'] ?? 0),
                            'criteria' => array_values($object['description'] ?? []),
                            'sort_order' => $sortOrder,
                        ]
                    );
                }
            }
        }
    }

    private function seedDpMissions(array $dpByLevel): void
    {
        $year = config('app.tbr_year', date('Y'));

        foreach ($dpByLevel as $level => $missions) {
            $sortOrder = 0;

            foreach ($missions as $mission) {
                $sortOrder++;

                DpMission::updateOrCreate(
                    [
                        'year' => $year,
                        'dp_level' => $level,
                        'mission_title' => $mission['mission'],
                    ],
                    [
                        'description' => $mission['description'] ?? '',
                        'image' => $mission['image'] ?? '',
                        'items' => $mission['itens'] ?? [],
                        'depends_on' => $mission['depends_on'] ?? null,
                        'sort_order' => $sortOrder,
                    ]
                );
            }
        }
    }
}
