<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Client;
use App\Models\ClientPlan;
use App\Models\Plan;
use Laravel\Jetstream\InteractsWithBanner;

class ClientesClientPlans extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $linkId = null;
    public $client_id = '';
    public $plan_id = '';
    public $start_date = '';
    public $end_date = '';
    public $adjusted_value = '';
    public $active = true;
    public $perPage = 10;

    public $confirmingId = null;
    public $confirmingMessage = '';

    public $clients;
    public $plans;

    protected function rules()
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'plan_id' => ['required', 'exists:plans,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'adjusted_value' => ['nullable', 'numeric', 'min:0'],
            'active' => ['boolean'],
        ];
    }

    public function mount()
    {
        $clientsQuery = Client::where('active', true)->orderBy('name');
        if (!auth()->user()->isSuperAdmin()) {
            $clientsQuery->where('user_id', auth()->id());
        }
        $this->clients = $clientsQuery->get();
        $this->plans = Plan::where('active', true)->orderBy('name')->get();
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
        $link = ClientPlan::findOrFail($id);
        $clientQuery = Client::query();
        if (!auth()->user()->isSuperAdmin()) {
            $clientQuery->where('user_id', auth()->id());
        }
        $client = $clientQuery->find($link->client_id);
        if (!$client) abort(403);

        $this->linkId = $link->id;
        $this->client_id = $link->client_id;
        $this->plan_id = $link->plan_id;
        $this->start_date = $link->start_date?->format('Y-m-d');
        $this->end_date = $link->end_date?->format('Y-m-d');
        $this->adjusted_value = $link->adjusted_value;
        $this->active = $link->active;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $clientQuery = Client::query();
        if (!auth()->user()->isSuperAdmin()) {
            $clientQuery->where('user_id', auth()->id());
        }
        $client = $clientQuery->find($this->client_id);
        if (!$client) abort(403);

        $data = [
            'user_id' => auth()->id(),
            'client_id' => $this->client_id,
            'plan_id' => $this->plan_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'adjusted_value' => $this->adjusted_value ?: null,
            'active' => $this->active,
        ];

        if ($this->linkId) {
            ClientPlan::where('id', $this->linkId)->update($data);
        } else {
            ClientPlan::create($data);
        }

        $this->banner($this->linkId ? 'Vinculo atualizado!' : 'Plano vinculado ao cliente!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $link = ClientPlan::findOrFail($id);
        $clientQuery = Client::query();
        if (!auth()->user()->isSuperAdmin()) {
            $clientQuery->where('user_id', auth()->id());
        }
        $client = $clientQuery->find($link->client_id);
        if (!$client) abort(403);

        $this->confirmingId = $id;
        $this->confirmingMessage = 'Remover este vínculo? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        $link = ClientPlan::findOrFail($this->confirmingId);
        $clientQuery = Client::query();
        if (!auth()->user()->isSuperAdmin()) {
            $clientQuery->where('user_id', auth()->id());
        }
        $client = $clientQuery->find($link->client_id);
        if (!$client) abort(403);

        $link->delete();
        $this->banner('Vínculo removido!');
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
        $this->linkId = null;
        $this->client_id = '';
        $this->plan_id = '';
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = '';
        $this->adjusted_value = '';
        $this->active = true;
    }

    public function render()
    {
        $query = ClientPlan::join('clients', 'client_plan.client_id', '=', 'clients.id')
            ->join('plans', 'client_plan.plan_id', '=', 'plans.id')
            ->join('users', 'client_plan.user_id', '=', 'users.id')
            ->select('client_plan.*', 'clients.name as client_name', 'plans.name as plan_name', 'plans.value as plan_value', 'users.name as user_name')
            ->orderBy('client_plan.active', 'desc')
            ->orderBy('client_plan.created_at', 'desc');

        if (!auth()->user()->isSuperAdmin()) {
            $query->where('clients.user_id', auth()->id());
        }

        return view('livewire.page.clientes.client-plans', [
            'links' => $query->paginate($this->perPage),
        ])
            ->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Vincular Planos',
            ]);
    }
}
