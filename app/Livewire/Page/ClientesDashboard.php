<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\Client;
use App\Models\ClientAccount;
use Illuminate\Support\Facades\DB;

class ClientesDashboard extends Component
{
    public $totalClients = 0;
    public $monthlyClients = 0;
    public $expectedRevenue = 0;
    public $receivedRevenue = 0;
    public $totalCollected = 0;
    public $monthlyExpenses = 0;
    public $netTotal = 0;
    public $monthlyTrend = [];
    public $recentClients;

    public function mount()
    {
        $this->loadStats();
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

    public function loadStats()
    {
        $now = now();
        $month = $now->month;
        $year = $now->year;
        $lastDay = $now->copy()->endOfMonth()->toDateString();

        $clientQuery = $this->applyTeamFilter(Client::query());
        $receitaQuery = $this->applyTeamFilter(ClientAccount::whereNotNull('client_id'));
        $despesaQuery = $this->applyTeamFilter(ClientAccount::whereNull('client_id'));

        $this->totalClients = (clone $clientQuery)->count();

        $this->monthlyClients = (clone $clientQuery)
            ->where(function ($q) use ($year, $month) {
                $q->whereNull('deactivated_at')
                    ->orWhere('deactivated_at', '>=', "{$year}-{$month}-01");
            })
            ->where('created_at', '<=', $lastDay)
            ->count();

        $this->expectedRevenue = (clone $receitaQuery)
            ->where('month', $month)->where('year', $year)
            ->sum('value');

        $this->receivedRevenue = (clone $receitaQuery)
            ->where('month', $month)->where('year', $year)
            ->where('paid', true)
            ->sum('value');

        $this->totalCollected = (clone $receitaQuery)
            ->where('paid', true)
            ->sum('value');

        $this->monthlyExpenses = (clone $despesaQuery)
            ->where('paid', true)
            ->where('month', $month)->where('year', $year)
            ->sum('value');

        $this->netTotal = $this->receivedRevenue - $this->monthlyExpenses;

        $trend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $m = $date->month;
            $y = $date->year;

            $rq = clone $receitaQuery;
            $dq = clone $despesaQuery;

            $receitas = $rq->where('month', $m)->where('year', $y)->sum('value');
            $despesas = $dq->where('paid', true)->where('month', $m)->where('year', $y)->sum('value');

            $trend[] = [
                'month' => $date->format('M/Y'),
                'receitas' => (float) $receitas,
                'despesas' => (float) $despesas,
            ];
        }
        $this->monthlyTrend = $trend;

        $this->recentClients = $this->applyTeamFilter(
            Client::where('active', true)
                ->orderBy('created_at', 'desc')
                ->limit(5)
        )->get();
    }

    public function render()
    {
        return view('livewire.page.clientes.dashboard')
            ->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Dashboard Clientes',
            ]);
    }
}
