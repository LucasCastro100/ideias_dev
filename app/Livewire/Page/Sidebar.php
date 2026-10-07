<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Event;
use App\Models\Team;
use App\Models\User;
use App\Services\IbgeService;

class Sidebar extends Component
{
    public bool $showEventModal = false;
    public bool $showTeamModal = false;
    public bool $confirmClearStorage = false;

    public $eventName = '';
    public $eventDate = '';
    public $tipoEvento = 'interno';

    public $selectedEventId = null;
    public $numTeams = 1;
    public $teams = [
        [
            'name' => '',
            'category' => 'baby',
            'representative_user_id' => '',
            'representative_name' => '',
            'representative_email' => '',
            'representative_phone' => '',
        ],
    ];

    public $events = [];
    public $allUsers = [];

    public $selectedRegion = null;
    public $selectedRegionId = null;
    public $selectedState = null;
    public $selectedStateId = null;
    public $selectedCity = null;
    public $selectedCityId = null;

    public $regions = [];
    public $filteredStates = [];
    public $filteredCities = [];

    protected IbgeService $ibge;

    public function boot(IbgeService $ibge)
    {
        $this->ibge = $ibge;
    }

    public function mount()
    {
        $this->regions = $this->ibge->getRegions();
        $this->filteredStates = [];
        $this->filteredCities = [];
        $this->resetLocation();
    }

    private function resetLocation()
    {
        $this->selectedRegion = null;
        $this->selectedRegionId = null;
        $this->selectedState = null;
        $this->selectedStateId = null;
        $this->selectedCity = null;
        $this->selectedCityId = null;
        $this->filteredStates = [];
        $this->filteredCities = [];
    }

    public function updatedSelectedRegion($value)
    {
        $region = collect($this->regions)->firstWhere('nome', $value);
        $this->selectedRegionId = $region['id'] ?? null;
        $this->selectedState = null;
        $this->selectedStateId = null;
        $this->selectedCity = null;
        $this->selectedCityId = null;
        $this->filteredStates = $this->selectedRegionId
            ? $this->ibge->getStatesByRegion($this->selectedRegionId)
            : [];
    }

    public function updatedSelectedState($value)
    {
        $state = collect($this->filteredStates)->firstWhere('nome', $value);
        $this->selectedStateId = $state['id'] ?? null;
        $this->selectedCity = null;
        $this->selectedCityId = null;
        $this->filteredCities = $this->selectedStateId
            ? $this->ibge->getCitiesByState($this->selectedStateId)
            : [];
    }

    public function updatedSelectedCity($value)
    {
        $city = collect($this->filteredCities)->firstWhere('nome', $value);
        $this->selectedCityId = $city['id'] ?? null;
    }

    #[On('openEventModal')]
    public function openEventModal()
    {
        $this->authorize('create');
        $this->loadEvents();
        $this->showEventModal = true;
    }

    public function closeEventModal()
    {
        $this->showEventModal = false;
        $this->resetEventForm();
    }

    public function resetEventForm()
    {
        $this->eventName = '';
        $this->eventDate = '';
        $this->tipoEvento = 'interno';
        $this->selectedRegion = null;
        $this->selectedState = null;
        $this->selectedCity = null;
        $this->filteredStates = [];
        $this->filteredCities = [];
        $this->resetErrorBag('eventName');
        $this->resetErrorBag('eventDate');
        $this->resetErrorBag('selectedRegion');
        $this->resetErrorBag('selectedState');
        $this->resetErrorBag('selectedCity');
    }

