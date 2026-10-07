<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ClientAccount;
use Illuminate\Support\Str;
use Laravel\Jetstream\InteractsWithBanner;

class ClientesAccounts extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $accountId = null;
    public $description = '';
    public $value = '';
    public $month = '';
    public $year = '';
    public $notes = '';
    public $recurring = false;
    public $recurring_until_month = '';
    public $recurring_until_year = '';

    public $filterMonth;
    public $filterYear;
    public $perPage = 10;

    public $confirmingId = null;
    public $confirmingMessage = '';

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

    public function mount()
    {
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

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $query = ClientAccount::query();
        $account = $this->applyTeamFilter($query)->findOrFail($id);

        $this->accountId = $account->id;
        $this->description = $account->description;
        $this->value = $account->value;
        $this->month = $account->month;
        $this->year = $account->year;
        $this->notes = $account->notes;
        $this->recurring = !is_null($account->recurring_group_id);
        $this->recurring_until_month = $account->recurring_until_month ?? 12;
        $this->recurring_until_year = $account->recurring_until_year ?? $this->year;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'description' => ['required', 'string', 'max:255'],
            'value' => ['required', 'numeric'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2020'],
            'notes' => ['nullable', 'string'],
        ]);

        $dueDate = now()->createFromDate($this->year, $this->month, 1)->endOfMonth()->format('Y-m-d');

        $recurringGroupId = $this->recurring ? ((string) Str::uuid()) : null;
        $endMonth = $this->recurring ? (int) ($this->recurring_until_month ?: 12) : null;
        $endYear = $this->recurring ? (int) ($this->recurring_until_year ?: $this->year) : null;

        $existing = $this->accountId ? ClientAccount::find($this->accountId) : null;

        ClientAccount::updateOrCreate(
            ['id' => $this->accountId],
            [
                'user_id' => $existing ? $existing->user_id : auth()->id(),
                'team_id' => $existing ? $existing->team_id : $this->userTeamId(),
                'client_id' => null,
                'description' => $this->description,
                'value' => $this->value,
                'due_date' => $dueDate,
                'month' => $this->month,
                'year' => $this->year,
                'paid_date' => $dueDate,
                'paid' => true,
                'notes' => $this->notes,
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
                $existsQuery = ClientAccount::whereNull('client_id')
                    ->where('month', $currentMonth)
                    ->where('year', $currentYear);
                $existsQuery = $this->applyTeamFilter($existsQuery);
                if (!$existsQuery->exists()) {
                    ClientAccount::create([
                        'user_id' => auth()->id(),
                        'team_id' => $teamId,
                        'client_id' => null,
                        'description' => $this->description,
                        'value' => $this->value,
                        'due_date' => now()->createFromDate($currentYear, $currentMonth, 1)->endOfMonth()->format('Y-m-d'),
                        'paid_date' => now()->createFromDate($currentYear, $currentMonth, 1)->endOfMonth()->format('Y-m-d'),
                        'paid' => true,
                        'month' => $currentMonth,
                        'year' => $currentYear,
                        'notes' => $this->notes,
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

            $this->banner($this->accountId
                ? "Gasto atualizado! Mais {$created} meses gerados até {$endMonth}/{$endYear}."
                : "Gasto cadastrado! Mais {$created} meses gerados até {$endMonth}/{$endYear}.");
        } else {
            $this->banner($this->accountId ? 'Gasto atualizado!' : 'Gasto cadastrado!');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir este gasto? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        $query = ClientAccount::query();
        $this->applyTeamFilter($query)->findOrFail($this->confirmingId)->delete();
        $this->banner('Gasto excluído!');
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
        $this->accountId = null;
        $this->description = '';
        $this->value = '';
        $this->month = now()->month;
        $this->year = now()->year;
        $this->notes = '';
        $this->recurring = false;
        $this->recurring_until_month = 12;
        $this->recurring_until_year = now()->year;
    }

    public function render()
    {
        $query = ClientAccount::whereNull('client_id')
            ->where('month', $this->filterMonth)
            ->where('year', $this->filterYear)
            ->active()
            ->with('user', 'team')
            ->orderBy('due_date', 'desc');

        $query = $this->applyTeamFilter($query);

        return view('livewire.page.clientes.accounts', [
            'accounts' => $query->paginate($this->perPage),
        ])
            ->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Gastos Operacionais',
            ]);
    }
}
