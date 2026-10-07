<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Client;
use App\Models\LessonLog;
use App\Models\ClientAccount;
use Laravel\Jetstream\InteractsWithBanner;

class ClientesClients extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $showHistoryModal = false;
    public $historyClientId = null;
    public $historyClientName = '';
    public $historyLogs = [];
    public $historyAccounts = [];
    public $clientId = null;
    public $name = '';
    public $email = '';
    public $phone = '';
    public $document = '';
    public $cep = '';
    public $sexo = '';
    public $birthDate = '';
    public $endereco = '';
    public $numero = '';
    public $complemento = '';
    public $bairro = '';
    public $cidade = '';
    public $uf = '';
    public $notes = '';

    public $search = '';
    public $perPage = 10;
    public $sortField = 'name';
    public $sortDirection = 'asc';

    public $confirmingId = null;
    public $confirmingAction = null;
    public $confirmingMessage = '';

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'document' => ['nullable', 'string', 'max:20'],
            'cep' => ['nullable', 'string', 'max:10'],
            'sexo' => ['nullable', 'string', 'in:M,F'],
            'birthDate' => ['nullable', 'date'],
            'endereco' => ['nullable', 'string'],
            'numero' => ['nullable', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:100'],
            'bairro' => ['nullable', 'string', 'max:100'],
            'cidade' => ['nullable', 'string', 'max:100'],
            'uf' => ['nullable', 'string', 'max:2'],
            'notes' => ['nullable', 'string'],
        ];
    }

    private function userTeamId(): ?int
    {
        $team = auth()->user()->teams()->first();
        return $team ? (int) $team->id : null;
    }

    private function applyTeamFilter($query)
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return $query;
        }
        $teamId = $this->userTeamId();
        return $teamId ? $query->where('team_id', $teamId) : $query->whereRaw('1 = 0');
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    protected function clientsQuery()
    {
        $query = Client::with('user', 'team')
            ->withSum(['accounts as total_revenue' => function ($q) {
                $q->where('paid', true);
            }], 'value')
            ->orderBy('active', 'desc');

        $allowedFields = ['name', 'birth_date'];
        if (in_array($this->sortField, $allowedFields)) {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        $query = $this->applyTeamFilter($query);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%")
                  ->orWhere('document', 'like', "%{$this->search}%");
            });
        }

        return $query;
    }

    public function updatedSearch()
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
        $client = Client::findOrFail($id);
        $this->applyTeamFilter(Client::where('id', $id))->firstOrFail();
        $this->clientId = $client->id;
        $this->name = $client->name;
        $this->email = $client->email;
        $this->phone = $client->phone;
        $this->document = $client->document;
        $this->cep = $client->cep;
        $this->sexo = $client->sexo;
        $this->birthDate = $client->birth_date?->format('Y-m-d');
        $this->endereco = $client->endereco;
        $this->numero = $client->numero;
        $this->complemento = $client->complemento;
        $this->bairro = $client->bairro;
        $this->cidade = $client->cidade;
        $this->uf = $client->uf;
        $this->notes = $client->notes;
        $this->showModal = true;
    }

    public function openHistory($id)
    {
        $client = $this->applyTeamFilter(Client::where('id', $id))->firstOrFail();
        $this->historyClientId = $client->id;
        $this->historyClientName = $client->name;

        $this->historyLogs = LessonLog::where('client_id', $client->id)
            ->with('user')
            ->orderBy('lesson_date', 'desc')
            ->limit(20)
            ->get()
            ->toArray();

        $this->historyAccounts = ClientAccount::where('client_id', $client->id)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(20)
            ->get()
            ->toArray();

        $this->showHistoryModal = true;
    }

    public function closeHistory()
    {
        $this->showHistoryModal = false;
        $this->historyClientId = null;
        $this->historyClientName = '';
        $this->historyLogs = [];
        $this->historyAccounts = [];
    }

    public function save()
    {
        $this->validate();

        Client::updateOrCreate(
            ['id' => $this->clientId],
            [
                'user_id' => $this->clientId ? Client::find($this->clientId)->user_id : auth()->id(),
                'team_id' => $this->clientId ? Client::find($this->clientId)->team_id : $this->userTeamId(),
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'document' => $this->document,
                'cep' => $this->cep ?: null,
                'sexo' => $this->sexo ?: null,
                'birth_date' => $this->birthDate ?: null,
                'endereco' => $this->endereco ?: null,
                'numero' => $this->numero ?: null,
                'complemento' => $this->complemento ?: null,
                'bairro' => $this->bairro ?: null,
                'cidade' => $this->cidade ?: null,
                'uf' => $this->uf ?: null,
                'notes' => $this->notes,
            ]
        );

        $this->banner($this->clientId ? 'Cliente atualizado!' : 'Cliente cadastrado!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmAction($id, $action)
    {
        $client = $this->applyTeamFilter(Client::where('id', $id))->firstOrFail();
        $this->confirmingId = $id;
        $this->confirmingAction = $action;
        $this->confirmingMessage = $action === 'deactivate'
            ? "Desativar {$client->name}? As contas pendentes serão baixadas automaticamente."
            : "Excluir {$client->name}? Esta ação não pode ser desfeita.";
    }

    public function executeAction()
    {
        $id = $this->confirmingId;
        $client = $this->applyTeamFilter(Client::where('id', $id))->firstOrFail();

        if ($this->confirmingAction === 'deactivate') {
            \DB::transaction(function () use ($client) {
                $client->update(['active' => false, 'deactivated_at' => now()]);
                $client->plans()->wherePivot('active', true)->update(['active' => false]);
                $client->accounts()->where('paid', false)->whereNull('cancelled_at')->update([
                    'cancelled_at' => now(),
                ]);
            });

            $this->banner('Cliente desativado!');
        } elseif ($this->confirmingAction === 'delete') {
            $client->delete();
            $this->banner('Cliente excluído!');
        }

        $this->confirmingId = null;
        $this->confirmingAction = null;
        $this->confirmingMessage = '';
    }

    public function cancelConfirmation()
    {
        $this->confirmingId = null;
        $this->confirmingAction = null;
        $this->confirmingMessage = '';
    }

    public function activate($id)
    {
        $client = $this->applyTeamFilter(Client::where('id', $id))->firstOrFail();
        $client->update(['active' => true, 'deactivated_at' => null]);
        $this->banner('Cliente ativado!');
    }

    public function resetForm()
    {
        $this->clientId = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->document = '';
        $this->cep = '';
        $this->sexo = '';
        $this->birthDate = '';
        $this->endereco = '';
        $this->numero = '';
        $this->complemento = '';
        $this->bairro = '';
        $this->cidade = '';
        $this->uf = '';
        $this->notes = '';
    }

    public function render()
    {
        $user = auth()->user();
        $baseQuery = $this->applyTeamFilter(Client::query());
        $totalM = (clone $baseQuery)->where('sexo', 'M')->count();
        $totalF = (clone $baseQuery)->where('sexo', 'F')->count();

        return view('livewire.page.clientes.clients', [
            'clients' => $this->clientsQuery()->paginate($this->perPage),
            'totalM' => $totalM,
            'totalF' => $totalF,
        ])
            ->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Clientes',
            ]);
    }
}
