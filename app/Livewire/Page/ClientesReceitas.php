<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Client;
use App\Models\ClientAccount;
use App\Models\LessonLog;
use App\Models\Plan;
use Illuminate\Support\Str;
use Laravel\Jetstream\InteractsWithBanner;

class ClientesReceitas extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $showHistoryModal = false;
    public $historyClientId = null;
    public $historyClientName = '';
    public $historyLogs = [];
    public $historyAccounts = [];
    public $receitaId = null;
    public $client_id = '';
    public $plan_id = '';
    public $value = '';
    public $month = '';
    public $year = '';
    public $due_date = '';
    public $paid = true;
    public $recurring = false;
    public $recurring_until_month = '';
    public $recurring_until_year = '';

    public $filterMonth;
    public $filterYear;
    public $perPage = 10;

    public $confirmingId = null;
    public $confirmingMessage = '';

    public $clients;
    public $plans;

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
        $this->plans = Plan::where('active', true)->orderBy('name')->get();
        $this->month = now()->month;
        $this->year = now()->year;
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

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedPlanId($id)
    {
        if ($id) {
            $plan = Plan::find($id);
            if ($plan) {
                $this->value = $plan->value;
            }
        } else {
            $this->value = '';
        }
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $query = ClientAccount::whereNotNull('client_id');
        $receita = $this->applyTeamFilter($query)->findOrFail($id);
        $this->receitaId = $receita->id;
        $this->client_id = $receita->client_id;
        $this->plan_id = $receita->plan_id;
        $this->value = $receita->value;
        $this->month = $receita->month;
        $this->year = $receita->year;
        $this->due_date = $receita->due_date?->format('Y-m-d');
        $this->paid = $receita->paid;
        $this->recurring = !is_null($receita->recurring_group_id);
        $this->recurring_until_month = $receita->recurring_until_month ?? 12;
        $this->recurring_until_year = $receita->recurring_until_year ?? $this->year;
        $this->showModal = true;
    }

    public function openHistory($clientId)
    {
        $client = $this->applyTeamFilter(Client::where('id', $clientId))->firstOrFail();
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
        $this->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'value' => ['required', 'numeric', 'min:0'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2020'],
            'due_date' => ['nullable', 'date'],
        ]);

        $clientQuery = Client::query();
        $client = $this->applyTeamFilter($clientQuery)->find($this->client_id);
        if (!$client) abort(403);

        $dueDate = $this->due_date ?: now()->createFromDate($this->year, $this->month, 1)->endOfMonth()->format('Y-m-d');

        $recurringGroupId = $this->recurring ? ((string) Str::uuid()) : null;
        $endMonth = $this->recurring ? (int) ($this->recurring_until_month ?: 12) : null;
        $endYear = $this->recurring ? (int) ($this->recurring_until_year ?: $this->year) : null;

        $existing = $this->receitaId ? ClientAccount::find($this->receitaId) : null;

        ClientAccount::updateOrCreate(
            ['id' => $this->receitaId],
            [
                'user_id' => $existing ? $existing->user_id : auth()->id(),
                'team_id' => $existing ? $existing->team_id : $this->userTeamId(),
                'client_id' => $this->client_id,
                'plan_id' => $this->plan_id ?: null,
                'value' => $this->value,
                'month' => $this->month,
                'year' => $this->year,
                'due_date' => $dueDate,
                'paid_date' => $this->paid ? $dueDate : null,
                'paid' => $this->paid,
                'description' => 'Recebimento - ' . $client->name,
                'recurring_group_id' => $recurringGroupId,
                'recurring_until_month' => $endMonth,
                'recurring_until_year' => $endYear,
            ]
        );

        if ($this->recurring) {
            $startMonth = (int) $this->month + 1;
            $startYear = (int) $this->year;
            if ($startMonth > 12) {
                $startMonth = 1;
                $startYear++;
            }

            $teamId = $this->userTeamId();
            $created = 0;
            $currentMonth = $startMonth;
            $currentYear = $startYear;

            while ($currentYear < $endYear || ($currentYear === $endYear && $currentMonth <= $endMonth)) {
                $existsQuery = ClientAccount::where('client_id', $this->client_id)
                    ->where('month', $currentMonth)
                    ->where('year', $currentYear);
                $existsQuery = $this->applyTeamFilter($existsQuery);
                if (!$existsQuery->exists()) {
                    ClientAccount::create([
                        'user_id' => auth()->id(),
                        'team_id' => $teamId,
                        'client_id' => $this->client_id,
                        'plan_id' => $this->plan_id ?: null,
                        'value' => $this->value,
                        'month' => $currentMonth,
                        'year' => $currentYear,
                        'due_date' => now()->createFromDate($currentYear, $currentMonth, 1)->endOfMonth()->format('Y-m-d'),
                        'paid_date' => $this->paid ? now()->createFromDate($currentYear, $currentMonth, 1)->endOfMonth()->format('Y-m-d') : null,
                        'paid' => $this->paid,
                        'description' => 'Recebimento - ' . $client->name,
                        'recurring_group_id' => $recurringGroupId,
                        'recurring_until_month' => $endMonth,
                        'recurring_until_year' => $endYear,
                    ]);
                    $created++;
                }

                $currentMonth++;
                if ($currentMonth > 12) {
                    $currentMonth = 1;
                    $currentYear++;
                }
            }

            $this->banner($this->receitaId
                ? "Receita atualizada! Mais {$created} meses gerados até {$endMonth}/{$endYear}."
                : "Receita cadastrada! Mais {$created} meses gerados até {$endMonth}/{$endYear}.");
        } else {
            $this->banner($this->receitaId ? 'Receita atualizada!' : 'Receita cadastrada!');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir esta receita? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        $query = ClientAccount::whereNotNull('client_id');
        $this->applyTeamFilter($query)->findOrFail($this->confirmingId)->delete();
        $this->banner('Receita excluída!');
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
        $this->receitaId = null;
        $this->client_id = '';
        $this->plan_id = '';
        $this->value = '';
        $this->month = now()->month;
        $this->year = now()->year;
        $this->due_date = '';
        $this->paid = false;
        $this->recurring = false;
        $this->recurring_until_month = 12;
        $this->recurring_until_year = now()->year;
    }

    public function render()
    {
        $query = ClientAccount::whereNotNull('client_id')
            ->where('month', $this->filterMonth)
            ->where('year', $this->filterYear)
            ->with('client', 'user', 'team')
            ->orderBy('due_date', 'desc');

        $query = $this->applyTeamFilter($query);

        return view('livewire.page.clientes.receitas', [
            'receitas' => $query->paginate($this->perPage),
        ])
            ->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Receitas',
            ]);
    }
}
