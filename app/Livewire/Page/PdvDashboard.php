<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\PdvProduto;
use App\Models\PdvVenda;

class PdvDashboard extends Component
{
    public $totalProdutos = 0;
    public $produtosEstoqueBaixo = 0;
    public $vendasHoje = 0;
    public $faturamentoMes = 0;

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $userId = auth()->id();
        $now = now();

        $this->totalProdutos = PdvProduto::where('user_id', $userId)->count();

        $this->produtosEstoqueBaixo = PdvProduto::where('user_id', $userId)
            ->where('stock', '<', 5)
            ->count();

        $this->vendasHoje = PdvVenda::where('user_id', $userId)
            ->whereDate('sold_at', $now->toDateString())
            ->count();

        $this->faturamentoMes = PdvVenda::where('user_id', $userId)
            ->whereMonth('sold_at', $now->month)
            ->whereYear('sold_at', $now->year)
            ->sum('total');
    }

    public function render()
    {
        return view('livewire.page.pdv-vendas.dashboard')
            ->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Dashboard PDV',
            ]);
    }
}
