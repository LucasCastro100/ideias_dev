<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\OrdemServicoCliente;
use App\Models\OrdemServicoOrdem;
use App\Models\OrdemServicoLancamento;

class OrdemServicoDashboard extends Component
{
    public $totalClientes = 0;
    public $osAbertas = 0;
    public $osConcluidasMes = 0;
    public $totalAReceber = 0;

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $userId = auth()->id();
        $now = now();

        $this->totalClientes = OrdemServicoCliente::where('user_id', $userId)->count();

        $this->osAbertas = OrdemServicoOrdem::where('user_id', $userId)
            ->where('status', 'aberta')
            ->count();

        $this->osConcluidasMes = OrdemServicoOrdem::where('user_id', $userId)
            ->where('status', 'concluida')
            ->whereMonth('end_date', $now->month)
            ->whereYear('end_date', $now->year)
            ->count();

        $this->totalAReceber = OrdemServicoLancamento::where('user_id', $userId)
            ->where('paid', false)
            ->sum('amount');
    }

    public function render()
    {
        return view('livewire.page.ordem-servico.dashboard')
            ->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Dashboard Ordem de Serviço',
            ]);
    }
}
