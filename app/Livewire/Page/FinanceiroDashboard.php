<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\FinancialTransaction;
use App\Models\FinancialCategory;
use Illuminate\Support\Facades\DB;

class FinanceiroDashboard extends Component
{
    public $totalReceitas = 0;
    public $totalDespesas = 0;
    public $saldo = 0;
    public $pendingCount = 0;
    public $currentMonthReceitas = 0;
    public $currentMonthDespesas = 0;
    public $previousMonthReceitas = 0;
    public $previousMonthDespesas = 0;
    public $receitaChange = 0;
    public $receitaChangePercent = 0;
    public $despesaChange = 0;
    public $despesaChangePercent = 0;
    public $monthlyTrend = [];
    public $topCategories = [];
    public $topAccountTypes = [];
    public $recentTransactions;

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $userId = auth()->id();
        $now = now();
        $currentMonth = $now->month;
        $currentYear = $now->year;
        $prev = $now->copy()->subMonth();

        $this->totalReceitas = FinancialTransaction::where('user_id', $userId)
            ->where('paid', true)->where('value', '>', 0)->sum('value');

        $this->totalDespesas = FinancialTransaction::where('user_id', $userId)
            ->where('paid', true)->where('value', '<', 0)->sum('value');

        $this->saldo = $this->totalReceitas + $this->totalDespesas;

        $this->pendingCount = FinancialTransaction::where('user_id', $userId)
            ->where('paid', false)->count();

        $this->currentMonthReceitas = FinancialTransaction::where('user_id', $userId)
            ->where('paid', true)->where('value', '>', 0)
            ->where('month', $currentMonth)->where('year', $currentYear)
            ->sum('value');

        $this->currentMonthDespesas = FinancialTransaction::where('user_id', $userId)
            ->where('paid', true)->where('value', '<', 0)
            ->where('month', $currentMonth)->where('year', $currentYear)
            ->sum('value');

        $this->previousMonthReceitas = FinancialTransaction::where('user_id', $userId)
            ->where('paid', true)->where('value', '>', 0)
            ->where('month', $prev->month)->where('year', $prev->year)
            ->sum('value');

        $this->previousMonthDespesas = FinancialTransaction::where('user_id', $userId)
            ->where('paid', true)->where('value', '<', 0)
            ->where('month', $prev->month)->where('year', $prev->year)
            ->sum('value');

        $this->receitaChange = $this->currentMonthReceitas - $this->previousMonthReceitas;
        $this->receitaChangePercent = $this->previousMonthReceitas > 0
            ? round(($this->receitaChange / $this->previousMonthReceitas) * 100, 1) : 0;

        $this->despesaChange = $this->currentMonthDespesas - $this->previousMonthDespesas;
        $this->despesaChangePercent = $this->previousMonthDespesas > 0
            ? round(($this->despesaChange / abs($this->previousMonthDespesas)) * 100, 1) : 0;

        // Monthly trend (last 6 months)
        $trend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $receitas = FinancialTransaction::where('user_id', $userId)
                ->where('paid', true)->where('value', '>', 0)
                ->where('month', $date->month)->where('year', $date->year)
                ->sum('value');
            $despesas = FinancialTransaction::where('user_id', $userId)
                ->where('paid', true)->where('value', '<', 0)
                ->where('month', $date->month)->where('year', $date->year)
                ->sum('value');
            $trend[] = [
                'month' => $date->format('M/Y'),
                'receitas' => (float) $receitas,
                'despesas' => (float) abs($despesas),
            ];
        }
        $this->monthlyTrend = $trend;

        // Top categories by total value (expenses)
        $this->topCategories = FinancialTransaction::where('user_id', $userId)
            ->where('paid', true)->where('value', '<', 0)
            ->whereNotNull('category_id')
            ->select('category_id', DB::raw('SUM(ABS(value)) as total'))
            ->with('category')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn($t) => [
                'name' => $t->category?->name ?? 'Sem categoria',
                'value' => (float) $t->total,
            ])
            ->toArray();

        // Top account types by total value (expenses)
        $this->topAccountTypes = FinancialTransaction::where('user_id', $userId)
            ->where('paid', true)->where('value', '<', 0)
            ->whereNotNull('account_type_id')
            ->select('account_type_id', DB::raw('SUM(ABS(value)) as total'))
            ->with('accountType')
            ->groupBy('account_type_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn($t) => [
                'name' => $t->accountType?->name ?? 'Sem tipo',
                'value' => (float) $t->total,
            ])
            ->toArray();

        $this->recentTransactions = FinancialTransaction::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.page.financeiro.dashboard', [
            'monthlyTrendJson' => json_encode($this->monthlyTrend),
            'topCategoriesJson' => json_encode($this->topCategories),
            'topAccountTypesJson' => json_encode($this->topAccountTypes),
        ])->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Dashboard Financeiro',
            ]);
    }
}
