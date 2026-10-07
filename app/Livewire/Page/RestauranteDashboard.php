<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\RestauranteMesa;
use App\Models\RestaurantePedido;

class RestauranteDashboard extends Component
{
    public $totalMesas = 0;
    public $mesasOcupadas = 0;
    public $pedidosAbertos = 0;
    public $faturamentoDia = 0;

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $userId = auth()->id();
        $now = now();

        $this->totalMesas = RestauranteMesa::where('user_id', $userId)->count();

        $this->mesasOcupadas = RestauranteMesa::where('user_id', $userId)
            ->where('status', 'ocupada')
            ->count();

        $this->pedidosAbertos = RestaurantePedido::where('user_id', $userId)
            ->where('status', 'aberto')
            ->count();

        $this->faturamentoDia = RestaurantePedido::where('user_id', $userId)
            ->whereDate('created_at', $now->toDateString())
            ->sum('total');
    }

    public function render()
    {
        return view('livewire.page.restaurante-mesas.dashboard')
            ->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Dashboard Restaurante',
            ]);
    }
}
