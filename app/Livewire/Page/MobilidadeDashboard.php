<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\MobilidadeMotorista;
use App\Models\MobilidadeCorrida;

class MobilidadeDashboard extends Component
{
    public $totalDrivers = 0;
    public $activeDrivers = 0;
    public $ridesToday = 0;
    public $monthlyRevenue = 0;

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $userId = auth()->id();
        $now = now();

        $this->totalDrivers = MobilidadeMotorista::where('user_id', $userId)->count();

        $this->activeDrivers = MobilidadeMotorista::where('user_id', $userId)
            ->where('status', 'ativo')->count();

        $this->ridesToday = MobilidadeCorrida::where('user_id', $userId)
            ->whereDate('requested_at', $now->toDateString())
            ->count();

        $this->monthlyRevenue = MobilidadeCorrida::where('user_id', $userId)
            ->where('status', 'concluida')
            ->whereMonth('requested_at', $now->month)
            ->whereYear('requested_at', $now->year)
            ->sum('amount');
    }

    public function render()
    {
        return view('livewire.page.corridas-mobilidade.dashboard')
            ->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Dashboard Mobilidade',
            ]);
    }
}
