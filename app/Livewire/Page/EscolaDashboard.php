<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\EscolaTurma;
use App\Models\EscolaAluno;
use App\Models\EscolaFatura;

class EscolaDashboard extends Component
{
    public $totalTurmas = 0;
    public $totalAlunosAtivos = 0;
    public $faturasPendentes = 0;
    public $totalRecebidoMes = 0;

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $userId = auth()->id();
        $now = now();

        $this->totalTurmas = EscolaTurma::where('user_id', $userId)->count();

        $this->totalAlunosAtivos = EscolaAluno::where('user_id', $userId)
            ->where('active', true)
            ->count();

        $this->faturasPendentes = EscolaFatura::where('user_id', $userId)
            ->where('paid', false)
            ->count();

        $this->totalRecebidoMes = EscolaFatura::where('user_id', $userId)
            ->where('paid', true)
            ->whereMonth('paid_date', $now->month)
            ->whereYear('paid_date', $now->year)
            ->sum('amount');
    }

    public function render()
    {
        return view('livewire.page.gestao-escolar.dashboard')
            ->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Dashboard Gestão Escolar',
            ]);
    }
}