    public function saveEvent()
    {
        $this->authorize('create');
        $this->validate([
            'eventName' => 'required|string|min:2',
            'eventDate' => 'required|date',
        ], [
            'eventName.required' => 'O nome do evento é obrigatório.',
            'eventName.min' => 'O nome deve ter pelo menos 2 caracteres.',
            'eventDate.required' => 'A data do evento é obrigatória.',
            'eventDate.date' => 'Informe uma data válida.',
        ]);

        $exists = Event::where('name', $this->eventName)
            ->where('date', $this->eventDate)
            ->exists();

        if ($exists) {
            $this->addError('eventName', 'Evento já cadastrado com este nome e data.');
            return;
        }

        Event::create([
            'id' => Str::upper(Str::random(12)),
            'name' => $this->eventName,
            'date' => $this->eventDate,
            'status' => false,
            'tipo_evento' => $this->tipoEvento,
            'location' => [
                'regiao' => $this->selectedRegion
                    ? [
                        'id' => $this->selectedRegionId,
                        'sigla' => collect($this->regions)->firstWhere('id', $this->selectedRegionId)['sigla'] ?? null,
                        'nome' => $this->selectedRegion,
                    ]
                    : null,
                'estado' => $this->selectedState
                    ? [
                        'id' => $this->selectedStateId,
                        'sigla' => collect($this->filteredStates)->firstWhere('id', $this->selectedStateId)['sigla'] ?? null,
                        'nome' => $this->selectedState,
                    ]
                    : null,
                'municipio' => $this->selectedCity
                    ? [
                        'id' => $this->selectedCityId,
                        'nome' => $this->selectedCity,
                    ]
                    : null,
            ],
        ]);

        $this->dispatch('eventCreated');
        $this->closeEventModal();

        $this->dispatch('toast-message', message: 'Evento cadastrado com sucesso!', style: 'success');
    }

    #[On('openTeamModal')]
    public function openTeamModal()
    {
        $this->authorize('create');
        $rawEvents = Event::where('status', false)->orderBy('date', 'desc')->get();
        $this->events = $rawEvents->map(fn($e) => [
            'id' => $e->id,
            'name' => $e->name,
            'date' => $e->date,
            'date_formatted' => \Carbon\Carbon::parse($e->date)->format('d/m/y'),
            'display' => $e->name . ' (' . \Carbon\Carbon::parse($e->date)->format('d/m/y') . ')',
        ])->toArray();
        $this->allUsers = User::where('id', '!=', 1)->orderBy('name')->get(['id', 'name', 'email']);
        if (empty($this->events)) {
            $this->dispatch('toast-message', message: 'Você precisa cadastrar pelo menos um evento antes de adicionar equipes.', style: 'warning');
            return;
        }
        $this->showTeamModal = true;
    }

    public function closeTeamModal()
    {
        $this->showTeamModal = false;
        $this->resetTeamForm();
    }

    public function resetTeamForm()
    {
        $this->numTeams = 1;
        $defaultCategorySlug = Category::orderBy('sort_order')->value('slug') ?? 'baby';
        $this->teams = [
            [
                'name' => '',
                'category' => $defaultCategorySlug,
                'representative_name' => '',
                'representative_email' => '',
                'representative_phone' => '',
            ],
        ];
        $this->selectedEventId = null;
        $this->resetErrorBag('teams');
        $this->resetErrorBag('selectedEventId');
    }

    public function updatedNumTeams($value)
    {
        $value = (int)$value;
        if ($value < 1) $value = 1;

        $count = count($this->teams);
        $defaultCategorySlug = Category::orderBy('sort_order')->value('slug') ?? 'baby';

        if ($value > $count) {
            for ($i = $count; $i < $value; $i++) {
                $this->teams[] = [
                    'name' => '',
                    'category' => $defaultCategorySlug,
                    'representative_user_id' => '',
                    'representative_name' => '',
                    'representative_email' => '',
                    'representative_phone' => '',
                ];
            }
        } elseif ($value < $count) {
            $this->teams = array_slice($this->teams, 0, $value);
        }
    }

    public function fillTeamRepresentative(int $index, string $userId): void
    {
        if (!$userId || !isset($this->teams[$index])) return;

        $user = User::find($userId);
        if ($user) {
            $this->teams[$index]['representative_name'] = $user->name;
            $this->teams[$index]['representative_email'] = $user->email;
        }
    }

