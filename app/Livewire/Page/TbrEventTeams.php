<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\Category;
use App\Models\Event;
use App\Models\Team;

class TbrEventTeams extends Component
{
    public $title = 'TBR - Equipes';

    public ?string $eventId = null;
    public ?string $eventName = null;

    #[Url(as: 'equipe', history: true)]
    public ?string $selectedTeamId = null;

    public ?string $filterCategory = null;

    public bool $showEditModal = false;
    public ?string $editTeamId = null;
    public string $editTeamName = '';
    public string $editTeamCategory = '';
    public string $editTeamRepresentative = '';
    public string $editTeamEmail = '';
    public string $editTeamPhone = '';

    public bool $showDeleteModal = false;
    public ?string $deleteTeamId = null;

    public function mount(?string $event_id = null): void
    {
        if ($event_id) {
            $event = Event::find($event_id);
            if ($event) {
                $this->eventId = $event->id;
                $this->eventName = $event->name;
            }
        }
    }

    public function selectTeam(string $teamId): void
    {
        $this->selectedTeamId = $teamId;
    }

    public function backToList(): void
    {
        $this->selectedTeamId = null;
    }

    public function openEditModal(string $teamId): void
    {
        $this->authorize('edit');

        $team = Team::findOrFail($teamId);
        $this->editTeamId = $team->id;
        $this->editTeamName = $team->name;
        $this->editTeamCategory = $team->category_slug;
        $this->editTeamRepresentative = $team->representative_name ?? '';
        $this->editTeamEmail = $team->representative_email ?? '';
        $this->editTeamPhone = $team->representative_phone ?? '';
        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->editTeamId = null;
        $this->editTeamName = '';
        $this->editTeamCategory = '';
        $this->editTeamRepresentative = '';
        $this->editTeamEmail = '';
        $this->editTeamPhone = '';
    }

    public function updateTeam(): void
    {
        $this->authorize('edit');

        $this->validate([
            'editTeamName' => 'required|string|max:255',
            'editTeamCategory' => 'required|string',
        ]);

        Team::findOrFail($this->editTeamId)->update([
            'name' => $this->editTeamName,
            'category_slug' => $this->editTeamCategory,
            'representative_name' => $this->editTeamRepresentative ?: null,
            'representative_email' => $this->editTeamEmail ?: null,
            'representative_phone' => $this->editTeamPhone ?: null,
        ]);

        $this->closeEditModal();
    }

    public function openDeleteModal(string $teamId): void
    {
        $this->authorize('delete');

        $this->deleteTeamId = $teamId;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deleteTeamId = null;
    }

    public function deleteTeam(): void
    {
        $this->authorize('delete');

        Team::findOrFail($this->deleteTeamId)->delete();
        $this->closeDeleteModal();
    }

    public function render()
    {
        $teams = [];
        $selectedTeam = null;

        if ($this->eventId) {
            $query = Team::where('event_id', $this->eventId)
                ->with('scores')
                ->orderBy('name');

            if ($this->filterCategory) {
                $query->where('category_slug', $this->filterCategory);
            }

            if ($this->selectedTeamId) {
                $selectedTeam = (clone $query)->where('id', $this->selectedTeamId)->first();
            }

            $teams = $query->get();
        }

        $categories = Category::orderBy('sort_order')->get(['slug', 'label']);

        $categoryCounts = [];
        if ($this->eventId) {
            $categoryCounts = Team::where('event_id', $this->eventId)
                ->selectRaw('category_slug, count(*) as total')
                ->groupBy('category_slug')
                ->pluck('total', 'category_slug')
                ->toArray();
        }

        return view('livewire.page.tbr.event-teams', [
            'teams' => $teams,
            'selectedTeam' => $selectedTeam,
            'categories' => $categories,
            'categoryCounts' => $categoryCounts,
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => $this->title,
        ]);
    }
}
