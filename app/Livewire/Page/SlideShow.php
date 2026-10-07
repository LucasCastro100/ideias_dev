<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\Category;
use App\Models\Event;

class SlideShow extends Component
{
    public $slides = [];
    public $currentSlide = 0;
    public $showSidebar = false;
    public $title = 'TBR - Slide Ranking';

    public function mount($event_id)
    {
        $event = Event::with('teams.scores')->find($event_id);

        if (!$event) {
            abort(404, 'Evento não encontrado');
        }

        $categories = Category::orderBy('sort_order')->get()->map(fn($c) => [
            'slug' => $c->slug, 'label' => $c->label,
            'modalitie' => $c->modality_level, 'dp' => $c->dp_level,
        ])->toArray();
        $modalitiesByLevel = config('tbr-config.modalities_by_level');

        $topPositions = 3;
        $generalTopPositions = 3;
        $slides = [];

        $slides[] = [
            'type' => 'intro',
            'title' => $event->name ?? 'Evento',
        ];

        foreach ($categories as $category) {
            $catSlug = $category['slug'];
            $catLabel = $category['label'];
            $modalitieLevel = $category['modalitie'];
            $modalities = $modalitiesByLevel[$modalitieLevel] ?? [];

            $teamsCategory = $event->teams
                ->filter(fn($t) => ($t->category_slug ?? '') === $catSlug)
                ->values();

            if ($teamsCategory->isEmpty()) continue;

            $teamsSortedTotal = $teamsCategory->sortByDesc(fn($t) => (float) $t->total_score)->values();
            $generalTopCount = min($generalTopPositions, $teamsSortedTotal->count());

            if ($catSlug === 'baby') {
                for ($i = $generalTopCount - 1; $i >= 0; $i--) {
                    $posNumber = $i + 1;
                    $slides[] = [
                        'type' => 'award',
                        'categoryLabel' => $catLabel,
                        'modalidadeLabel' => 'Nota Geral',
                        'posNumber' => $posNumber,
                        'teamName' => null,
                    ];
                    $slides[] = [
                        'type' => 'award',
                        'categoryLabel' => $catLabel,
                        'modalidadeLabel' => 'Nota Geral',
                        'posNumber' => $posNumber,
                        'teamName' => $teamsSortedTotal[$i]['name'],
                    ];
                }
                continue;
            }

            $totalTeamCount = $teamsCategory->count();

            $generalPodiumIds = $teamsSortedTotal->take($generalTopPositions)->pluck('id')->toArray();

            $modalitiesToAward = [];
            $isInterno = ($event->tipo_evento ?? 'interno') === 'interno';
            if (!$isInterno) {
                if ($totalTeamCount >= 10) {
                    $modalitiesToAward = $modalities;
                } elseif ($totalTeamCount === 9) {
                    $modalitiesToAward = collect($modalities)->filter(fn($m) => in_array($m['slug'], ['mc', 'om']))->values()->toArray();
                } elseif ($totalTeamCount === 8) {
                    $modalitiesToAward = collect($modalities)->filter(fn($m) => $m['slug'] === 'mc')->values()->toArray();
                }
            }

            if ($topPositions > 0 && !empty($modalitiesToAward)) {
                foreach ($modalitiesToAward as $mod) {
                    $modSlug = $mod['slug'];
                    $modLabel = $mod['label'];
                    $teamsSorted = $teamsCategory->sortByDesc(function ($t) use ($modSlug) {
                        $scores = $t->scores->where('modality_slug', $modSlug);
                        if ($modSlug === 'dp') {
                            return (float) $scores->max('total');
                        }
                        $first = $scores->first();
                        return $first ? (float) $first->total : 0;
                    })->values();

                    $teamsAfterExclusion = $modSlug !== 'dp'
                        ? $teamsSorted->reject(fn($t) => in_array($t->id, $generalPodiumIds))->values()
                        : $teamsSorted;

                    $remainingCount = $teamsAfterExclusion->count();
                    if ($remainingCount <= 1) continue;

                    $dynamicTop = $remainingCount <= 3
                        ? 1
                        : ($remainingCount === 4 ? 2 : min($topPositions, 3));

                    $modalityTop = $teamsAfterExclusion->take($dynamicTop)->values();
                    $modalityCount = $modalityTop->count();
                    for ($i = $modalityCount - 1; $i >= 0; $i--) {
                        $posNumber = $i + 1;
                        $slides[] = [
                            'type' => 'award',
                            'categoryLabel' => $catLabel,
                            'modalidadeLabel' => $modLabel,
                            'posNumber' => $posNumber,
                            'teamName' => null,
                        ];
                        $slides[] = [
                            'type' => 'award',
                            'categoryLabel' => $catLabel,
                            'modalidadeLabel' => $modLabel,
                            'posNumber' => $posNumber,
                            'teamName' => $modalityTop[$i]['name'],
                        ];
                    }
                }
            }

            if ($generalTopPositions > 0) {
                for ($i = $generalTopCount - 1; $i >= 0; $i--) {
                    $posNumber = $i + 1;
                    $slides[] = [
                        'type' => 'award',
                        'categoryLabel' => $catLabel,
                        'modalidadeLabel' => 'Nota Geral',
                        'posNumber' => $posNumber,
                        'teamName' => null,
                    ];
                    $slides[] = [
                        'type' => 'award',
                        'categoryLabel' => $catLabel,
                        'modalidadeLabel' => 'Nota Geral',
                        'posNumber' => $posNumber,
                        'teamName' => $teamsSortedTotal[$i]['name'],
                    ];
                }
            }
        }

        $slides[] = [
            'type' => 'thankyou',
            'message' => 'Muito obrigado pela participação!',
        ];

        $this->slides = $slides;
    }

    public function next()
    {
        if ($this->currentSlide < count($this->slides) - 1) {
            $this->currentSlide++;
        }
    }

    public function prev()
    {
        if ($this->currentSlide > 0) {
            $this->currentSlide--;
        }
    }

    public function render()
    {
        return view('livewire.page.slide-show')
        ->layout('layouts.app-tbr-public', [
            'title' => $this->title
        ]);
    }
}
