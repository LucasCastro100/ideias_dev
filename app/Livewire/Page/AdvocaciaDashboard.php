<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\AdvocaciaCliente;
use App\Models\AdvocaciaProcesso;

class AdvocaciaDashboard extends Component
{
    public $totalClientes = 0;
    public $processosAtivos = 0;
    public $audienciasEsteMes = 0;
    public $totalHonorarios = 0;

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $userId = auth()->id();
        $now = now();

        $this->totalClientes = AdvocaciaCliente::where('user_id', $userId)->count();

        $this->processosAtivos = AdvocaciaProcesso::where('user_id', $userId)
            ->where('stage', '!=', 'encerrado')->count();

        $this->audienciasEsteMes = AdvocaciaProcesso::where('user_id', $userId)
            ->whereNotNull('hearing_date')
            ->whereMonth('hearing_date', $now->month)
            ->whereYear('hearing_date', $now->year)
            ->count();

        $this->totalHonorarios = AdvocaciaProcesso::where('user_id', $userId)->sum('fees');
    }

    public function render()
    {
        return view('livewire.page.gestao-advocacia.dashboard')
            ->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Dashboard Advocacia',
            ]);
    }
}
