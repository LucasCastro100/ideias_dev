<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\SuporteTicket;
use App\Models\SuporteDepartamento;

class SuporteDashboard extends Component
{
    public $totalTickets = 0;
    public $ticketsAbertos = 0;
    public $ticketsResolvidosMes = 0;
    public $totalDepartamentos = 0;

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $userId = auth()->id();
        $now = now();

        $this->totalTickets = SuporteTicket::where('user_id', $userId)->count();

        $this->ticketsAbertos = SuporteTicket::where('user_id', $userId)
            ->where('status', 'aberto')
            ->count();

        $this->ticketsResolvidosMes = SuporteTicket::where('user_id', $userId)
            ->where('status', 'resolvido')
            ->whereMonth('updated_at', $now->month)
            ->whereYear('updated_at', $now->year)
            ->count();

        $this->totalDepartamentos = SuporteDepartamento::where('user_id', $userId)->count();
    }

    public function render()
    {
        return view('livewire.page.central-suporte.dashboard')
            ->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Dashboard Central de Suporte',
            ]);
    }
}
