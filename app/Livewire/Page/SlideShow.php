<?php

namespace App\Livewire\Page;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class SlideShow extends Component
{
    public $slides = [];
    public $currentSlide = 0;
    public $showSidebar = true;

    public function mount($event_id)
    {
        $json = Storage::disk('public')->get('tbr/json/data.json');
        $events = json_decode($json, true);
        $event = collect($events)->firstWhere('id', $event_id);

        if (!$event) {
            abort(404, 'Evento não encontrado');
        }

        $categories = config('tbr-config.categories');
        $modalitiesByLevel = config('tbr-config.modalities_by_level');
        $rankingConfig = $event['ranking_config'] ?? [];

        $topPositions = (int)($rankingConfig['top_positions'] ?? 0);
        $generalTopPositions = (int)($rankingConfig['general_top_positions'] ?? 3);

        $slides = [];

        // ➕ Slide inicial com nome do evento
        $slides[] = [
            'type' => 'intro',
            'title' => $event['nome'] ?? 'Evento',
        ];

        foreach ($categories as $category) {
            $catSlug = $category['slug'];
            $catLabel = $category['label'];
            $modalitieLevel = $category['modalitie'];
            $modalities = $modalitiesByLevel[$modalitieLevel] ?? [];

            $teamsCategory = collect($event['equipes'] ?? [])
                ->filter(fn($t) => ($t['category'] ?? '') === $catSlug)
                ->values();

            if ($teamsCategory->isEmpty()) continue;

            if ($catSlug === 'baby') {
                $teamsSortedTotal = $teamsCategory->sortByDesc(fn($t) => floatval($t['nota_total'] ?? 0))->values();
                $count = min($generalTopPositions, $teamsSortedTotal->count());

                for ($pos = 0; $pos < $count; $pos++) {
                    $posNumber = $count - $pos;

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
                        'teamName' => $teamsSortedTotal[$pos]['name'],
                    ];
                }

                continue;
            }

            if ($topPositions > 0) {
                foreach ($modalities as $mod) {
                    $modSlug = $mod['slug'];
                    $modLabel = $mod['label'];

                    $teamsSorted = $teamsCategory->sortByDesc(fn($t) => $t['modalities'][$modSlug]['total'] ?? 0)->values();
                    $count = min($topPositions, $teamsSorted->count());

                    for ($pos = 0; $pos < $count; $pos++) {
                        $posNumber = $count - $pos;

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
                            'teamName' => $teamsSorted[$pos]['name'],
                        ];
                    }
                }
            }

            if ($generalTopPositions > 0 && $catSlug !== 'baby') {
                $teamsSortedTotal = $teamsCategory->sortByDesc(fn($t) => floatval($t['nota_total'] ?? 0))->values();
                $countTotal = min($generalTopPositions, $teamsSortedTotal->count());

                for ($pos = 0; $pos < $countTotal; $pos++) {
                    $posNumber = $countTotal - $pos;

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
                        'teamName' => $teamsSortedTotal[$pos]['name'],
                    ];
                }
            }
        }

        // ➕ Slide final de agradecimento
        $slides[] = [
            'type' => 'thankyou',
            'message' => 'Muito obrigado pela participação!',
        ];

        $this->slides = $slides;
        $this->showSidebar = false;
    }

    public function next()
    {
        $this->currentSlide = ($this->currentSlide + 1) % count($this->slides);
    }

    public function prev()
    {
        $this->currentSlide = ($this->currentSlide - 1 + count($this->slides)) % count($this->slides);
    }

    public function render()
    {
        return view('livewire.page.slide-show')->layout('layouts.app-sidebar', ['showSidebar' => $this->showSidebar]);
    }
}
