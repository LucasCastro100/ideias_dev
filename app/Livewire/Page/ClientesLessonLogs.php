<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Client;
use App\Models\LessonLog;
use Laravel\Jetstream\InteractsWithBanner;

class ClientesLessonLogs extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $logId = null;
    public $client_id = '';
    public $lesson_date = '';
    public $description = '';

    public $filterMonth;
    public $filterYear;
    public $filterDay = '';
    public $filterClientId = '';
    public $perPage = 10;

    public $confirmingId = null;
    public $confirmingMessage = '';

    public $clients;
    public $filterableClients;

    protected function rules()
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'lesson_date' => ['required', 'date'],
            'description' => ['required', 'string'],
        ];
    }

    private function userTeamId(): ?int
    {
        $team = auth()->user()->teams()->first();
        return $team ? (int) $team->id : null;
    }

    private function applyTeamFilter($query, string $table = '')
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return $query;
        }
        $teamId = $this->userTeamId();
        $column = $table ? "{$table}.team_id" : 'team_id';
        return $teamId ? $query->where($column, $teamId) : $query->whereRaw('1 = 0');
    }

    public function mount()
    {
        $clientsQuery = Client::where('active', true)->orderBy('name');
        $clientsQuery = $this->applyTeamFilter($clientsQuery);
        $this->clients = $clientsQuery->get();

        $filterQuery = Client::orderBy('name');
        $filterQuery = $this->applyTeamFilter($filterQuery);
        $this->filterableClients = $filterQuery->get(['id', 'name']);

        $this->lesson_date = now()->format('Y-m-d');
        $this->filterMonth = now()->month;
        $this->filterYear = now()->year;
    }

    public function updatedFilterMonth()
    {
        $this->resetPage();
    }

    public function updatedFilterYear()
    {
        $this->resetPage();
    }

    public function updatedFilterDay()
    {
        $this->resetPage();
    }

    public function updatedFilterClientId()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $query = LessonLog::query();
        $log = $this->applyTeamFilter($query)->findOrFail($id);
        $this->logId = $log->id;
        $this->client_id = $log->client_id;
        $this->lesson_date = $log->lesson_date->format('Y-m-d');
        $this->description = $log->description;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $existing = $this->logId ? LessonLog::find($this->logId) : null;

        LessonLog::updateOrCreate(
            ['id' => $this->logId],
            [
                'user_id' => $existing ? $existing->user_id : auth()->id(),
                'team_id' => $existing ? $existing->team_id : $this->userTeamId(),
                'client_id' => $this->client_id,
                'lesson_date' => $this->lesson_date,
                'description' => $this->description,
            ]
        );

        $this->banner($this->logId ? 'Registro atualizado!' : 'Registro cadastrado!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir este registro de aula? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        $query = LessonLog::query();
        $this->applyTeamFilter($query)->findOrFail($this->confirmingId)->delete();
        $this->banner('Registro excluído!');
        $this->confirmingId = null;
        $this->confirmingMessage = '';
    }

    public function cancelConfirmation()
    {
        $this->confirmingId = null;
        $this->confirmingMessage = '';
    }

    public function resetForm()
    {
        $this->logId = null;
        $this->client_id = '';
        $this->lesson_date = now()->format('Y-m-d');
        $this->description = '';
    }

    public function render()
    {
        $query = LessonLog::whereYear('lesson_date', $this->filterYear)
            ->whereMonth('lesson_date', $this->filterMonth)
            ->with('client', 'user', 'team')
            ->orderBy('lesson_date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($this->filterDay) {
            $query->whereDay('lesson_date', $this->filterDay);
        }

        $query = $this->applyTeamFilter($query, 'lesson_logs');

        if (auth()->user()->isSuperAdmin() && $this->filterClientId) {
            $query->where('lesson_logs.client_id', $this->filterClientId);
        }

        return view('livewire.page.clientes.lesson-logs', [
            'logs' => $query->paginate($this->perPage),
        ])
            ->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Registro de Aulas',
            ]);
    }
}
