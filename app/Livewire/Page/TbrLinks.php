<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Laravel\Jetstream\InteractsWithBanner;
use App\Models\Category;
use App\Models\Event;

class TbrLinks extends Component
{
    use InteractsWithBanner;

    public $title = 'TBR - Links';
    public $showSidebar = false;

    public $event_id;
    public $event;

    public array $categoriesWithModalities = [];

    public function mount($event_id)
    {
        $this->event_id = $event_id;
        $this->loadEvent();
        $this->categoriesWithModalities = $this->loadCategoriesAndModalities();
    }

    private function loadEvent()
    {
        $this->event = Event::with('teams')->find($this->event_id);

        if (!$this->event) {
            $this->dangerBanner('Evento não encontrado');
        }
    }

    public function loadCategoriesAndModalities()
    {
        if (!$this->event) return [];

        $teams = $this->event->teams;

        $categorySlugs = $teams->pluck('category_slug')->unique()->values();

        $configCategories = Category::orderBy('sort_order')->get()->map(fn($c) => [
            'id' => $c->id, 'slug' => $c->slug, 'label' => $c->label,
            'modalitie' => $c->modality_level, 'question' => $c->question_level, 'dp' => $c->dp_level,
        ]);
        $modalitiesByLevel = config('tbr-config.modalities_by_level');

        $filteredCategories = $configCategories->filter(function ($category) use ($categorySlugs) {
            return $categorySlugs->contains($category['slug']);
        })->values();

        $links = $filteredCategories->map(function ($category) use ($modalitiesByLevel) {
            $level = $category['modalitie'] ?? null;
            $modalities = $level && isset($modalitiesByLevel[$level])
                ? $modalitiesByLevel[$level]
                : [];

            $modalitiesFormatted = collect($modalities)->map(function ($modality) {
                return [
                    'id' => $modality['id'],
                    'name' => $modality['label'],
                ];
            })->values()->toArray();

            return [
                'category_name' => $category['label'],
                'category_id' => $category['id'],
                'modalities' => $modalitiesFormatted,
            ];
        })->values()->toArray();

        return [
            'event_id' => $this->event->id,
            'event' => $this->event->name,
            'links' => $links,
        ];
    }

    public function render()
    {
        return view('livewire.page.tbr.links', [
            'categoriesWithModalities' => $this->categoriesWithModalities,
            'status' => $this->event?->status,
        ])->layout('layouts.app-tbr-public', [
            'title' => $this->title,
        ]);
    }
}