    public function saveTeams()
    {
        $this->authorize('create');
        $categories = Category::orderBy('sort_order')->get();
        $modalitiesByLevel = config('tbr-config.modalities_by_level') ?? [];

        $categorySlugs = $categories->pluck('slug')->toArray();
        $categoryLevels = $categories->mapWithKeys(fn($cat) => [$cat['slug'] => $cat['modality_level'] ?? 'basic'])->toArray();

        $this->validate([
            'selectedEventId' => 'required',
            'teams.*.name' => 'required|min:2',
            'teams.*.category' => 'required|in:' . implode(',', $categorySlugs),
        ], [
            'selectedEventId.required' => 'Selecione um evento.',
            'teams.*.name.required' => 'O nome da equipe é obrigatório.',
            'teams.*.name.min' => 'O nome da equipe deve ter pelo menos 2 caracteres.',
            'teams.*.category.required' => 'Selecione uma categoria.',
        ]);

        $event = Event::find($this->selectedEventId);
        if (!$event) {
            $this->addError('selectedEventId', 'Evento selecionado inválido.');
            return;
        }

        foreach ($this->teams as $teamData) {
            $teamNameLower = strtolower($teamData['name']);

            $exists = $event->teams()->whereRaw('LOWER(name) = ?', [$teamNameLower])->exists();

            if (!$exists) {
                $categorySlug = $teamData['category'];
                $modalitieLevel = $categoryLevels[$categorySlug] ?? 'basic';

                $team = $event->teams()->create([
                    'id' => Str::upper(Str::random(12)),
                    'name' => $teamData['name'],
                    'category_slug' => $categorySlug,
                    'total_score' => 0,
                    'representative_name' => $teamData['representative_name'] ?? null,
                    'representative_email' => $teamData['representative_email'] ?? null,
                    'representative_phone' => $teamData['representative_phone'] ?? null,
                ]);

                foreach ($modalitiesByLevel[$modalitieLevel] ?? [] as $modality) {
                    $slug = is_array($modality) ? ($modality['slug'] ?? null) : $modality;
                    if (!$slug || !is_string($slug)) continue;

                    if ($slug === 'dp') {
                        $team->scores()->createMany([
                            ['modality_slug' => 'dp', 'round' => 'r1', 'scores' => [], 'total' => 0, 'comment' => ''],
                            ['modality_slug' => 'dp', 'round' => 'r2', 'scores' => [], 'total' => 0, 'comment' => ''],
                            ['modality_slug' => 'dp', 'round' => 'r3', 'scores' => [], 'total' => 0, 'comment' => ''],
                        ]);
                    } else {
                        $team->scores()->create([
                            'modality_slug' => $slug,
                            'round' => null,
                            'scores' => [],
                            'total' => 0,
                            'comment' => '',
                        ]);
                    }
                }
            }
        }

        $this->dispatch('toast-message', message: 'Equipes cadastradas com sucesso!', style: 'success');
        $this->closeTeamModal();
        $this->dispatch('teams-updated');
    }

    public function askToClearStorage()
    {
        $this->confirmClearStorage = true;
    }

    public function cancelClearStorage()
    {
        $this->confirmClearStorage = false;
    }

    public function confirmAndClearStorage()
    {
        $this->authorize('delete');
        $this->clearStorage();
        $this->confirmClearStorage = false;
    }

    public function loadEvents()
    {
        $this->events = Event::orderBy('date', 'desc')->get()->toArray();
        return $this->events;
    }

    public function clearStorage()
    {
        Event::query()->delete();

        $this->resetEventForm();
        $this->resetTeamForm();

        $this->dispatch('toast-message', message: 'Dados apagados e formulários resetados!', style: 'success');
        $this->dispatch('eventCreated');
    }

    public function render()
    {
        return view('livewire.page.sidebar', [
            'events' => $this->events,
            'categories' => Category::orderBy('sort_order')->get()->toArray(),
        ]);
    }
}
