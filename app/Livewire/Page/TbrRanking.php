<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;

class TbrRanking extends Component
{
    public $event_id;
    public array $event = [];
    public array $teamsByCategory = [];
    public $showSidebar = true;

    public function mount($event_id)
    {
        $this->event_id = $event_id;
        $this->loadEvent($event_id);
        $this->organizeTeamsByCategory();
        $this->showSidebar = false;
    }

    public function loadEvent($id): void
    {
        $jsonPath = 'tbr/json/data.json';

        if (Storage::disk('public')->exists($jsonPath)) {
            $json = Storage::disk('public')->get($jsonPath);
            $events = json_decode($json, true) ?? [];

            $this->event = collect($events)->firstWhere('id', $id) ?? [];
        }
    }

    private function calculateModalityScore(array $modalidades = [], string $modalitySlug): float
    {
        if (!isset($modalidades[$modalitySlug]['nota'])) return 0;

        $nota = $modalidades[$modalitySlug]['nota'];

        if ($modalitySlug === 'dp') {
            $maxSum = 0;
            foreach (['r1', 'r2', 'r3'] as $r) {
                if (isset($nota[$r]) && is_array($nota[$r])) {
                    $sum = array_sum(array_map('floatval', $nota[$r]));
                    if ($sum > $maxSum) $maxSum = $sum;
                }
            }
            return $maxSum;
        }

        return is_array($nota) ? array_sum(array_map('floatval', $nota)) : floatval($nota);
    }

    public function organizeTeamsByCategory(): void
    {
        $teams = collect($this->event['equipes'] ?? []);
        $categories = collect(config('tbr-config.categories'));
        $modalitiesByLevel = config('tbr-config.modalities_by_level');
    
        $rankingConfig = $this->event['ranking_config'] ?? [];
        $modalitiesToShowGlobal = $rankingConfig['modalities_to_show'] ?? ['ap', 'mc', 'om', 'te', 'dp'];
        $topPositions = $rankingConfig['top_positions'] ?? 3;
        $generalTopPositions = $rankingConfig['general_top_positions'] ?? 3;
    
        $this->teamsByCategory = [];
    
        $categoriesWithTeams = $categories->filter(function ($category) use ($teams) {
            $slug = $category['slug'];
            return $teams->contains(fn($team) => ($team['category'] ?? '') === $slug);
        });
    
        foreach ($categoriesWithTeams as $category) {
            $slug = $category['slug'];
            $modalitie_level = $category['modalitie'] ?? 'basic';
    
            // Modalidades disponíveis para o nível da categoria
            $modalitiesCategory = collect($modalitiesByLevel[$modalitie_level] ?? [])
                ->map(fn($mod) => is_array($mod) ? $mod['slug'] : $mod)
                ->toArray();
    
            // Se o nível for basic, remover a modalidade 'ap'
            if ($modalitie_level === 'basic') {
                $modalitiesAllowed = array_values(array_filter($modalitiesToShowGlobal, function ($mod) use ($modalitiesCategory) {
                    return $mod !== 'ap' && in_array($mod, $modalitiesCategory);
                }));
            } else {
                $modalitiesAllowed = array_values(array_filter($modalitiesToShowGlobal, function ($mod) use ($modalitiesCategory) {
                    return in_array($mod, $modalitiesCategory);
                }));
            }
    
            // Labels das modalidades para exibição
            $modalitiesData = [];
            foreach ($modalitiesAllowed as $mod) {
                $allMods = collect($modalitiesByLevel[$modalitie_level] ?? []);
                $foundMod = $allMods->first(fn($m) => (is_array($m) ? $m['slug'] : $m) === $mod);
    
                if ($foundMod) {
                    $modalitiesData[] = is_array($foundMod)
                        ? ['slug' => $foundMod['slug'], 'label' => $foundMod['label']]
                        : ['slug' => $foundMod, 'label' => strtoupper($foundMod)];
                }
            }
    
            // Equipes da categoria
            $teamsInCategory = $teams->filter(fn($team) => ($team['category'] ?? null) === $slug);
    
            // Tabelas por modalidade – mostra todas as equipes
            $modalitiesScores = [];
            foreach ($modalitiesAllowed as $modSlug) {
                $modalitiesScores[$modSlug] = $teamsInCategory
                    ->map(fn($team) => [
                        'id' => $team['id'],
                        'name' => $team['name'],
                        'score' => isset($team['modalities'][$modSlug]['total']) ? floatval($team['modalities'][$modSlug]['total']) : 0,
                    ])
                    ->sortByDesc('score')
                    ->values()
                    ->toArray();
            }
    
            // Pontuação geral – mostra todas as equipes
            $totalTeams = $teamsInCategory
                ->map(fn($team) => [
                    'id' => $team['id'],
                    'name' => $team['name'],
                    'total' => isset($team['nota_total']) ? floatval($team['nota_total']) : 0,
                ])
                ->sortByDesc('total')
                ->values()
                ->toArray();
    
            // Resultado final
            $this->teamsByCategory[$slug] = [
                'label' => $category['label'],
                'modalities' => $modalitiesScores,
                'modalities_data' => $modalitiesData,
                'total' => $totalTeams,
                'top_positions' => $topPositions,
                'general_top_positions' => $generalTopPositions,
            ];
        }
    }
        
    public function render()
    {
        return view('livewire.page.tbr-ranking', [
            'event' => $this->event,
            'teamsByCategory' => $this->teamsByCategory,
        ])->layout('layouts.app-sidebar', ['showSidebar' => $this->showSidebar]);
    }
}
