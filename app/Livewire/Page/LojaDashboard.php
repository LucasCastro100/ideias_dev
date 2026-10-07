<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\LojaProduto;
use App\Models\LojaPedido;

class LojaDashboard extends Component
{
    public $totalProdutos = 0;
    public $pedidosPendentes = 0;
    public $faturamentoMes = 0;
    public $produtosSemEstoque = 0;

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $userId = auth()->id();
        $now = now();

        $this->totalProdutos = LojaProduto::where('user_id', $userId)->count();

        $this->pedidosPendentes = LojaPedido::where('user_id', $userId)
            ->where('status', 'pendente')
            ->count();

        $this->faturamentoMes = LojaPedido::where('user_id', $userId)
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->sum('total');

        $this->produtosSemEstoque = LojaProduto::where('user_id', $userId)
            ->where('stock', 0)
            ->count();
    }

    public function render()
    {
        return view('livewire.page.loja-virtual.dashboard')
            ->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Dashboard Loja Virtual',
            ]);
    }
}
