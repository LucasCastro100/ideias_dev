<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\MmnMembro;
use App\Models\MmnPagamento;

class MmnDashboard extends Component
{
    public $totalMembros = 0;
    public $membrosAtivos = 0;
    public $pagamentosPendentes = 0;
    public $totalPago = 0;

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $userId = auth()->id();

        $this->totalMembros = MmnMembro::where('user_id', $userId)->count();

        $this->membrosAtivos = MmnMembro::where('user_id', $userId)
            ->where('status', 'ativo')->count();

        $this->pagamentosPendentes = MmnPagamento::where('user_id', $userId)
            ->where('status', 'pendente')->count();

        $this->totalPago = MmnPagamento::where('user_id', $userId)
            ->where('status', 'aprovado')->sum('amount');
    }

    public function render()
    {
        return view('livewire.page.marketing-multinivel.dashboard')
            ->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Dashboard Marketing Multinível',
            ]);
    }
}
