<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\PizzariaProduto;
use App\Models\PizzariaPedido;

class PizzariaDashboard extends Component
{
    public $totalProdutos = 0;
    public $pedidosHoje = 0;
    public $pedidosEmPreparo = 0;
    public $faturamentoDia = 0;

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $userId = auth()->id();
        $now = now();

        $this->totalProdutos = PizzariaProduto::where('user_id', $userId)->count();

        $this->pedidosHoje = PizzariaPedido::where('user_id', $userId)
            ->whereDate('created_at', $now->toDateString())
            ->count();

        $this->pedidosEmPreparo = PizzariaPedido::where('user_id', $userId)
            ->where('status', 'preparo')
            ->count();

        $this->faturamentoDia = PizzariaPedido::where('user_id', $userId)
            ->whereDate('created_at', $now->toDateString())
            ->sum('total');
    }

    public function render()
    {
        return view('livewire.page.pizzaria-delivery.dashboard')
            ->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Dashboard Pizzaria',
            ]);
    }
}
