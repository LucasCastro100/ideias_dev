<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\ErpProduto;
use App\Models\ErpVenda;

class ErpDashboard extends Component
{
    public $totalProdutos = 0;
    public $produtosEstoqueBaixo = 0;
    public $vendasMes = 0;
    public $faturamentoMes = 0;

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $userId = auth()->id();
        $now = now();

        $this->totalProdutos = ErpProduto::where('user_id', $userId)->count();

        $this->produtosEstoqueBaixo = ErpProduto::where('user_id', $userId)
            ->where('stock', '<', 5)
            ->count();

        $this->vendasMes = ErpVenda::where('user_id', $userId)
            ->whereMonth('sold_at', $now->month)
            ->whereYear('sold_at', $now->year)
            ->count();

        $this->faturamentoMes = ErpVenda::where('user_id', $userId)
            ->whereMonth('sold_at', $now->month)
            ->whereYear('sold_at', $now->year)
            ->sum('total');
    }

    public function render()
    {
        return view('livewire.page.controle-empresarial-nfe.dashboard')
            ->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Dashboard Controle Empresarial',
            ]);
    }
}
