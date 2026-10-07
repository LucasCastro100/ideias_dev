<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\Category;
use App\Models\Event;

class TbrRanking extends Component
{
    public $title = 'TBR - Ranking';

    public $event_id;
    public $event = null;
    public array $teamsByCategory = [];
    public $showSidebar = false;

    public function mount($event_id)
    {
        $this->event_id = $event_id;
        $this->loadEvent();
        $this->organizeTeamsByCategory();
    }

    public function loadEvent(): void
    {
        $this->event = Event::with(['teams.scores'])->find($this->event_id);
    }

    private function calculateModalityScore($scores, string $modalitySlug): float
    {
        $scoresColl = $scores->where('modality_slug', $modalitySlug);

        if ($scoresColl->isEmpty()) return 0;

        if ($modalitySlug === 'dp') {
            return (float) $scoresColl->max('total');
        }

        $first = $scoresColl->first();
        return $first ? (float) $first->total : 0;
    }

    public function organizeTeamsByCategory(): void
    {
        if (!$this->event) return;

        $teams = $this->event->teams;
        $categories = Category::orderBy('sort_order')->get()->map(fn($c) => [
            'slug' => $c->slug, 'label' => $c->label, 'id' => $c->id,
            'modalitie' => $c->modality_level,
        ]);
        $modalitiesByLevel = config('tbr-config.modalities_by_level');

        $modalitiesToShowGlobal = ['ap', 'mc', 'om', 'te', 'dp'];
        $topPositions = 3;
        $generalTopPositions = 3;
        $this->teamsByCategory = [];

        $categoriesWithTeams = $categories->filter(function ($category) use ($teams) {
            $slug = $category['slug'];
            return $teams->contains(fn($team) => ($team->category_slug ?? '') === $slug);
        });

        foreach ($categoriesWithTeams as $category) {
            $slug = $category['slug'];
            $modalitieLevel = $category['modalitie'] ?? 'basic';

            $modalitiesCategory = collect($modalitiesByLevel[$modalitieLevel] ?? [])
                ->map(fn($mod) => is_array($mod) ? $mod['slug'] : $mod)
                ->toArray();

            if ($modalitieLevel === 'basic') {
                $modalitiesAllowed = array_values(array_filter($modalitiesToShowGlobal, function ($mod) use ($modalitiesCategory) {
                    return $mod !== 'ap' && in_array($mod, $modalitiesCategory);
                }));
            } else {
                $modalitiesAllowed = array_values(array_filter($modalitiesToShowGlobal, function ($mod) use ($modalitiesCategory) {
                    return in_array($mod, $modalitiesCategory);
                }));
            }

            $teamsInCategory = $teams->filter(fn($team) => ($team->category_slug ?? null) === $slug);
            $totalTeamCount = $teamsInCategory->count();

            $totalTeams = $teamsInCategory
                ->map(fn($team) => [
                    'id' => $team->id,
                    'name' => $team->name,
                    'total' => (float) $team->total_score,
                ])
                ->sortByDesc('total')
                ->values()
                ->toArray();

            $generalPodiumIds = collect($totalTeams)->take($generalTopPositions)->pluck('id')->toArray();

            $modalitiesToShow = [];
            $isInterno = ($this->event->tipo_evento ?? 'interno') === 'interno';
            if (!$isInterno) {
                if ($totalTeamCount >= 10) {
                    $modalitiesToShow = $modalitiesAllowed;
                } elseif ($totalTeamCount === 9) {
                    $modalitiesToShow = array_values(array_intersect($modalitiesAllowed, ['mc', 'om']));
                } elseif ($totalTeamCount === 8) {
                    $modalitiesToShow = array_values(array_intersect($modalitiesAllowed, ['mc']));
                }
            }

            $modalitiesData = [];
            foreach ($modalitiesToShow as $mod) {
                $allMods = collect($modalitiesByLevel[$modalitieLevel] ?? []);
                $foundMod = $allMods->first(fn($m) => (is_array($m) ? $m['slug'] : $m) === $mod);

                if ($foundMod) {
                    $modalitiesData[] = is_array($foundMod)
                        ? ['slug' => $foundMod['slug'], 'label' => $foundMod['label']]
                        : ['slug' => $foundMod, 'label' => strtoupper($foundMod)];
                }
            }

            $modalitiesScores = [];
            $modalitiesHighlightedIds = [];
            foreach ($modalitiesToShow as $modSlug) {
                $sorted = $teamsInCategory
                    ->map(fn($team) => [
                        'id' => $team->id,
                        'name' => $team->name,
                        'score' => $this->calculateModalityScore($team->scores, $modSlug),
                    ])
                    ->sortByDesc('score')
                    ->values()
                    ->toArray();

                $modalitiesScores[$modSlug] = $sorted;

                $teamsAfterExclusion = $topPositions > 0 && $modSlug !== 'dp'
                    ? collect($sorted)->reject(fn($t) => in_array($t['id'], $generalPodiumIds))->values()
                    : collect($sorted)->values();

                $remainingCount = $teamsAfterExclusion->count();
                if ($remainingCount <= 1) {
                    $modalitiesHighlightedIds[$modSlug] = [];
                    continue;
                }

                $dynamicTop = $remainingCount <= 3
                    ? 1
                    : ($remainingCount === 4 ? 2 : min($topPositions, 3));

                $highlighted = $teamsAfterExclusion->take($dynamicTop)->pluck('id')->toArray();
                $modalitiesHighlightedIds[$modSlug] = $highlighted;
            }

            $this->teamsByCategory[$slug] = [
                'label' => $category['label'],
                'modalities' => $modalitiesScores,
                'modalities_data' => $modalitiesData,
                'modalities_highlighted_ids' => $modalitiesHighlightedIds,
                'total' => $totalTeams,
                'top_positions' => $topPositions,
                'general_top_positions' => $generalTopPositions,
            ];
        }
    }

    public function render()
    {
        return view('livewire.page.tbr.ranking', [
            'event' => $this->event,
            'teamsByCategory' => $this->teamsByCategory,
        ])->layout('layouts.app-tbr-public', [
            'title' => $this->title
        ]);
    }
}
